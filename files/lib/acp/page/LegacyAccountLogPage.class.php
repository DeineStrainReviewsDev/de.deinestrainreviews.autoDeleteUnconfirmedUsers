<?php

namespace wcf\acp\page;

use wcf\data\legacyAccountLog\LegacyAccountLogList;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the log of legacy accounts (quarantined unconfirmed users).
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class LegacyAccountLogPage extends SortablePage {
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.legacyAccountLog';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canViewDeletedUnconfirmedUsersLog'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = LegacyAccountLogList::class;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'detectionDate';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['logID', 'userID', 'registrationDate', 'detectionDate', 'status'];

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 30;

    /**
     * @var array
     */
    public $filter = [
        'userID' => null,
        'registrationFromDate' => null,
        'registrationToDate' => null,
        'detectionFromDate' => null,
        'detectionToDate' => null,
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

        // Only show pending legacy accounts (not yet deleted)
        $this->objectList->getConditionBuilder()->add('legacy_account_log.status = ?', ['pending']);

        if (!empty($this->filter['userID'])) {
            $this->objectList->getConditionBuilder()->add('legacy_account_log.userID = ?', [(int)$this->filter['userID']]);
        }

        $timeZone = WCF::getUser()->getTimeZone();

        // Filter by registration date
        $registrationFromDate = $registrationToDate = 0;
        if (!empty($this->filter['registrationFromDate'])) {
            try {
                $registrationFromDate = (new \DateTimeImmutable($this->filter['registrationFromDate'], $timeZone))->getTimestamp();
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }
        if (!empty($this->filter['registrationToDate'])) {
            try {
                $registrationToDate = (new \DateTimeImmutable($this->filter['registrationToDate'], $timeZone))->setTime(23, 59, 59)->getTimestamp();
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        if ($registrationFromDate && $registrationToDate) {
            $this->objectList->getConditionBuilder()->add('legacy_account_log.registrationDate BETWEEN ? AND ?', [
                $registrationFromDate,
                $registrationToDate,
            ]);
        } else {
            if ($registrationFromDate) {
                $this->objectList->getConditionBuilder()->add('legacy_account_log.registrationDate >= ?', [$registrationFromDate]);
            }
            if ($registrationToDate) {
                $this->objectList->getConditionBuilder()->add('legacy_account_log.registrationDate <= ?', [$registrationToDate]);
            }
        }

        // Filter by detection date
        $detectionFromDate = $detectionToDate = 0;
        if (!empty($this->filter['detectionFromDate'])) {
            try {
                $detectionFromDate = (new \DateTimeImmutable($this->filter['detectionFromDate'], $timeZone))->getTimestamp();
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }
        if (!empty($this->filter['detectionToDate'])) {
            try {
                $detectionToDate = (new \DateTimeImmutable($this->filter['detectionToDate'], $timeZone))->setTime(23, 59, 59)->getTimestamp();
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        if ($detectionFromDate && $detectionToDate) {
            $this->objectList->getConditionBuilder()->add('legacy_account_log.detectionDate BETWEEN ? AND ?', [
                $detectionFromDate,
                $detectionToDate,
            ]);
        } else {
            if ($detectionFromDate) {
                $this->objectList->getConditionBuilder()->add('legacy_account_log.detectionDate >= ?', [$detectionFromDate]);
            }
            if ($detectionToDate) {
                $this->objectList->getConditionBuilder()->add('legacy_account_log.detectionDate <= ?', [$detectionToDate]);
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
            'maxAge' => (int) AUTO_DELETE_UNCONFIRMED_USERS_MAX_REGISTRATION_AGE,
            'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(
                ClipboardHandler::getInstance()->getObjectTypeID('de.deinestrainreviews.legacyAccount')
            ),
            'filter' => $this->filter,
            'filterParameter' => \http_build_query(['filter' => $this->filter], '', '&'),
        ]);
    }
}