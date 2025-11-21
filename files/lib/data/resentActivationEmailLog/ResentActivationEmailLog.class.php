<?php

namespace wcf\data\resentActivationEmailLog;

use wcf\data\DatabaseObject;

/**
 * Represents a log entry for a resent activation email.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class ResentActivationEmailLog extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'resent_activation_email_log';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'logID';
}

