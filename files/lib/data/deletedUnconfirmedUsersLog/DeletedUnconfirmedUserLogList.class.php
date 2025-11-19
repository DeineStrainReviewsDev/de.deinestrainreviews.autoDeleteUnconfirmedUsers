<?php

namespace wcf\data\deletedUnconfirmedUsersLog;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of deleted unconfirmed user log entries.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @package de.deinestrainreviews.autoDeleteUnconfirmedUsers
 * 
 * @method DeletedUnconfirmedUserLog current()
 * @method DeletedUnconfirmedUserLog[] getObjects()
 * @method DeletedUnconfirmedUserLog|null search($objectID)
 */
class DeletedUnconfirmedUserLogList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = DeletedUnconfirmedUserLog::class;
}

