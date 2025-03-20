<?php

namespace wcf\system\cronjob;

use wcf\data\user\UserAction;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;

/**
 * Cronjob zur automatischen Löschung unbestätigter Benutzer.
 * @copyright 2025 DeineStrainReviews.de
 * @author DeineStrainReviews.de Development Team
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class DeleteUnconfirmedUsersCronjob extends AbstractCronjob {
    /**
     * Führt den Cronjob aus.
     */
    public function execute($data) {
        parent::execute($data);

        // Anzahl der Tage aus der Option abrufen (als Konstante)
        $days = (int) AUTO_DELETE_UNCONFIRMED_USERS_DAYS;

        // Maximale Anzahl der zu löschenden Benutzer abrufen (als Konstante)
        $limit = (int) AUTO_DELETE_UNCONFIRMED_USERS_LIMIT;

        $timeLimit = TIME_NOW - ($days * 86400); // 86400 Sekunden = 1 Tag

        // Benutzer-IDs abrufen, die unbestätigt sind und NUR in den Gruppen 1 (Jeder) und 2 (Gäste) sind
        $sql = "SELECT u.userID
                FROM wcf1_user u
                INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
                WHERE u.emailConfirmed IS NOT NULL
                AND u.registrationDate < ?
                GROUP BY u.userID
                HAVING COUNT(DISTINCT ug.groupID) <= 2 
                AND SUM(ug.groupID NOT IN (?, ?)) = 0";

        // Statement mit dem Limit als zweiten Parameter vorbereiten
        $statement = WCF::getDB()->prepare($sql, $limit);
        $statement->execute([$timeLimit, 1, 2]);
        $userIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($userIDs)) {
            return; // Keine passenden Benutzer gefunden
        }

        // Benutzer über UserAction löschen
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();
    }
}
