<?php

namespace wcf\data\deleted\unconfirmed\user\log;

use wcf\data\DatabaseObject;

/**
 * Represents a log entry for a deleted unconfirmed user.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @package de.deinestrainreviews.autoDeleteUnconfirmedUsers
 */
class DeletedUnconfirmedUserLog extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'deleted_unconfirmed_user_log';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'logID';
}

