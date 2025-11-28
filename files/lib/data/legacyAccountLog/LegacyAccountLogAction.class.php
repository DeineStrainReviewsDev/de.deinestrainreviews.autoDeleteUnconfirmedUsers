<?php

namespace wcf\data\legacyAccountLog;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\user\LegacyAccountService;
use wcf\system\WCF;

/**
 * Executes legacy account log related actions.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @method LegacyAccountLog create()
 * @method LegacyAccountLogEditor[] getObjects()
 * @method LegacyAccountLogEditor getSingleObject()
 */
class LegacyAccountLogAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.user.canViewDeletedUnconfirmedUsersLog'];

	/**
	 * @inheritDoc
	 */
	protected $allowGuestAccess = ['validateDelete'];

	/**
	 * Validates the delete action.
	 */
	public function validateDelete()
	{
		WCF::getSession()->checkPermissions($this->permissionsDelete);

		// Read and validate user IDs
		$this->readIntegerArray('objectIDs', true);

		if (empty($this->objectIDs)) {
			throw new \wcf\system\exception\UserInputException('objectIDs');
		}
	}

	/**
	 * Deletes legacy accounts (users) based on their log IDs.
	 */
	public function delete()
	{
		if (empty($this->objectIDs)) {
			return 0;
		}

		// Load the log entries to get the user IDs
		$logList = new LegacyAccountLogList();
		$logList->getConditionBuilder()->add('legacy_account_log.logID IN (?)', [$this->objectIDs]);
		$logList->readObjects();

		// Extract user IDs from log entries
		$userIDs = [];
		foreach ($logList->getObjects() as $logEntry) {
			$userIDs[] = $logEntry->userID;
		}

		if (empty($userIDs)) {
			return 0;
		}

		// Delete users using the LegacyAccountService
		$result = LegacyAccountService::manualDelete($userIDs);

		// Set session variables for success/error messages
		if ($result['success'] > 0) {
			WCF::getSession()->register('legacyAccountDeleteSuccess', $result['success']);
		}
		if ($result['failed'] > 0) {
			WCF::getSession()->register('legacyAccountDeleteFailed', $result['failed']);
		}

		// Unmark deleted items from clipboard (using logIDs)
		$this->unmarkItems();

		return $result['success'];
	}

	/**
	 * Validates the unmarkAll action.
	 */
	public function validateUnmarkAll()
	{
		// Does nothing, unmarking is always allowed
	}

	/**
	 * Unmarks all legacy accounts from clipboard.
	 */
	public function unmarkAll()
	{
		ClipboardHandler::getInstance()->removeItems(
			ClipboardHandler::getInstance()->getObjectTypeID('de.deinestrainreviews.legacyAccount')
		);
	}

	/**
	 * Unmarks items from clipboard.
	 *
	 * @param int[] $logIDs
	 */
	protected function unmarkItems(array $logIDs = [])
	{
		if (empty($logIDs)) {
			$logIDs = $this->objectIDs;
		}

		if (!empty($logIDs)) {
			ClipboardHandler::getInstance()->unmark(
				$logIDs,
				ClipboardHandler::getInstance()->getObjectTypeID('de.deinestrainreviews.legacyAccount')
			);
		}
	}
}
