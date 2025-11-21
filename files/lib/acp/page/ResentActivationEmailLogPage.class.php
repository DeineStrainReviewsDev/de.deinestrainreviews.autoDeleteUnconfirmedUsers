<?php

namespace wcf\acp\page;

use wcf\data\resentActivationEmailLog\ResentActivationEmailLogList;
use wcf\page\SortablePage;

/**
 * Shows the log of resent activation emails.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * 
 * @property ResentActivationEmailLogList $objectList
 */
class ResentActivationEmailLogPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.resentActivationEmailLog';
	
	/**
	 * @inheritDoc
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
	public $validSortFields = ['logID', 'userID', 'registrationDate', 'resendEmailDate'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = ResentActivationEmailLogList::class;
	
	/**
	 * @inheritDoc
	 */
	public $templateName = 'resentActivationEmailLog';
}

