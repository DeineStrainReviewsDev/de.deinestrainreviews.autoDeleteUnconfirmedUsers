<?php

namespace wcf\acp\page;

use wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogList;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 * Shows the log of deleted unconfirmed users.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @package de.deinestrainreviews.autoDeleteUnconfirmedUsers
 * 
 * @property DeletedUnconfirmedUserLogList $objectList
 */
class DeletedUnconfirmedUsersLogPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.deletedUnconfirmedUsersLog';
	
	/**
	 * @inheritDoc
	 * Permission to view the deleted unconfirmed users log.
	 */
	public $neededPermissions = ['admin.user.canViewDeletedUnconfirmedUsersLog'];
	
	/**
	 * @inheritDoc
	 */
	public $itemsPerPage = 100;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'logID';
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['logID', 'username', 'email', 'registrationDate', 'deletionDate'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = DeletedUnconfirmedUserLogList::class;
	
	/**
	 * @inheritDoc
	 */
	public $templateName = 'deletedUnconfirmedUsersLog';
}

