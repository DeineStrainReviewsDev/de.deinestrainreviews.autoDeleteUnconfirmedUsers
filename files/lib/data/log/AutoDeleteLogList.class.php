<?php

namespace wcf\data\log;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of auto-delete log entries.
 * 
 * @copyright 2025 DeineStrainReviews.de
 * @author DeineStrainReviews.de Development Team
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class AutoDeleteLogList extends DatabaseObjectList {
    /**
     * @inheritDoc
     */
    public $className = AutoDeleteLog::class;
    
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'executionTime DESC';
}

