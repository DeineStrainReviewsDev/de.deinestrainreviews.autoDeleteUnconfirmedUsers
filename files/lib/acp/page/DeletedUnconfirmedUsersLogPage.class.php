<?php

namespace wcf\acp\page;

use wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the log of deleted unconfirmed users.
 * * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * * @property DeletedUnconfirmedUserLogList $objectList
 */
class DeletedUnconfirmedUsersLogPage extends SortablePage {
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.deletedUnconfirmedUsersLog';
    
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canViewDeletedUnconfirmedUsersLog'];
    
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 30;
    
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
    public $validSortFields = ['logID', 'userID', 'username', 'email', 'registrationDate', 'deletionDate', 'deletionType'];
    
    /**
     * @inheritDoc
     */
    public $objectListClassName = DeletedUnconfirmedUserLogList::class;
    
    /**
     * @inheritDoc
     */
    public $templateName = 'deletedUnconfirmedUsersLog';
    
    /**
     * @var array
     */
    public $filter = [
        'userID' => null,
        'deletionType' => null,
        'registrationFromDate' => null,
        'registrationToDate' => null,
        'deletionFromDate' => null,
        'deletionToDate' => null,
    ];
    
    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();
        
        if (isset($_REQUEST['filter']) && \is_array($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if (\array_key_exists($key, $this->filter)) {
                    $this->filter[$key] = StringUtil::trim($value);
                }
            }
        }
    }
    
    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();
        
        if (!empty($this->filter['userID'])) {
            $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.userID = ?', [(int)$this->filter['userID']]);
        }
        
        if (!empty($this->filter['deletionType'])) {
            $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.deletionType = ?', [$this->filter['deletionType']]);
        }
        
        $timeZone = WCF::getUser()->getTimeZone();

        // Filter by registration date
        $registrationFromDate = $registrationToDate = 0;
        if (!empty($this->filter['registrationFromDate'])) {
            try {
                $registrationFromDate = (new \DateTimeImmutable($this->filter['registrationFromDate'], $timeZone))->getTimestamp();
            } catch (\Exception $e) {
                // ignore
            }
        }
        if (!empty($this->filter['registrationToDate'])) {
            try {
                $registrationToDate = (new \DateTimeImmutable($this->filter['registrationToDate'], $timeZone))->setTime(23, 59, 59)->getTimestamp();
            } catch (\Exception $e) {
                // ignore
            }
        }
        
        if ($registrationFromDate && $registrationToDate) {
            $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.registrationDate BETWEEN ? AND ?', [
                $registrationFromDate,
                $registrationToDate,
            ]);
        } else {
            if ($registrationFromDate) {
                $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.registrationDate >= ?', [$registrationFromDate]);
            }
            if ($registrationToDate) {
                $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.registrationDate <= ?', [$registrationToDate]);
            }
        }
        
        // Filter by deletion date
        $deletionFromDate = $deletionToDate = 0;
        if (!empty($this->filter['deletionFromDate'])) {
            try {
                $deletionFromDate = (new \DateTimeImmutable($this->filter['deletionFromDate'], $timeZone))->getTimestamp();
            } catch (\Exception $e) {
                // ignore
            }
        }
        if (!empty($this->filter['deletionToDate'])) {
            try {
                $deletionToDate = (new \DateTimeImmutable($this->filter['deletionToDate'], $timeZone))->setTime(23, 59, 59)->getTimestamp();
            } catch (\Exception $e) {
                // ignore
            }
        }
        
        if ($deletionFromDate && $deletionToDate) {
            $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.deletionDate BETWEEN ? AND ?', [
                $deletionFromDate,
                $deletionToDate,
            ]);
        } else {
            if ($deletionFromDate) {
                $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.deletionDate >= ?', [$deletionFromDate]);
            }
            if ($deletionToDate) {
                $this->objectList->getConditionBuilder()->add('deleted_unconfirmed_user_log.deletionDate <= ?', [$deletionToDate]);
            }
        }
    }
    
    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();
        
        WCF::getTPL()->assign([
            'filter' => $this->filter,
            'filterParameter' => \http_build_query(['filter' => $this->filter], '', '&'),
        ]);
    }
}