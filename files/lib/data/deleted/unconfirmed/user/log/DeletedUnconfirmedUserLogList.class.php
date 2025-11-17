<?php

namespace wcf\data\deleted\unconfirmed\user\log;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of deleted unconfirmed user log entries.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @package de.deinestrainreviews.autoDeleteUnconfirmedUsers
 */
class DeletedUnconfirmedUserLogList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = DeletedUnconfirmedUserLog::class;
}

