<?php

namespace wcf\system\user;

use wcf\data\legacyAccountLog\LegacyAccountLogEditor;
use wcf\data\user\UserAction;
use wcf\system\WCF;
use wcf\util\DSRGdprAnonymizer;

/**
 * Service class for managing legacy accounts (email reputation protection).
 * 
 * Legacy accounts are unconfirmed users who registered beyond the configured
 * maximum age threshold. These accounts are risky to email due to potential
 * bounces and spam traps.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class LegacyAccountService {
	/**
	 * Checks if legacy account protection is enabled.
	 *
	 * @return bool True if protection is enabled
	 */
	public static function isProtectionEnabled()
	{
		$maxAge = (int)AUTO_DELETE_UNCONFIRMED_USERS_MAX_REGISTRATION_AGE;
		return $maxAge > 0;
	}

	/**
	 * Gets the legacy account age threshold in seconds.
	 *
	 * @return int Timestamp threshold (0 if disabled)
	 */
	public static function getLegacyThreshold()
	{
		$maxAge = (int)AUTO_DELETE_UNCONFIRMED_USERS_MAX_REGISTRATION_AGE;

		if ($maxAge <= 0) {
			return 0;
		}

		return TIME_NOW - ($maxAge * 86400);
	}

	/**
	 * Returns SQL WHERE clause fragment to exclude legacy accounts.
	 *
	 * @return string SQL fragment (empty if protection disabled)
	 */
	public static function getExclusionFilter()
	{
		$threshold = self::getLegacyThreshold();

		if ($threshold <= 0) {
			return '';
		}

		return " AND u.registrationDate >= {$threshold}";
	}

	/**
	 * Finds legacy accounts eligible for processing.
	 *
	 * @param int $limit Maximum number of accounts to return
	 * @return array List of legacy accounts
	 */
	public static function findLegacyAccounts($limit)
	{
		$threshold = self::getLegacyThreshold();

		if ($threshold <= 0) {
			return [];
		}

		$sql = "SELECT u.userID, u.username, u.email, u.registrationDate
			FROM wcf1_user u
			INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
			LEFT JOIN wcf1_deleted_unconfirmed_user_log deletedLog ON deletedLog.userID = u.userID AND deletedLog.deletionDate > 0
			LEFT JOIN wcf1_legacy_account_log legacyLog ON legacyLog.userID = u.userID
			WHERE u.emailConfirmed IS NOT NULL
			AND u.registrationDate < ?
			AND deletedLog.logID IS NULL
			AND legacyLog.logID IS NULL
			GROUP BY u.userID, u.username, u.email, u.registrationDate
			HAVING COUNT(DISTINCT ug.groupID) <= 2
			AND SUM(ug.groupID NOT IN (?, ?)) = 0
			LIMIT ?";

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$threshold, 1, 2, $limit]);

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Logs legacy accounts for manual review (quarantine mode).
	 *
	 * @param array $accounts List of legacy accounts
	 * @return int Number of accounts logged
	 */
	public static function logForManualReview(array $accounts)
	{
		if (empty($accounts)) {
			return 0;
		}

		$detectionDate = TIME_NOW;
		$loggedCount = 0;

		foreach ($accounts as $account) {
			try {
				LegacyAccountLogEditor::create([
					'userID' => $account['userID'],
					'username' => DSRGdprAnonymizer::maskUsername($account['username']),
					'email' => DSRGdprAnonymizer::maskEmail($account['email']),
					'registrationDate' => $account['registrationDate'],
					'detectionDate' => $detectionDate,
					'status' => 'pending', // pending, deleted
				]);
				$loggedCount++;
			} catch (\Exception $e) {
				\wcf\functions\exception\logThrowable($e);
			}
		}

		return $loggedCount;
	}

	/**
	 * Silently deletes legacy accounts without email notification.
	 *
	 * @param array $accounts List of legacy accounts to delete
	 * @return int Number of accounts deleted
	 */
	public static function silentDelete(array $accounts)
	{
		if (empty($accounts)) {
			return 0;
		}

		$deletionDate = TIME_NOW;
		$userIDs = array_column($accounts, 'userID');

		// Log in deleted users table (marked as silent/legacy deletion)
		foreach ($accounts as $account) {
			try {
				\wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogEditor::create([
					'userID' => $account['userID'],
					'username' => DSRGdprAnonymizer::maskUsername($account['username']),
					'email' => DSRGdprAnonymizer::maskEmail($account['email']),
					'registrationDate' => $account['registrationDate'],
					'deletionDate' => $deletionDate,
					'deletionType' => 'silent',
				]);
			} catch (\Exception $e) {
				\wcf\functions\exception\logThrowable($e);
			}
		}

		// Execute deletion
		try {
			$userAction = new UserAction($userIDs, 'delete');
			$userAction->executeAction();
		} catch (\Exception $e) {
			\wcf\functions\exception\logThrowable($e);
			return 0;
		}

		// Send admin notification if enabled
		if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL_DELETED) {
			self::notifyAdministrators($accounts, $deletionDate);
		}

		return count($accounts);
	}

	/**
	 * Manually deletes legacy accounts from ACP (with logging).
	 *
	 * @param array $userIDs Array of user IDs to delete
	 * @return array ['success' => int, 'failed' => int]
	 */
	public static function manualDelete(array $userIDs)
	{
		if (empty($userIDs)) {
			return ['success' => 0, 'failed' => 0];
		}

		$deletionDate = TIME_NOW;
		$success = 0;
		$failed = 0;

		// Get user data before deletion
		$sql = "SELECT u.userID, u.username, u.email, u.registrationDate
			FROM wcf1_user u
			WHERE u.userID IN (" . str_repeat('?,', count($userIDs) - 1) . "?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($userIDs);
		$users = $statement->fetchAll(\PDO::FETCH_ASSOC);

		// Log deletions
		foreach ($users as $user) {
			try {
				\wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogEditor::create([
					'userID' => $user['userID'],
					'username' => DSRGdprAnonymizer::maskUsername($user['username']),
					'email' => DSRGdprAnonymizer::maskEmail($user['email']),
					'registrationDate' => $user['registrationDate'],
					'deletionDate' => $deletionDate,
					'deletionType' => 'manual',
				]);
			} catch (\Exception $e) {
				\wcf\functions\exception\logThrowable($e);
			}
		}

		// Update legacy log status to 'deleted'
		$sql = "UPDATE wcf1_legacy_account_log
			SET status = 'deleted', deletionDate = ?
			WHERE userID IN (" . str_repeat('?,', count($userIDs) - 1) . "?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array_merge([$deletionDate], $userIDs));

		// Execute deletion
		try {
			$userAction = new UserAction($userIDs, 'delete');
			$userAction->executeAction();
			$success = count($userIDs);
		} catch (\Exception $e) {
			\wcf\functions\exception\logThrowable($e);
			$failed = count($userIDs);
		}

		return ['success' => $success, 'failed' => $failed];
	}

	/**
	 * Notifies administrators about legacy account deletions.
	 *
	 * @param array $accounts Deleted accounts
	 * @param int $deletionDate Timestamp of deletion
	 */
	protected static function notifyAdministrators(array $accounts, $deletionDate)
	{
		$anonymizedAccounts = [];

		foreach ($accounts as $account) {
			$anonymizedAccounts[] = [
				'username' => DSRGdprAnonymizer::maskUsername($account['username']),
				'email' => DSRGdprAnonymizer::maskEmail($account['email']),
				'registrationDate' => $account['registrationDate'],
				'deletionDate' => $deletionDate,
			];
		}

		DSRUnconfirmedUserMailService::notifyAdministratorsLegacyDeletion(
			count($accounts),
			$anonymizedAccounts,
			(int)AUTO_DELETE_UNCONFIRMED_USERS_MAX_REGISTRATION_AGE
		);
	}

	/**
	 * Gets count of pending legacy accounts in quarantine.
	 *
	 * @return int Count of pending accounts
	 */
	public static function getPendingCount()
	{
		$sql = "SELECT COUNT(*) as count
			FROM wcf1_legacy_account_log
			WHERE status = 'pending'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchSingleRow();

		return (int)$row['count'];
	}
}

