<?php

namespace wcf\system\user;

use wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogEditor;
use wcf\data\resentActivationEmailLog\ResentActivationEmailLogEditor;
use wcf\data\user\UserAction;
use wcf\system\user\DSRUnconfirmedUserMailService;
use wcf\system\user\LegacyAccountService;
use wcf\system\WCF;
use wcf\util\DSRGdprAnonymizer;

/**
 * Service class for managing unconfirmed users.
 * Handles queries, processing and business logic for unconfirmed user management.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class UnconfirmedUserService {
	/**
	 * Finds recent unconfirmed users eligible for reminder emails.
	 * Excludes legacy accounts if reputation protection is enabled.
	 *
	 * @param int $days Days since registration
	 * @param int $limit Maximum number of users to process
	 * @return array List of users
	 */
	public static function findUsersForResend($days, $limit)
	{
		$timeLimit = TIME_NOW - ($days * 86400);

		// Apply legacy account exclusion filter if reputation protection enabled
		$legacyFilter = LegacyAccountService::getExclusionFilter();

		$sql = "SELECT u.userID, u.username, u.email, u.registrationDate, u.emailConfirmed
			FROM wcf1_user u
			INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
			LEFT JOIN wcf1_resent_activation_email_log log ON log.userID = u.userID
			WHERE u.emailConfirmed IS NOT NULL
			AND u.registrationDate < ?
			{$legacyFilter}
			AND log.logID IS NULL
			GROUP BY u.userID, u.username, u.email, u.registrationDate, u.emailConfirmed
			HAVING COUNT(DISTINCT ug.groupID) <= 2
			AND SUM(ug.groupID NOT IN (?, ?)) = 0
			LIMIT ?";

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$timeLimit, 1, 2, $limit]);

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Finds recent unconfirmed users eligible for direct deletion (without prior reminder).
	 * Excludes legacy accounts if reputation protection is enabled.
	 *
	 * @param int $days Days since registration
	 * @param int $limit Maximum number of users to process
	 * @return array List of users
	 */
	public static function findUsersForDirectDeletion($days, $limit)
	{
		$timeLimit = TIME_NOW - ($days * 86400);

		// Apply legacy account exclusion filter if reputation protection enabled
		$legacyFilter = LegacyAccountService::getExclusionFilter();

		$sql = "SELECT u.userID, u.username, u.email, u.registrationDate
			FROM wcf1_user u
			INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
			LEFT JOIN wcf1_deleted_unconfirmed_user_log log ON log.userID = u.userID AND log.deletionDate > 0
			WHERE u.emailConfirmed IS NOT NULL
			AND u.registrationDate < ?
			{$legacyFilter}
			AND log.logID IS NULL
			GROUP BY u.userID, u.username, u.email, u.registrationDate
			HAVING COUNT(DISTINCT ug.groupID) <= 2
			AND SUM(ug.groupID NOT IN (?, ?)) = 0
			LIMIT ?";

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$timeLimit, 1, 2, $limit]);

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Finds unconfirmed users who received a reminder and are eligible for deletion.
	 *
	 * @param int $days Days since reminder was sent
	 * @param int $limit Maximum number of users to process
	 * @return array List of users
	 */
	public static function findUsersForDeletionAfterResend($days, $limit)
	{
		$timeLimit = TIME_NOW - ($days * 86400);

		$sql = "SELECT u.userID, u.username, u.email, u.registrationDate, resendLog.resendEmailDate
			FROM wcf1_user u
			INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
			INNER JOIN wcf1_resent_activation_email_log resendLog ON resendLog.userID = u.userID
			LEFT JOIN wcf1_deleted_unconfirmed_user_log deletedLog ON deletedLog.userID = u.userID
			WHERE u.emailConfirmed IS NOT NULL
			AND resendLog.resendEmailDate > 0
			AND resendLog.resendEmailDate < ?
			AND deletedLog.logID IS NULL
			GROUP BY u.userID, u.username, u.email, u.registrationDate, resendLog.resendEmailDate
			HAVING COUNT(DISTINCT ug.groupID) <= 2
			AND SUM(ug.groupID NOT IN (?, ?)) = 0
			LIMIT ?";

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$timeLimit, 1, 2, $limit]);

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Processes reminder email sending for unconfirmed users.
	 *
	 * @param array $users List of users to process
	 * @param int $deleteDays Days until deletion (for warning message)
	 * @return array List of successfully processed users
	 */
	public static function processResendEmails(array $users, $deleteDays)
	{
		if (empty($users)) {
			return [];
		}

		$resendDate = TIME_NOW;
		$successfulSends = [];

		// Send reminder emails
		foreach ($users as $user) {
			try {
				DSRUnconfirmedUserMailService::sendReminderEmail($user, $deleteDays);
				$successfulSends[] = $user;
			} catch (\Exception $e) {
				\wcf\functions\exception\logThrowable($e);
			}
		}

		// Create log entries for successful sends
		foreach ($successfulSends as $user) {
			try {
				ResentActivationEmailLogEditor::create([
					'userID' => $user['userID'],
					'registrationDate' => $user['registrationDate'],
					'resendEmailDate' => $resendDate,
				]);
			} catch (\Exception $e) {
				\wcf\functions\exception\logThrowable($e);
			}
		}

		// Notify administrators if enabled
		if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL_RESENT && !empty($successfulSends)) {
			$anonymizedUsers = self::anonymizeUsers($successfulSends, [
				'resendEmailDate' => $resendDate,
			]);
			DSRUnconfirmedUserMailService::notifyAdministratorsResent(
				count($successfulSends),
				$anonymizedUsers
			);
		}

		return $successfulSends;
	}

	/**
	 * Processes deletion of unconfirmed users.
	 *
	 * @param array $users List of users to delete
	 * @return int Number of deleted users
	 */
	public static function processUserDeletion(array $users)
	{
		if (empty($users)) {
			return 0;
		}

		$deletionDate = TIME_NOW;
		$userIDs = array_column($users, 'userID');

		// Create log entries before deletion
		foreach ($users as $user) {
			try {
				DeletedUnconfirmedUserLogEditor::create([
					'userID' => $user['userID'],
					'username' => DSRGdprAnonymizer::maskUsername($user['username']),
					'email' => DSRGdprAnonymizer::maskEmail($user['email']),
					'registrationDate' => $user['registrationDate'],
					'deletionDate' => $deletionDate,
					'deletionType' => 'automatic',
				]);
			} catch (\Exception $e) {
				\wcf\functions\exception\logThrowable($e);
			}
		}

		// Delete users
		try {
			$userAction = new UserAction($userIDs, 'delete');
			$userAction->executeAction();
		} catch (\Exception $e) {
			\wcf\functions\exception\logThrowable($e);
			return 0;
		}

		// Notify administrators if enabled
		if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL_DELETED) {
			$anonymizedUsers = self::anonymizeUsers($users, [
				'deletionDate' => $deletionDate,
			]);
			DSRUnconfirmedUserMailService::notifyAdministrators(
				count($users),
				$anonymizedUsers
			);
		}

		return count($users);
	}

	/**
	 * Anonymizes user data for GDPR compliance.
	 *
	 * @param array $users List of users
	 * @param array $additionalFields Additional fields to include in result
	 * @return array Anonymized user data
	 */
	protected static function anonymizeUsers(array $users, array $additionalFields = [])
	{
		$anonymizedUsers = [];

		foreach ($users as $user) {
			$anonymizedUser = [
				'username' => DSRGdprAnonymizer::maskUsername($user['username']),
				'email' => DSRGdprAnonymizer::maskEmail($user['email']),
				'registrationDate' => $user['registrationDate'],
			];

			// Add additional fields
			foreach ($additionalFields as $key => $value) {
				$anonymizedUser[$key] = $value;
			}

			$anonymizedUsers[] = $anonymizedUser;
		}

		return $anonymizedUsers;
	}
}
