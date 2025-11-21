<?php

namespace wcf\data\resentActivationEmailLog;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of resent activation email log entries.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * @method ResentActivationEmailLog current()
 * @method ResentActivationEmailLog[] getObjects()
 * @method ResentActivationEmailLog|null search($objectID)
 * @property ResentActivationEmailLog[] $objects
 */
class ResentActivationEmailLogList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = ResentActivationEmailLog::class;
}

