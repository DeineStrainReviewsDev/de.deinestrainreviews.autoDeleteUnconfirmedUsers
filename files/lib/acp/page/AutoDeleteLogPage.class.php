<?php

namespace wcf\acp\page;

use wcf\data\log\AutoDeleteLogList;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 * ACP page for displaying auto-delete logs.
 * 
 * @copyright 2025 DeineStrainReviews.de
 * @author DeineStrainReviews.de Development Team
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class AutoDeleteLogPage extends SortablePage {
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.log.autoDelete';
    
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canManageUser'];
    
    /**
     * @inheritDoc
     */
    public $objectListClassName = AutoDeleteLogList::class;
    
    /**
     * @inheritDoc
     */
    public $defaultSortField = 'executionTime';
    
    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'DESC';
    
    /**
     * @inheritDoc
     */
    public $validSortFields = ['executionTime', 'logID', 'userID'];
    
    /**
     * @inheritDoc
     */
    public function assignVariables() {
        parent::assignVariables();
    }
}

