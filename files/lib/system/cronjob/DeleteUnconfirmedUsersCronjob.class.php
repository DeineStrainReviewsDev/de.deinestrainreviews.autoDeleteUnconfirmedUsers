<?php

namespace wcf\system\cronjob;

use wcf\data\user\UserAction;
use wcf\system\cronjob\AbstractCronjob;
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

        // Retrieve user IDs that are unconfirmed and ONLY in groups 1 (Everyone) and 2 (Guests)
        // Note: In WoltLab, emailConfirmed is NOT NULL for unconfirmed users (contains hash/value),
        // while confirmed users have emailConfirmed = NULL
        $sql = "SELECT u.userID
                FROM wcf1_user u
                INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
                WHERE u.emailConfirmed IS NOT NULL
                AND u.registrationDate < ?
                GROUP BY u.userID
                HAVING COUNT(DISTINCT ug.groupID) <= 2 
                AND SUM(ug.groupID NOT IN (?, ?)) = 0";

        // Prepare statement with limit as second parameter
        $statement = WCF::getDB()->prepare($sql, $limit);
        $statement->execute([$timeLimit, 1, 2]);
        $userIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($userIDs)) {
            return; // No matching users found
        }

        // Delete users via UserAction
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();
    }
}
