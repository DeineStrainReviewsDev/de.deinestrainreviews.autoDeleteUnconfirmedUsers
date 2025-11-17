<?php

namespace wcf\system\deleted\unconfirmed\user;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Handler for deleted unconfirmed user logs.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @package de.deinestrainreviews.autoDeleteUnconfirmedUsers
 */
class DeletedUnconfirmedUserLogHandler {
	/**
	 * Creates log entries for deleted users.
	 * 
	 * @param array $users Array of user data (userID, username, email, registrationDate)
	 * @return int Number of log entries created
	 */
	public static function createLogEntries(array $users) {
		if (empty($users)) {
			return 0;
		}
		
		$sql = "INSERT INTO wcf1_deleted_unconfirmed_user_log
				(userID, username, email, registrationDate, deletionDate)
				VALUES (?, ?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		$deletionDate = TIME_NOW;
		$count = 0;
		
		WCF::getDB()->beginTransaction();
		foreach ($users as $user) {
			$statement->execute([
				$user['userID'],
				$user['username'],
				$user['email'],
				$user['registrationDate'],
				$deletionDate
			]);
			$count++;
		}
		WCF::getDB()->commitTransaction();
		
		return $count;
	}
}

