<?php

namespace wcf\system\clipboard\action;

use wcf\data\clipboard\action\ClipboardAction;
use wcf\data\legacyAccountLog\LegacyAccountLogAction;
use wcf\system\WCF;

/**
 * Clipboard action implementation for legacy accounts.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class LegacyAccountClipboardAction extends AbstractClipboardAction {
	/**
	 * @inheritDoc
	 */
	protected $actionClassActions = ['delete'];

	/**
	 * @inheritDoc
	 */
	protected $supportedActions = ['delete'];

	/**
	 * @inheritDoc
	 */
	public function execute(array $objects, ClipboardAction $action)
	{
		$item = parent::execute($objects, $action);

		if ($item === null) {
			return;
		}

		switch ($action->actionName) {
			case 'delete':
				$item->addInternalData(
					'confirmMessage',
					WCF::getLanguage()->getDynamicVariable(
						'wcf.clipboard.item.de.deinestrainreviews.legacyAccount.delete.confirmMessage',
						[
							'count' => $item->getCount(),
						]
					)
				);
				break;
		}

		return $item;
	}

	/**
	 * @inheritDoc
	 */
	public function getClassName()
	{
		return LegacyAccountLogAction::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getTypeName()
	{
		return 'de.deinestrainreviews.legacyAccount';
	}

	/**
	 * Validates the delete action.
	 *
	 * @return int[]
	 */
	public function validateDelete()
	{
		// Check permissions
		if (!WCF::getSession()->getPermission('admin.user.canViewDeletedUnconfirmedUsersLog')) {
			return [];
		}

		// Return all user IDs (from the legacy account log entries)
		return \array_keys($this->objects);
	}
}
