<?php

use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

// Note: Version history
// - Versions < 1.2.0: No database tables
// - Version 1.2.0: Only wcf1_deleted_unconfirmed_user_log table
// - Version 1.3.0: Both tables (added wcf1_resent_activation_email_log)
// No migration needed as resend feature is new in 1.3.0

return [
	// Log table for resent activation emails
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
	
	// Log table for deleted unconfirmed users
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
		])
		->indices([
			DatabaseTablePrimaryIndex::create()
				->columns(['logID']),
			DatabaseTableIndex::create('deletionDate')
				->columns(['deletionDate']),
			DatabaseTableIndex::create('userID')
				->columns(['userID']),
		]),
];







