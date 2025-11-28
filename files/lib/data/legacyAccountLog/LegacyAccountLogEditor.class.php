<?php

namespace wcf\data\legacyAccountLog;

use wcf\data\DatabaseObjectEditor;

/**
 * Editor for legacy account log entries.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @method static LegacyAccountLog create(array $parameters = [])
 * @method LegacyAccountLog getDecoratedObject()
 * @mixin LegacyAccountLog
 */
class LegacyAccountLogEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = LegacyAccountLog::class;
}

