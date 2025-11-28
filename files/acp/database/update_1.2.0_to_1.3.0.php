<?php

use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

/**
 * Database update script from version 1.2.0 to 1.3.0.
 * 
 * Adds the resent activation email log table for the new reminder email feature.
 * No data migration needed as this is a new feature in 1.3.0.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */

return [
	// Create new table for resent activation emails (new in 1.3.0)
	DatabaseTable::create('wcf1_resent_activation_email_log')
		->columns([
			NotNullInt10DatabaseTableColumn::create('logID')
				->autoIncrement(),
			NotNullInt10DatabaseTableColumn::create('userID'),
			NotNullInt10DatabaseTableColumn::create('registrationDate')
				->defaultValue(0),
			NotNullInt10DatabaseTableColumn::create('resendEmailDate')
				->defaultValue(0),
		])
		->indices([
			DatabaseTablePrimaryIndex::create()
				->columns(['logID']),
			DatabaseTableIndex::create('userID')
				->columns(['userID']),
			DatabaseTableIndex::create('resendEmailDate')
				->columns(['resendEmailDate']),
		]),
];

