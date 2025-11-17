<?php

namespace wcf\acp\page;

use wcf\acp\page\AbstractAcpPage;
use wcf\data\deleted\unconfirmed\user\log\DeletedUnconfirmedUserLogList;
use wcf\system\WCF;

/**
 * Shows the log of deleted unconfirmed users.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @package de.deinestrainreviews.autoDeleteUnconfirmedUsers
 */
class DeletedUnconfirmedUsersLogPage extends AbstractAcpPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.deletedUnconfirmedUsersLog';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.user.canManageUser'];
	
	/**
	 * List of log entries
	 * 
	 * @var DeletedUnconfirmedUserLogList
	 */
	public $logList;
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		$this->logList = new DeletedUnconfirmedUserLogList();
		$this->logList->sqlOrderBy = 'deletionDate DESC';
		$this->logList->readObjects();
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'logEntries' => $this->logList->getObjects()
		]);
	}
}

