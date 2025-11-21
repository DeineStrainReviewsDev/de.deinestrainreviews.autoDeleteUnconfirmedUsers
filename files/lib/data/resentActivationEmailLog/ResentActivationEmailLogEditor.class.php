<?php

namespace wcf\data\resentActivationEmailLog;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit resent activation email log entries.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * @method static ResentActivationEmailLog create(array $parameters = [])
 * @method ResentActivationEmailLog getDecoratedObject()
 * @mixin ResentActivationEmailLog
 */
class ResentActivationEmailLogEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = ResentActivationEmailLog::class;
}

