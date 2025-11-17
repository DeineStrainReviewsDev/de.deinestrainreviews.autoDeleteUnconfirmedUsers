<?php

namespace wcf\system\cronjob;

use wcf\data\user\User;
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
        $sql = "SELECT u.userID, u.username, u.email
                FROM wcf1_user u
                INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
                WHERE u.emailConfirmed IS NOT NULL
                AND u.registrationDate < ?
                GROUP BY u.userID, u.username, u.email
                HAVING COUNT(DISTINCT ug.groupID) <= 2 
                AND SUM(ug.groupID NOT IN (?, ?)) = 0
                LIMIT ?";

        // Statement vorbereiten und mit Parametern ausführen
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$timeLimit, 1, 2, $limit]);
        $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($users)) {
            return; // Keine passenden Benutzer gefunden
        }

        // User-IDs für Löschung extrahieren
        $userIDs = array_column($users, 'userID');

        // Logging-Modus prüfen (Option wird als Konstante exportiert)
        $isAnonymous = (bool) AUTODELETE_LOG_ANONYMOUS;

        // Benutzer über UserAction löschen
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();

        // Logging durchführen
        $this->logDeletions($users, $isAnonymous);
    }

    /**
     * Protokolliert die gelöschten Benutzer entsprechend dem gewählten Modus.
     * 
     * @param array $users Array mit userID, username, email
     * @param bool $isAnonymous true für anonymes Logging (nur Zählung), false für vollständiges Logging
     */
    protected function logDeletions(array $users, bool $isAnonymous) {
        $db = WCF::getDB();
        $executionTime = TIME_NOW;

        if ($isAnonymous) {
            // Anonym-Modus: Ein einziger Log-Eintrag mit der Anzahl
            $sql = "INSERT INTO wcf1_deinestrainreviews_auto_delete_unconfirmed_users_log 
                    (executionTime, usersDeletedCount) 
                    VALUES (?, ?)";
            $statement = $db->prepareStatement($sql);
            $statement->execute([$executionTime, count($users)]);
        } else {
            // Vollständig-Modus: Ein Log-Eintrag pro Benutzer
            $sql = "INSERT INTO wcf1_deinestrainreviews_auto_delete_unconfirmed_users_log 
                    (executionTime, userID, username, email) 
                    VALUES (?, ?, ?, ?)";
            $statement = $db->prepareStatement($sql);
            
            foreach ($users as $user) {
                $statement->execute([
                    $executionTime,
                    $user['userID'],
                    $user['username'],
                    $user['email']
                ]);
            }
        }
    }
}
