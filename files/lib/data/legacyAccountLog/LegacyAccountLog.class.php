<?php

namespace wcf\data\legacyAccountLog;

use wcf\data\DatabaseObject;

/**
 * Represents a legacy account log entry.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @property-read int $logID
 * @property-read int $userID
 * @property-read string $username
 * @property-read string $email
 * @property-read int $registrationDate
 * @property-read int $detectionDate
 * @property-read int|null $deletionDate
 * @property-read string $status
 */
class LegacyAccountLog extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'legacy_account_log';

	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'logID';
}

