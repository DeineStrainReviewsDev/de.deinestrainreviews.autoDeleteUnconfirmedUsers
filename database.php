<?php

use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
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
