<?php

namespace wcf\data\deletedUnconfirmedUsersLog;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit deleted unconfirmed user logs.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * @method static DeletedUnconfirmedUserLog create(array $parameters = [])
 * @method DeletedUnconfirmedUserLog getDecoratedObject()
 * @mixin DeletedUnconfirmedUserLog
 */
class DeletedUnconfirmedUserLogEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = DeletedUnconfirmedUserLog::class;
}

