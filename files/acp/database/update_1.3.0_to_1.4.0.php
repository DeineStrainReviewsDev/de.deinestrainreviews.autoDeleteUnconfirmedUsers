<?php

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

/**
 * Database update script from version 1.3.0 to 1.4.0.
 * 
 * Creates the legacy account log table for email reputation protection.
 * Adds deletionType and deletionReason columns to deleted_unconfirmed_user_log.
 * Adds registrationDate indices for improved filter performance.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */

return [
	// Create new legacy account log table (new in 1.4.0)
	DatabaseTable::create('wcf1_legacy_account_log')
		->columns([
			NotNullInt10DatabaseTableColumn::create('logID')
				->autoIncrement(),
			NotNullInt10DatabaseTableColumn::create('userID'),
			VarcharDatabaseTableColumn::create('username')
				->length(255),
			VarcharDatabaseTableColumn::create('email')
				->length(255),
			NotNullInt10DatabaseTableColumn::create('registrationDate'),
			NotNullInt10DatabaseTableColumn::create('detectionDate'),
			IntDatabaseTableColumn::create('deletionDate')
				->length(10)
				->notNull(false)
				->defaultValue(null),
			VarcharDatabaseTableColumn::create('status')
				->length(50)
				->defaultValue('pending'),
		])
		->indices([
			DatabaseTablePrimaryIndex::create()
				->columns(['logID']),
			DatabaseTableIndex::create('userID')
				->columns(['userID']),
			DatabaseTableIndex::create('status')
				->columns(['status']),
			DatabaseTableIndex::create('detectionDate')
				->columns(['detectionDate']),
			DatabaseTableIndex::create('registrationDate')
				->columns(['registrationDate']),
		]),
	
	// Update existing deleted_unconfirmed_user_log table with new column
	DatabaseTable::create('wcf1_deleted_unconfirmed_user_log')
		->columns([
			VarcharDatabaseTableColumn::create('deletionType')
				->length(20)
				->notNull(false)
				->defaultValue(null), // NULL for old entries, 'automatic', 'manual', or 'silent' for new ones
		])
		->indices([
			DatabaseTableIndex::create('deletionType')
				->columns(['deletionType']),
			DatabaseTableIndex::create('registrationDate')
				->columns(['registrationDate']),
		]),
	
	// Add registrationDate index to resent_activation_email_log for improved filter performance
	DatabaseTable::create('wcf1_resent_activation_email_log')
		->indices([
			DatabaseTableIndex::create('registrationDate')
				->columns(['registrationDate']),
		]),
];

