<?php

namespace wcf\acp\action;

use wcf\action\AbstractAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\user\LegacyAccountService;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Handles manual deletion of legacy accounts from ACP.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeinestrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class LegacyAccountDeleteAction extends AbstractAction {
	/**
	 * User IDs to delete
	 * @var int[]
	 */
	public $userIDs = [];

	/**
	 * @inheritDoc
	 */
	public function readParameters()
	{
		parent::readParameters();

		if (isset($_POST['userIDs']) && \is_array($_POST['userIDs'])) {
			$this->userIDs = \array_map('intval', $_POST['userIDs']);
		} elseif (isset($_GET['userID'])) {
			$this->userIDs = [(int)$_GET['userID']];
		}

		if (empty($this->userIDs)) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		parent::execute();

		// Check permission
		WCF::getSession()->checkPermissions(['admin.user.canViewDeletedUnconfirmedUsersLog']);

		// Delete users
		$result = LegacyAccountService::manualDelete($this->userIDs);

		$this->executed();

		// Set success message
		if ($result['success'] > 0) {
			WCF::getSession()->register('legacyAccountDeleteSuccess', $result['success']);
		}
		if ($result['failed'] > 0) {
			WCF::getSession()->register('legacyAccountDeleteFailed', $result['failed']);
		}

		// Redirect back to log page
		HeaderUtil::redirect(\wcf\system\request\LinkHandler::getInstance()->getLink('LegacyAccountLog'));
		exit;
	}
}

