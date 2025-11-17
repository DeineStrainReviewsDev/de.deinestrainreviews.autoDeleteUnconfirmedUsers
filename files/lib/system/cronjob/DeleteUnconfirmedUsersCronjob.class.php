<?php

namespace wcf\system\cronjob;

use wcf\data\deleted\unconfirmed\user\log\DeletedUnconfirmedUserLogEditor;
use wcf\data\user\UserAction;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\email\Email;
use wcf\system\email\mime\MimePartFacade;
use wcf\system\email\mime\RecipientAwareTextMimePart;
use wcf\system\email\UserMailbox;
use wcf\system\WCF;

/**
 * Cronjob for automatic deletion of unconfirmed users.
 * 
 * This cronjob runs daily and deletes users who have not confirmed their email
 * within the configured number of days. Only users who are exclusively in the
 * default groups "Everyone" (ID 1) and "Guests" (ID 2) are deleted.
 * 
 * @copyright 2025 DeineStrainReviews.de
 * @author DeineStrainReviews.de Development Team
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class DeleteUnconfirmedUsersCronjob extends AbstractCronjob {
    /**
     * Executes the cronjob.
     * 
     * Retrieves unconfirmed users older than the configured number of days,
     * filters them by group membership, and deletes them (up to the configured limit).
     * 
     * @param array $data Cronjob data
     */
    public function execute($data) {
        parent::execute($data);

        // Get the number of days from the option (as constant)
        $days = (int) AUTO_DELETE_UNCONFIRMED_USERS_DAYS;

        // Get the maximum number of users to delete per execution (as constant)
        $limit = (int) AUTO_DELETE_UNCONFIRMED_USERS_LIMIT;

        // Calculate the timestamp threshold (86400 seconds = 1 day)
        $timeLimit = TIME_NOW - ($days * 86400);

        // Retrieve user data that are unconfirmed and ONLY in groups 1 (Everyone) and 2 (Guests)
        // Note: In WoltLab, emailConfirmed is NOT NULL for unconfirmed users (contains hash/value),
        // while confirmed users have emailConfirmed = NULL
        $sql = "SELECT u.userID, u.username, u.email, u.registrationDate
                FROM wcf1_user u
                INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
                WHERE u.emailConfirmed IS NOT NULL
                AND u.registrationDate < ?
                GROUP BY u.userID, u.username, u.email, u.registrationDate
                HAVING COUNT(DISTINCT ug.groupID) <= 2 
                AND SUM(ug.groupID NOT IN (?, ?)) = 0
                LIMIT ?";

        // Prepare statement with limit
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$timeLimit, 1, 2, $limit]);
        $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($users)) {
            return; // No matching users found
        }

        // Extract user IDs for deletion
        $userIDs = array_column($users, 'userID');

        // Create log entries before deletion
        $deletionDate = TIME_NOW;
        foreach ($users as $user) {
            DeletedUnconfirmedUserLogEditor::create([
                'userID' => $user['userID'],
                'username' => $user['username'],
                'email' => $user['email'],
                'registrationDate' => $user['registrationDate'],
                'deletionDate' => $deletionDate
            ]);
        }

        // Delete users via UserAction
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();

        // Send notification to administrators
        $this->notifyAdministrators(count($users));
    }

    /**
     * Sends email notification to administrators about deleted users.
     * 
     * @param int $deletedCount Number of deleted users
     */
    protected function notifyAdministrators($deletedCount) {
        if ($deletedCount == 0) {
            return;
        }

        // Get all administrators (group ID 4 is typically administrators)
        $sql = "SELECT DISTINCT u.userID, u.email, u.username
                FROM wcf1_user_to_group ug
                INNER JOIN wcf1_user u ON u.userID = ug.userID
                WHERE ug.groupID = ? AND u.userID <> ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([4, 0]);
        $administrators = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($administrators)) {
            return;
        }

        // Send email to each administrator
        foreach ($administrators as $admin) {
            $email = new Email();
            $email->addRecipient(new UserMailbox($admin['userID']));
            $email->setSubject(WCF::getLanguage()->get('wcf.acp.notification.deletedUnconfirmedUsers.subject', ['count' => $deletedCount]));
            
            $message = WCF::getLanguage()->get('wcf.acp.notification.deletedUnconfirmedUsers.message', [
                'count' => $deletedCount,
                'username' => $admin['username']
            ]);
            
            $email->setBody(new MimePartFacade([
                new RecipientAwareTextMimePart('text/html', 'email_html', $message),
                new RecipientAwareTextMimePart('text/plain', 'email_plain', strip_tags($message))
            ]));
            
            $email->send();
        }
    }
}
