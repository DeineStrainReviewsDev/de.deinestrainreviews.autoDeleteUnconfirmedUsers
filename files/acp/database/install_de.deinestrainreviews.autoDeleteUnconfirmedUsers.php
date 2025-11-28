<?php

use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

/**
 * Database table installation for Auto Delete Unconfirmed Users plugin.
 * 
 * Version history:
 * - Versions < 1.2.0: No database tables
 * - Version 1.2.0: wcf1_deleted_unconfirmed_user_log (basic)
 * - Version 1.3.0: wcf1_resent_activation_email_log
 * - Version 1.4.0: wcf1_legacy_account_log + deletionType/deletionReason columns
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */

return [
	// Log table for resent activation emails (since v1.3.0)
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
			DatabaseTableIndex::create('registrationDate')
				->columns(['registrationDate']),
		]),
	
	// Log table for deleted unconfirmed users (since v1.2.0)
	DatabaseTable::create('wcf1_deleted_unconfirmed_user_log')
		->columns([
			NotNullInt10DatabaseTableColumn::create('logID')
				->autoIncrement(),
			NotNullInt10DatabaseTableColumn::create('userID'),
			NotNullVarchar255DatabaseTableColumn::create('username')
				->defaultValue(''),
			NotNullVarchar255DatabaseTableColumn::create('email')
				->defaultValue(''),
			NotNullInt10DatabaseTableColumn::create('registrationDate')
				->defaultValue(0),
			NotNullInt10DatabaseTableColumn::create('deletionDate')
				->defaultValue(0),
			VarcharDatabaseTableColumn::create('deletionType')
				->length(20)
				->notNull(false)
				->defaultValue(null), // NULL for old entries (before v1.4.0), 'automatic', 'manual', or 'silent' for new ones
		])
		->indices([
			DatabaseTablePrimaryIndex::create()
				->columns(['logID']),
			DatabaseTableIndex::create('deletionDate')
				->columns(['deletionDate']),
			DatabaseTableIndex::create('userID')
				->columns(['userID']),
			DatabaseTableIndex::create('deletionType')
				->columns(['deletionType']),
			DatabaseTableIndex::create('registrationDate')
				->columns(['registrationDate']),
		]),
	
	// Log table for legacy accounts (since v1.4.0)
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
				->defaultValue('pending'), // 'pending' or 'deleted'
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
];
