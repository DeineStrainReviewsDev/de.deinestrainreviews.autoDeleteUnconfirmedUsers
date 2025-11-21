<?php

use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

// Update from 1.2.0 to 1.3.0
// Version 1.2.0 had only: wcf1_deleted_unconfirmed_user_log
// Version 1.3.0 adds: wcf1_resent_activation_email_log (new feature)
// No migration needed as resend feature is new in 1.3.0

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

