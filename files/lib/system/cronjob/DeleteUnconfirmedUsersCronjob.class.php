<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\deletedUnconfirmedUsersLog\DeletedUnconfirmedUserLogEditor;
use wcf\data\resentActivationEmailLog\ResentActivationEmailLogEditor;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\group\UserGroup;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\email\Email;
use wcf\system\email\mime\MimePartFacade;
use wcf\system\email\mime\RecipientAwareTextMimePart;
use wcf\system\email\UserMailbox;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Cronjob for automatic deletion of unconfirmed users.
 * 
 * @author DeineStrainReviews.de Development Team
 * @copyright 2025 DeineStrainReviews.de
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */
class DeleteUnconfirmedUsersCronjob extends AbstractCronjob {
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob) {
        parent::execute($cronjob);

        // Get options
        $resendEmailDays = (int) AUTO_DELETE_UNCONFIRMED_USERS_RESEND_EMAIL_DAYS;
        $deleteDays = (int) AUTO_DELETE_UNCONFIRMED_USERS_DAYS;
        $limit = (int) AUTO_DELETE_UNCONFIRMED_USERS_LIMIT;

        if ($resendEmailDays > 0) {
            // Step 1: Delete users who received a resend and are Y days old after resend (priority)
            $this->deleteUsersAfterResend($deleteDays, $limit);

            // Step 2: Resend confirmation emails to users who are X days old
            $this->resendConfirmationEmails($resendEmailDays, $limit);
        } else {
            // Direct deletion without resending email (old behavior)
            $this->deleteUsersDirectly($deleteDays, $limit);
        }
    }

    /**
     * Resends confirmation emails to unconfirmed users.
     */
    protected function resendConfirmationEmails($resendEmailDays, $limit) {
        // Calculate the timestamp threshold (86400 seconds = 1 day)
        $timeLimit = TIME_NOW - ($resendEmailDays * 86400);

        // Find users who are X days old, unconfirmed, and haven't received a resend yet
        // Check in the resent_activation_email_log table if a resend email was already sent
        $sql = "SELECT u.userID, u.username, u.email, u.registrationDate, u.emailConfirmed
                FROM wcf1_user u
                INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
                LEFT JOIN wcf1_resent_activation_email_log log ON log.userID = u.userID
                WHERE u.emailConfirmed IS NOT NULL
                AND u.registrationDate < ?
                AND log.logID IS NULL
                GROUP BY u.userID, u.username, u.email, u.registrationDate, u.emailConfirmed
                HAVING COUNT(DISTINCT ug.groupID) <= 2 
                AND SUM(ug.groupID NOT IN (?, ?)) = 0
                LIMIT ?";

        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$timeLimit, 1, 2, $limit]);
        $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($users)) {
            return; // No matching users found
        }

        // Get delete days configuration for the warning message
        $deleteDays = (int) AUTO_DELETE_UNCONFIRMED_USERS_DAYS;
        
        // Send custom reminder emails
        $resendDate = TIME_NOW;
        $successfulSends = [];
        
        foreach ($users as $user) {
            try {
                // Send custom reminder email with deletion warning
                $this->sendReminderEmail($user, $deleteDays);
                $successfulSends[] = $user;
            } catch (\Exception $e) {
                // Log error for individual user but continue with others
                \wcf\functions\exception\logThrowable($e);
            }
        }
        
        // Only create log entries for successfully sent emails
        foreach ($successfulSends as $user) {
            try {
                // Create log entry in resent_activation_email_log table
                ResentActivationEmailLogEditor::create([
                    'userID' => $user['userID'],
                    'registrationDate' => $user['registrationDate'],
                    'resendEmailDate' => $resendDate
                ]);
            } catch (\Exception $e) {
                // Log error for individual user but continue with others
                \wcf\functions\exception\logThrowable($e);
            }
        }
        
        // Send notification to administrators if enabled
        if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL_RESENT && !empty($successfulSends)) {
            // Prepare anonymized user list for email
            $anonymizedUsers = [];
            foreach ($successfulSends as $user) {
                $anonymizedUsers[] = [
                    'username' => $this->anonymizeUsername($user['username']),
                    'email' => $this->anonymizeEmail($user['email']),
                    'registrationDate' => $user['registrationDate'],
                    'resendEmailDate' => $resendDate
                ];
            }
            $this->notifyAdministratorsResent(count($successfulSends), $anonymizedUsers);
        }
    }
    
    /**
     * Sends a reminder email with deletion warning.
     */
    protected function sendReminderEmail($userData, $deleteDays) {
        $user = new User($userData['userID']);
        
        // Generate activation link
        $activationLink = LinkHandler::getInstance()->getLink('RegisterActivation', [
            'u' => $user->userID,
            'a' => $user->emailConfirmed
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
            'contactLink' => $contactLink
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
            new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', $messagePlaintext)
        ]));
        
        $email->send();
    }

    /**
     * Deletes users directly without resending email.
     */
    protected function deleteUsersDirectly($deleteDays, $limit) {
        // Calculate the timestamp threshold (86400 seconds = 1 day)
        $timeLimit = TIME_NOW - ($deleteDays * 86400);

        // Find users who are unconfirmed and ONLY in groups 1 (Everyone) and 2 (Guests)
        // Note: In WoltLab, emailConfirmed is NOT NULL for unconfirmed users (contains hash/value),
        // while confirmed users have emailConfirmed = NULL
        $sql = "SELECT u.userID, u.username, u.email, u.registrationDate
                FROM wcf1_user u
                INNER JOIN wcf1_user_to_group ug ON ug.userID = u.userID
                LEFT JOIN wcf1_deleted_unconfirmed_user_log log ON log.userID = u.userID AND log.deletionDate > 0
                WHERE u.emailConfirmed IS NOT NULL
                AND u.registrationDate < ?
                AND log.logID IS NULL
                GROUP BY u.userID, u.username, u.email, u.registrationDate
                HAVING COUNT(DISTINCT ug.groupID) <= 2 
                AND SUM(ug.groupID NOT IN (?, ?)) = 0
                LIMIT ?";

        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$timeLimit, 1, 2, $limit]);
        $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($users)) {
            return; // No matching users found
        }

        // Extract user IDs for deletion
        $userIDs = array_column($users, 'userID');

        // Create log entries before deletion
        $deletionDate = TIME_NOW;
        foreach ($users as $user) {
            DeletedUnconfirmedUserLogEditor::create([
                'userID' => $user['userID'],
                'username' => $this->anonymizeUsername($user['username']),
                'email' => $this->anonymizeEmail($user['email']),
                'registrationDate' => $user['registrationDate'],
                'deletionDate' => $deletionDate
            ]);
        }

        // Delete users via UserAction
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();

        // Send notification to administrators if enabled
        if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL_DELETED) {
            // Prepare anonymized user list for email
            $anonymizedUsers = [];
            foreach ($users as $user) {
                $anonymizedUsers[] = [
                    'username' => $this->anonymizeUsername($user['username']),
                    'email' => $this->anonymizeEmail($user['email']),
                    'registrationDate' => $user['registrationDate'],
                    'deletionDate' => $deletionDate
                ];
            }
            $this->notifyAdministrators(count($users), $anonymizedUsers);
        }
    }

    /**
     * Deletes users who received a resend email.
     */
    protected function deleteUsersAfterResend($deleteDays, $limit) {
        // Calculate the timestamp threshold (86400 seconds = 1 day)
        $timeLimit = TIME_NOW - ($deleteDays * 86400);

        // Find users who received a resend email and are Y days old after resend
        // Check in resent_activation_email_log table for resend date
        // Also check that user is not already in deleted_unconfirmed_user_log
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
        $users = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($users)) {
            return; // No matching users found
        }

        // Extract user IDs for deletion
        $userIDs = array_column($users, 'userID');

        // Create log entries in deleted_unconfirmed_user_log before deletion
        $deletionDate = TIME_NOW;
        foreach ($users as $user) {
            DeletedUnconfirmedUserLogEditor::create([
                'userID' => $user['userID'],
                'username' => $this->anonymizeUsername($user['username']),
                'email' => $this->anonymizeEmail($user['email']),
                'registrationDate' => $user['registrationDate'],
                'deletionDate' => $deletionDate
            ]);
        }

        // Delete users via UserAction
        $userAction = new UserAction($userIDs, 'delete');
        $userAction->executeAction();

        // Send notification to administrators if enabled
        if (AUTO_DELETE_UNCONFIRMED_USERS_SEND_EMAIL_DELETED) {
            // Prepare anonymized user list for email
            $anonymizedUsers = [];
            foreach ($users as $user) {
                $anonymizedUsers[] = [
                    'username' => $this->anonymizeUsername($user['username']),
                    'email' => $this->anonymizeEmail($user['email']),
                    'registrationDate' => $user['registrationDate'],
                    'deletionDate' => $deletionDate
                ];
            }
            $this->notifyAdministrators(count($users), $anonymizedUsers);
        }
    }

    /**
     * Sends email notification to administrators about deleted users.
     */
    protected function notifyAdministrators($deletedCount, $anonymizedUsers = []) {
        if ($deletedCount == 0) {
            return;
        }

        // Get all administrators using UserGroup::isAdminGroup()
        $adminGroupIDs = [];
        // Get all groups (not excluding OWNER groups) and check if they are admin groups
        foreach (UserGroup::getGroupsByType() as $group) {
            if ($group->isAdminGroup()) {
                $adminGroupIDs[] = $group->groupID;
            }
        }
        
        if (empty($adminGroupIDs)) {
            return;
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
        $administrators = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($administrators)) {
            return;
        }

        // Generate log page URL
        try {
            $logPageUrl = LinkHandler::getInstance()->getLink('DeletedUnconfirmedUsersLog', [], '', true);
        } catch (\Exception $e) {
            $logPageUrl = '';
        }
        
        // Format user list for email
        $userListHtml = $this->formatUserListForEmail($anonymizedUsers);
        
        // Send email to each administrator
        foreach ($administrators as $admin) {
            try {
                $subject = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedUnconfirmedUsers.subject', ['count' => $deletedCount]);
                
                $message = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.deletedUnconfirmedUsers.message', [
                    'count' => $deletedCount,
                    'username' => $admin['username'],
                    'logPageUrl' => $logPageUrl,
                    'userList' => $userListHtml
                ]);
                
                $adminUser = new User($admin['userID']);
                $email = new Email();
                $email->addRecipient(new UserMailbox($adminUser));
                $email->setSubject($subject);
                
                $email->setBody(new MimePartFacade([
                    new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $message),
                    new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', strip_tags($message))
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
     */
    protected function notifyAdministratorsResent($resentCount, $anonymizedUsers = []) {
        if ($resentCount == 0) {
            return;
        }

        // Get all administrators using UserGroup::isAdminGroup()
        $adminGroupIDs = [];
        // Get all groups (not excluding OWNER groups) and check if they are admin groups
        foreach (UserGroup::getGroupsByType() as $group) {
            if ($group->isAdminGroup()) {
                $adminGroupIDs[] = $group->groupID;
            }
        }
        
        if (empty($adminGroupIDs)) {
            return;
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
        $administrators = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($administrators)) {
            return;
        }

        // Generate log page URL
        try {
            $logPageUrl = LinkHandler::getInstance()->getLink('ResentActivationEmailLog', [], '', true);
        } catch (\Exception $e) {
            $logPageUrl = '';
        }
        
        // Format user list for email
        $userListHtml = $this->formatUserListForEmailResent($anonymizedUsers);
        
        // Send email to each administrator
        foreach ($administrators as $admin) {
            try {
                $subject = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.resentActivationEmails.subject', ['count' => $resentCount]);
                
                $message = WCF::getLanguage()->getDynamicVariable('wcf.acp.notification.resentActivationEmails.message', [
                    'count' => $resentCount,
                    'username' => $admin['username'],
                    'logPageUrl' => $logPageUrl,
                    'userList' => $userListHtml
                ]);
                
                $adminUser = new User($admin['userID']);
                $email = new Email();
                $email->addRecipient(new UserMailbox($adminUser));
                $email->setSubject($subject);
                
                $email->setBody(new MimePartFacade([
                    new RecipientAwareTextMimePart('text/html', 'email_html', 'wcf', $message),
                    new RecipientAwareTextMimePart('text/plain', 'email_plaintext', 'wcf', strip_tags($message))
                ]));
                
                $email->send();
            } catch (\Exception $e) {
                // Log error but continue with other administrators
                \wcf\functions\exception\logThrowable($e);
            }
        }
    }

    /**
     * Anonymizes a username for GDPR compliance.
     */
    protected function anonymizeUsername($username) {
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
     */
    protected function anonymizeEmail($email) {
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

    /**
     * Formats the anonymized user list as HTML table for email notification.
     */
    protected function formatUserListForEmail($anonymizedUsers) {
        if (empty($anonymizedUsers)) {
            return '';
        }

        $html = '<table style="border-collapse: collapse; width: 100%; margin: 20px 0;">';
        $html .= '<thead><tr style="background-color: #f0f0f0; border-bottom: 2px solid #ddd;">';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.username') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.email') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.registrationDate') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.deletedUnconfirmedUsersLog.deletionDate') . '</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($anonymizedUsers as $user) {
            $registrationDate = date('Y-m-d H:i:s', $user['registrationDate']);
            $deletionDate = date('Y-m-d H:i:s', $user['deletionDate']);
            $html .= '<tr style="border-bottom: 1px solid #ddd;">';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($registrationDate, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($deletionDate, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Formats the anonymized user list as HTML table for resent activation email notification.
     */
    protected function formatUserListForEmailResent($anonymizedUsers) {
        if (empty($anonymizedUsers)) {
            return '';
        }

        $html = '<table style="border-collapse: collapse; width: 100%; margin: 20px 0;">';
        $html .= '<thead><tr style="background-color: #f0f0f0; border-bottom: 2px solid #ddd;">';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.resentActivationEmailLog.username') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.resentActivationEmailLog.email') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.resentActivationEmailLog.registrationDate') . '</th>';
        $html .= '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">' . WCF::getLanguage()->get('wcf.acp.resentActivationEmailLog.resendEmailDate') . '</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($anonymizedUsers as $user) {
            $registrationDate = date('Y-m-d H:i:s', $user['registrationDate']);
            $resendEmailDate = date('Y-m-d H:i:s', $user['resendEmailDate']);
            $html .= '<tr style="border-bottom: 1px solid #ddd;">';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($registrationDate, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($resendEmailDate, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }
}
