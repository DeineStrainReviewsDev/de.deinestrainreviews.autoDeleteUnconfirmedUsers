<?php

namespace wcf\data\legacyAccountLog;

use wcf\data\DatabaseObjectList;

/**
 * List of legacy account log entries.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @method LegacyAccountLog current()
 * @method LegacyAccountLog[] getObjects()
 * @method LegacyAccountLog|null search($objectID)
 * @property LegacyAccountLog[] $objects
 */
class LegacyAccountLogList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = LegacyAccountLog::class;
}

