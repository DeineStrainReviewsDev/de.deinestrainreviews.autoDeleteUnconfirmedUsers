<?php

namespace wcf\data\log;

use wcf\data\DatabaseObject;

/**
 * Represents a log entry for auto-deleted unconfirmed users.
 * 
 * @property-read int $logID
 * @property-read int $executionTime
 * @property-read int|null $usersDeletedCount
 * @property-read int|null $userID
 * @property-read string|null $username
 * @property-read string|null $email
 * 
 * @copyright 2025 DeineStrainReviews.de
 * @author DeineStrainReviews.de Development Team
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class AutoDeleteLog extends DatabaseObject {
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'deinestrainreviews_auto_delete_unconfirmed_users_log';
    
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'logID';
}

