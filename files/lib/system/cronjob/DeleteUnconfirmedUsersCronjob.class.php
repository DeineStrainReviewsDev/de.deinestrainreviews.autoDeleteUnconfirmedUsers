<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\user\LegacyAccountService;
use wcf\system\user\UnconfirmedUserService;

/**
 * Cronjob for automatic deletion of unconfirmed users.
 * 
 * Handles both recent unconfirmed users (normal workflow) and legacy accounts
 * (reputation protection). See LOGIC_MATRIX_v1.4.0.md for detailed behavior.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class DeleteUnconfirmedUsersCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob)
	{
		parent::execute($cronjob);

		// Get configuration
		$resendEmailDays = (int)AUTO_DELETE_UNCONFIRMED_USERS_RESEND_EMAIL_DAYS;
		$deleteDays = (int)AUTO_DELETE_UNCONFIRMED_USERS_DAYS;
		$limit = (int)AUTO_DELETE_UNCONFIRMED_USERS_LIMIT;

		// STEP 1: Handle legacy accounts first (if reputation protection enabled)
		$this->processLegacyAccounts($limit);

		// STEP 2: Handle recent unconfirmed users (filtered by reputation protection if enabled)
		if ($resendEmailDays > 0) {
			// Two-stage workflow: Reminder email first, then deletion
			$this->deleteUsersAfterResend($deleteDays, $limit);
			$this->resendConfirmationEmails($resendEmailDays, $deleteDays, $limit);
		} else {
			// Direct deletion without reminder email
			$this->deleteUsersDirectly($deleteDays, $limit);
		}
	}

	/**
	 * Processes legacy accounts based on reputation protection settings.
	 *
	 * Behavior depends on configuration:
	 * - MAX_AGE = 0: Skip (no reputation protection)
	 * - MAX_AGE > 0, DELETE_LEGACY = 1: Silent deletion
	 * - MAX_AGE > 0, DELETE_LEGACY = 0: Quarantine (log for manual review)
	 *
	 * @param int $limit Maximum number of accounts to process
	 */
	protected function processLegacyAccounts($limit)
	{
		// Check if reputation protection is enabled
		if (!LegacyAccountService::isProtectionEnabled()) {
			return;
		}

		$deleteLegacy = (bool)AUTO_DELETE_UNCONFIRMED_USERS_DELETE_LEGACY;

		// Find legacy accounts
		$legacyAccounts = LegacyAccountService::findLegacyAccounts($limit);

		if (empty($legacyAccounts)) {
			return;
		}

		if ($deleteLegacy) {
			// Silent deletion mode: Delete without email
			LegacyAccountService::silentDelete($legacyAccounts);
		} else {
			// Quarantine mode: Log for manual review
			LegacyAccountService::logForManualReview($legacyAccounts);
		}
	}

	/**
	 * Resends confirmation emails to recent unconfirmed users.
	 * Automatically filtered by reputation protection if enabled.
	 *
	 * @param int $resendEmailDays Days after registration to send reminder
	 * @param int $deleteDays Days after reminder to delete
	 * @param int $limit Maximum number of users to process
	 */
	protected function resendConfirmationEmails($resendEmailDays, $deleteDays, $limit)
	{
		$users = UnconfirmedUserService::findUsersForResend($resendEmailDays, $limit);
		UnconfirmedUserService::processResendEmails($users, $deleteDays);
	}

	/**
	 * Deletes recent users directly without resending email.
	 * Automatically filtered by reputation protection if enabled.
	 *
	 * @param int $deleteDays Days after registration to delete
	 * @param int $limit Maximum number of users to process
	 */
	protected function deleteUsersDirectly($deleteDays, $limit)
	{
		$users = UnconfirmedUserService::findUsersForDirectDeletion($deleteDays, $limit);
		UnconfirmedUserService::processUserDeletion($users);
	}

	/**
	 * Deletes recent users who received a reminder email.
	 * Automatically filtered by reputation protection if enabled.
	 *
	 * @param int $deleteDays Days after reminder to delete
	 * @param int $limit Maximum number of users to process
	 */
	protected function deleteUsersAfterResend($deleteDays, $limit)
	{
		$users = UnconfirmedUserService::findUsersForDeletionAfterResend($deleteDays, $limit);
		UnconfirmedUserService::processUserDeletion($users);
	}
}
