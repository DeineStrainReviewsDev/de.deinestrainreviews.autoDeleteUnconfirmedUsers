<?php

namespace wcf\system\cronjob;

use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;

/**
 * Cronjob zur automatischen Löschung alter vollständiger Logs (PII).
 * Löscht nur vollständige Logs (mit userID), nicht die anonymen Zähl-Logs.
 * 
 * @copyright 2025 DeineStrainReviews.de
 * @author DeineStrainReviews.de Development Team
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class AutoDeleteLogCleanupCronjob extends AbstractCronjob {
    /**
     * Führt den Cronjob aus.
     */
    public function execute($data) {
        parent::execute($data);

        // Prüfe, ob vollständiges Logging aktiviert ist (Option wird als Konstante exportiert)
        $isAnonymous = (bool) AUTODELETE_LOG_ANONYMOUS;
        
        // Prüfe die Retention Period (Option wird als Konstante exportiert)
        $retentionPeriod = (int) AUTODELETE_LOG_RETENTION_PERIOD;

        // Führe Löschung NUR aus, wenn anonym == false (0) UND retentionPeriod > 0
        if ($isAnonymous || $retentionPeriod <= 0) {
            return; // Keine Löschung notwendig oder nicht konfiguriert
        }

        // Berechne den Cutoff-Timestamp (aktueller Zeitpunkt minus Retention Period in Tagen)
        $cutoffTime = TIME_NOW - ($retentionPeriod * 86400); // 86400 Sekunden = 1 Tag

        // Lösche alle vollständigen Logs, die älter als die Retention Period sind
        // Wichtig: userID IS NOT NULL, damit anonyme Zähl-Logs NICHT gelöscht werden
        $sql = "DELETE FROM wcf1_deinestrainreviews_auto_delete_unconfirmed_users_log 
                WHERE userID IS NOT NULL 
                AND executionTime < ?";
        
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$cutoffTime]);
    }
}

