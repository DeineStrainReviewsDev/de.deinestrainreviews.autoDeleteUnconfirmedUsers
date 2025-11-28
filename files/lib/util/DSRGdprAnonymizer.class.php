<?php

namespace wcf\util;

/**
 * Utility class for GDPR-compliant anonymization of user data.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class DSRGdprAnonymizer {
	/**
	 * Anonymizes a username for GDPR compliance.
	 *
	 * @param string $username
	 * @return string
	 */
	public static function maskUsername($username)
	{
		$length = mb_strlen($username);

		if ($length <= 4) {
			// Very short usernames: fully anonymize
			return str_repeat('*', $length);
		} elseif ($length <= 8) {
			// Medium usernames: show first 2, mask middle, show last 2
			return mb_substr($username, 0, 2) . '***' . mb_substr($username, -2);
		} else {
			// Long usernames: show first 2, mask middle, show last 4
			return mb_substr($username, 0, 2) . '***' . mb_substr($username, -4);
		}
	}

	/**
	 * Anonymizes an email address for GDPR compliance.
	 *
	 * @param string $email
	 * @return string
	 */
	public static function maskEmail($email)
	{
		if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return '***@***.***';
		}

		list($localPart, $domain) = explode('@', $email, 2);

		// Anonymize local part: show first character, mask the rest
		$localLength = mb_strlen($localPart);
		if ($localLength <= 1) {
			$anonymizedLocal = '*';
		} else {
			$anonymizedLocal = mb_substr($localPart, 0, 1) . '***';
		}

		// Anonymize domain: show first 2 characters, mask middle
		$domainParts = explode('.', $domain);
		$tld = array_pop($domainParts);
		$domainName = implode('.', $domainParts);

		$domainLength = mb_strlen($domainName);
		if ($domainLength <= 2) {
			$anonymizedDomain = '***';
		} else {
			$anonymizedDomain = mb_substr($domainName, 0, 2) . '***';
		}

		// Anonymize TLD: show first 1-2 characters, mask the rest
		$tldLength = mb_strlen($tld);
		if ($tldLength <= 1) {
			$anonymizedTld = '*';
		} elseif ($tldLength <= 3) {
			$anonymizedTld = mb_substr($tld, 0, 1) . '***';
		} else {
			$anonymizedTld = mb_substr($tld, 0, 2) . '***';
		}

		return $anonymizedLocal . '@' . $anonymizedDomain . '.' . $anonymizedTld;
	}
}

