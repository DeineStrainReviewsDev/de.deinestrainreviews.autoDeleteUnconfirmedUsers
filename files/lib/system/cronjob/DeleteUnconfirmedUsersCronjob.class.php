<?php

namespace wcf\system\cronjob;

use wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogEditor;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\group\UserGroup;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\email\Email;
use wcf\system\email\mime\MimePartFacade;
use wcf\system\email\mime\RecipientAwareTextMimePart;
use wcf\system\email\UserMailbox;
use wcf\system\request\LinkHandler;
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
                'username' => $this->anonymizeUsername($user['username']),
                'email' => $this->anonymizeEmail($user['email']),
                'registrationDate' => $user['registrationDate'],
                'deletionDate' => $deletionDate
            ]);
        }

        // Delete users via UserAction
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();

        // Send notification to administrators if enabled
        if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL) {
            // Prepare anonymized user list for email
            $anonymizedUsers = [];
            foreach ($users as $user) {
                $anonymizedUsers[] = [
                    'username' => $this->anonymizeUsername($user['username']),
                    'email' => $this->anonymizeEmail($user['email']),
                    'registrationDate' => $user['registrationDate'],
                    'deletionDate' => $deletionDate
                ];
            }
            $this->notifyAdministrators(count($users), $anonymizedUsers);
        }
    }

    /**
     * Sends email notification to administrators about deleted users.
     * 
     * @param int $deletedCount Number of deleted users
     * @param array $anonymizedUsers Array of anonymized user data (username, email, registrationDate)
     */
    protected function notifyAdministrators($deletedCount, $anonymizedUsers = []) {
        if ($deletedCount == 0) {
            return;
        }

        // Get all administrators using UserGroup::isAdminGroup()
        $adminGroupIDs = [];
        // Get all groups (not excluding OWNER groups) and check if they are admin groups
        foreach (UserGroup::getGroupsByType() as $group) {
            if ($group->isAdminGroup()) {
                $adminGroupIDs[] = $group->groupID;
            }
        }
        
        if (empty($adminGroupIDs)) {
            return;
        }
        
        // Get all users in admin groups
        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add('ug.groupID IN (?)', [$adminGroupIDs]);
        $conditions->add('u.userID <> ?', [0]);
        
        $sql = "SELECT DISTINCT u.userID, u.email, u.username
                FROM wcf1_user_to_group ug
                INNER JOIN wcf1_user u ON u.userID = ug.userID
                " . $conditions;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditions->getParameters());
        $administrators = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($administrators)) {
            return;
        }

        // Generate log page URL
        try {
            $logPageUrl = LinkHandler::getInstance()->getLink('DeletedUnconfirmedUsersLog', [], '', true);
        } catch (\Exception $e) {
            $logPageUrl = '';
        }
        
        // Format user list for email
        $userListHtml = $this->formatUserListForEmail($anonymizedUsers);
        
        // Send email to each administrator
        foreach ($administrators as $admin) {
            try {
                $subject = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedUnconfirmedUsers.subject', ['count' => $deletedCount]);
                
                $message = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedUnconfirmedUsers.message', [
                    'count' => $deletedCount,
                    'username' => $admin['username'],
                    'logPageUrl' => $logPageUrl,
                    'userList' => $userListHtml
                ]);
                
                $adminUser = new User($admin['userID']);
                $email = new Email();
                $email->addRecipient(new UserMailbox($adminUser));
                $email->setSubject($subject);
                
                $email->setBody(new MimePartFacade([
                    new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $message),
                    new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', strip_tags($message))
                ]));
                
                $email->send();
            } catch (\Exception $e) {
                // Log error but continue with other administrators
                error_log("Failed to send email notification to administrator {$admin['userID']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Anonymizes a username for GDPR compliance.
     * Shows first 2 characters, masks the middle, and shows last 2-4 characters.
     * 
     * @param string $username Original username
     * @return string Anonymized username
     */
    protected function anonymizeUsername($username) {
        $length = mb_strlen($username);
        
        if ($length <= 4) {
            // Very short usernames: fully anonymize
            return str_repeat('*', $length);
        } elseif ($length <= 8) {
            // Medium usernames: show first 2, mask middle, show last 2
            return mb_substr($username, 0, 2) . '***' . mb_substr($username, -2);
        } else {
            // Long usernames: show first 2, mask middle, show last 4
            return mb_substr($username, 0, 2) . '***' . mb_substr($username, -4);
        }
    }

    /**
     * Anonymizes an email address for GDPR compliance.
     * Shows first character of local part, masks the rest, and partially masks domain.
     * 
     * @param string $email Original email address
     * @return string Anonymized email address
     */
    protected function anonymizeEmail($email) {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '***@***.***';
        }
        
        list($localPart, $domain) = explode('@', $email, 2);
        
        // Anonymize local part: show first character, mask the rest
        $localLength = mb_strlen($localPart);
        if ($localLength <= 1) {
            $anonymizedLocal = '*';
        } else {
            $anonymizedLocal = mb_substr($localPart, 0, 1) . '***';
        }
        
        // Anonymize domain: show first 2 characters, mask middle
        $domainParts = explode('.', $domain);
        $tld = array_pop($domainParts);
        $domainName = implode('.', $domainParts);
        
        $domainLength = mb_strlen($domainName);
        if ($domainLength <= 2) {
            $anonymizedDomain = '***';
        } else {
            $anonymizedDomain = mb_substr($domainName, 0, 2) . '***';
        }
        
        // Anonymize TLD: show first 1-2 characters, mask the rest
        $tldLength = mb_strlen($tld);
        if ($tldLength <= 1) {
            $anonymizedTld = '*';
        } elseif ($tldLength <= 3) {
            $anonymizedTld = mb_substr($tld, 0, 1) . '***';
        } else {
            $anonymizedTld = mb_substr($tld, 0, 2) . '***';
        }
        
        return $anonymizedLocal . '@' . $anonymizedDomain . '.' . $anonymizedTld;
    }

    /**
     * Formats the anonymized user list as HTML table for email notification.
     * 
     * @param array $anonymizedUsers Array of anonymized user data
     * @return string HTML formatted user list
     */
    protected function formatUserListForEmail($anonymizedUsers) {
        if (empty($anonymizedUsers)) {
            return '';
        }

        $html = '<table style="border-collapse: collapse; width: 100%; margin: 20px 0;">';
        $html .= '<thead><tr style="background-color: #f0f0f0; border-bottom: 2px solid #ddd;">';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.username') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.email') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.registrationDate') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.deletionDate') . '</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($anonymizedUsers as $user) {
            $registrationDate = date('Y-m-d H:i:s', $user['registrationDate']);
            $deletionDate = date('Y-m-d H:i:s', $user['deletionDate']);
            $html .= '<tr style="border-bottom: 1px solid #ddd;">';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($registrationDate, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deletionDate, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }
}
