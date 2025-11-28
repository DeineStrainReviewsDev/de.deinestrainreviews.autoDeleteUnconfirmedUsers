<?php

namespace wcf\system\user;

use wcf\data\user\User;
use wcf\data\user\group\UserGroup;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\email\Email;
use wcf\system\email\mime\MimePartFacade;
use wcf\system\email\mime\RecipientAwareTextMimePart;
use wcf\system\email\UserMailbox;
use wcf\system\request\LinkHandler;
use wcf\system\template\EmailTemplateEngine;
use wcf\system\WCF;

/**
 * Service class for sending emails related to unconfirmed users.
 *
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class DSRUnconfirmedUserMailService {
	/**
	 * Sends a reminder email with deletion warning to an unconfirmed user.
	 *
	 * @param array $userData User data array with userID
	 * @param int $deleteDays Number of days until deletion
	 */
	public static function sendReminderEmail($userData, $deleteDays)
	{
		$user = new User($userData['userID']);

		// Generate activation link
		$activationLink = LinkHandler::getInstance()->getLink('RegisterActivation', [
			'u' => $user->userID,
			'a' => $user->emailConfirmed,
		], '', true);

		// Generate contact form link (if module is enabled)
		$contactLink = '';
		if (defined('MODULE_CONTACT_FORM') && MODULE_CONTACT_FORM) {
			try {
				$contactLink = LinkHandler::getInstance()->getLink('Contact', ['forceFrontend' => true]);
			} catch (\Exception $e) {
				// Contact form not available, fallback to empty string
				$contactLink = '';
			}
		}

		// Get language for the user (fallback to default if not set)
		$userLanguage = $user->getLanguage();

		// Prepare email variables
		$emailData = [
			'username' => $user->username,
			'activationLink' => $activationLink,
			'activationCode' => $user->emailConfirmed,
			'deleteDays' => $deleteDays,
			'contactLink' => $contactLink,
		];

		// Get subject and message from language variables
		$subject = $userLanguage->getDynamicVariable('wcf.user.notification.reminderActivation.subject', $emailData);
		$messageHtml = $userLanguage->getDynamicVariable('wcf.user.notification.reminderActivation.message', $emailData);
		$messagePlaintext = $userLanguage->getDynamicVariable('wcf.user.notification.reminderActivation.message.plaintext', $emailData);

		// Create and send email
		$email = new Email();
		$email->addRecipient(new UserMailbox($user));
		$email->setSubject($subject);
		$email->setBody(new MimePartFacade([
			new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $messageHtml),
			new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', $messagePlaintext),
		]));

		$email->send();
	}

	/**
	 * Gets all administrators from admin groups.
	 *
	 * @return array Array of administrator user data
	 */
	protected static function getAdministrators()
	{
		// Get all administrators using UserGroup::isAdminGroup()
		$adminGroupIDs = [];
		// Get all groups (not excluding OWNER groups) and check if they are admin groups
		foreach (UserGroup::getGroupsByType() as $group) {
			if ($group->isAdminGroup()) {
				$adminGroupIDs[] = $group->groupID;
			}
		}

		if (empty($adminGroupIDs)) {
			return [];
		}

		// Get all users in admin groups
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add('ug.groupID IN (?)', [$adminGroupIDs]);
		$conditions->add('u.userID <> ?', [0]);

		$sql = "SELECT DISTINCT u.userID, u.email, u.username
			FROM wcf1_user_to_group ug
			INNER JOIN wcf1_user u ON u.userID = ug.userID
			" . $conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Formats the anonymized user list as HTML using template.
	 *
	 * @param array $anonymizedUsers Array of anonymized user data
	 * @param string $templateName Template name to use
	 * @return string HTML content
	 */
	protected static function formatUserListForEmail($anonymizedUsers, $templateName)
	{
		if (empty($anonymizedUsers)) {
			return '';
		}

		// Format dates for template
		$formattedUsers = [];
		foreach ($anonymizedUsers as $user) {
			$formattedUser = $user;
			if (isset($user['registrationDate'])) {
				$formattedUser['registrationDate'] = $user['registrationDate'];
			}
			if (isset($user['deletionDate'])) {
				$formattedUser['deletionDate'] = $user['deletionDate'];
			}
			if (isset($user['resendEmailDate'])) {
				$formattedUser['resendEmailDate'] = $user['resendEmailDate'];
			}
			$formattedUsers[] = $formattedUser;
		}

		return EmailTemplateEngine::getInstance()->fetch(
			$templateName,
			'wcf',
			['anonymizedUsers' => $formattedUsers],
			true
		);
	}

	/**
	 * Sends email notification to administrators about deleted users.
	 *
	 * @param int $deletedCount Number of deleted users
	 * @param array $anonymizedUsers Array of anonymized user data
	 */
	public static function notifyAdministrators($deletedCount, $anonymizedUsers = [])
	{
		if ($deletedCount == 0) {
			return;
		}

		$administrators = self::getAdministrators();
		if (empty($administrators)) {
			return;
		}

		// Generate log page URL
		try {
			$logPageUrl = LinkHandler::getInstance()->getLink('DeletedUnconfirmedUsersLog', [], '', true);
		} catch (\Exception $e) {
			$logPageUrl = '';
		}

		// Format user list for email using template
		$userListHtml = self::formatUserListForEmail($anonymizedUsers, 'email_deletedUsersList');

		// Send email to each administrator
		foreach ($administrators as $admin) {
			try {
				$subject = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedUnconfirmedUsers.subject', ['count' => $deletedCount]);

				$message = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedUnconfirmedUsers.message', [
					'count' => $deletedCount,
					'username' => $admin['username'],
					'logPageUrl' => $logPageUrl,
					'userList' => $userListHtml,
				]);

				$adminUser = new User($admin['userID']);
				$email = new Email();
				$email->addRecipient(new UserMailbox($adminUser));
				$email->setSubject($subject);

				$email->setBody(new MimePartFacade([
					new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $message),
					new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', strip_tags($message)),
				]));

				$email->send();
			} catch (\Exception $e) {
				// Log error but continue with other administrators
				\wcf\functions\exception\logThrowable($e);
			}
		}
	}

	/**
	 * Sends email notification to administrators about resent activation emails.
	 *
	 * @param int $resentCount Number of resent emails
	 * @param array $anonymizedUsers Array of anonymized user data
	 */
	public static function notifyAdministratorsResent($resentCount, $anonymizedUsers = [])
	{
		if ($resentCount == 0) {
			return;
		}

		$administrators = self::getAdministrators();
		if (empty($administrators)) {
			return;
		}

		// Generate log page URL
		try {
			$logPageUrl = LinkHandler::getInstance()->getLink('ResentActivationEmailLog', [], '', true);
		} catch (\Exception $e) {
			$logPageUrl = '';
		}

		// Format user list for email using template
		$userListHtml = self::formatUserListForEmail($anonymizedUsers, 'email_resentActivationLog');

		// Send email to each administrator
		foreach ($administrators as $admin) {
			try {
				$subject = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.resentActivationEmails.subject', ['count' => $resentCount]);

				$message = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.resentActivationEmails.message', [
					'count' => $resentCount,
					'username' => $admin['username'],
					'logPageUrl' => $logPageUrl,
					'userList' => $userListHtml,
				]);

				$adminUser = new User($admin['userID']);
				$email = new Email();
				$email->addRecipient(new UserMailbox($adminUser));
				$email->setSubject($subject);

				$email->setBody(new MimePartFacade([
					new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $message),
					new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', strip_tags($message)),
				]));

				$email->send();
			} catch (\Exception $e) {
				// Log error but continue with other administrators
				\wcf\functions\exception\logThrowable($e);
			}
		}
	}

	/**
	 * Sends email notification to administrators about legacy users deleted silently.
	 *
	 * @param int $deletedCount Number of deleted legacy users
	 * @param array $anonymizedUsers Array of anonymized user data
	 * @param int $maxAge Maximum registration age threshold
	 */
	public static function notifyAdministratorsLegacyDeletion($deletedCount, $anonymizedUsers = [], $maxAge = 0)
	{
		if ($deletedCount == 0) {
			return;
		}

		$administrators = self::getAdministrators();
		if (empty($administrators)) {
			return;
		}

		// Generate log page URL
		try {
			$logPageUrl = LinkHandler::getInstance()->getLink('DeletedUnconfirmedUsersLog', [], '', true);
		} catch (\Exception $e) {
			$logPageUrl = '';
		}

		// Format user list for email using template
		$userListHtml = self::formatUserListForEmail($anonymizedUsers, 'email_deletedUsersList');

		// Send email to each administrator
		foreach ($administrators as $admin) {
			try {
				$subject = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedLegacyUsers.subject', ['count' => $deletedCount]);

				$message = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedLegacyUsers.message', [
					'count' => $deletedCount,
					'username' => $admin['username'],
					'logPageUrl' => $logPageUrl,
					'userList' => $userListHtml,
					'maxAge' => $maxAge,
				]);

				$adminUser = new User($admin['userID']);
				$email = new Email();
				$email->addRecipient(new UserMailbox($adminUser));
				$email->setSubject($subject);

				$email->setBody(new MimePartFacade([
					new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $message),
					new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', strip_tags($message)),
				]));

				$email->send();
			} catch (\Exception $e) {
				// Log error but continue with other administrators
				\wcf\functions\exception\logThrowable($e);
			}
		}
	}
}
