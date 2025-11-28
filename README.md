# Auto-Delete Unconfirmed Users

> 🌐 **Language / Sprache**: [English](#readme) | [Deutsch](README_DE.md)

A WoltLab plugin that automatically deletes unconfirmed users after a configurable period. Features a two-stage deletion workflow with optional reminder emails and comprehensive logging.

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![WoltLab Plugin Store](https://img.shields.io/badge/WoltLab-Plugin%20Store-orange)](https://www.woltlab.com/)

## 🚀 Current Version: 1.4.0

### Key Features

- ✅ **Email Reputation Protection** - NEW in v1.4.0: Protect your server from bounces and spam traps
- ✅ **Silent Legacy Deletion** - Automatically clean up old "ghost accounts" without risking your email reputation
- ✅ **Two-Stage Deletion Workflow** - Optional reminder email before deletion
- ✅ **Comprehensive Logging** - Track deleted users and sent reminder emails
- ✅ **GDPR-Compliant** - Automatic data anonymization
- ✅ **Email Notifications** - Keep admins informed about deletions and reminders
- ✅ **Flexible Configuration** - Customize timing and behavior
- ✅ **Multilingual** - Full support for German and English

<details>
<summary><strong>📋 Full Feature List</strong></summary>

### Core Features (v1.0.0+)
- Automatic deletion of unconfirmed users via cron job
- Configurable deletion period
- Only deletes users exclusively in "Guests" group (ID 2)
- Preserves user content (posts, attachments, conversations)
- Multilingual support (German & English)

### Enhanced Features (v1.1.0+)
- Configurable batch size (max 50 users per cron run)
- Server load optimization

### Logging & Notifications (v1.2.0+)
- ACP log page for deleted users
- Sortable log table (ID, username, email, dates)
- Database logging with permanent storage
- Email notifications to administrators
- GDPR-compliant data anonymization

### Two-Stage Workflow (v1.3.0+)
- Optional reminder email before deletion
- Separate log page for resent activation emails
- Split notification settings (reminder vs. deletion)
- Intelligent contact form detection
- Professional HTML email templates

### Email Reputation Protection (v1.4.0+)
- Configurable maximum age threshold for email sending
- Silent deletion mode for legacy accounts (no email sent)
- Safety quarantine option (ignore old accounts)
- Separate admin notifications for legacy deletions
- Automatic filtering of risky accounts from reminder workflow
- Legacy Account Log page for managing old accounts
- Bulk deletion actions via clipboard

</details>

## 📦 Installation

1. Download the latest release from the [WoltLab Plugin Store](https://www.woltlab.com/) or [GitHub Releases](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases)
2. Upload the `.tar.gz` file via WoltLab ACP
3. Configure settings under **ACP → Configuration → Options → Users → Registration**
4. Set permissions for user groups that should access the log pages

## ⚙️ Configuration

### Location
**ACP → Configuration → Options → Users → Registration**

### Available Options

| Option | Description | Default |
|--------|-------------|---------|
| **Enable automatic deletion** | Activate the automatic deletion feature | Disabled |
| **Days until resending activation email** | Days before sending reminder email (0 = disabled, direct deletion) | 0 |
| **Days until deletion** | Days after reminder before final deletion | 7 |
| **Users per cron execution** | Maximum users processed per run (max 50) | 10 |
| **Email notification (reminders)** | Notify admins when reminder emails are sent | Enabled |
| **Email notification (deletions)** | Notify admins when users are deleted | Enabled |
| **Maximum age for email sending** ⭐ NEW | Maximum age (days) for accounts that receive emails (0 = disabled) | 0 |
| **Silent deletion of Legacy Accounts** ⭐ NEW | Delete old accounts without email to protect reputation | Disabled |

<details>
<summary><strong>🔧 Configuration Examples</strong></summary>

### Example 1: Two-Stage Process (Recommended)
```
Days until resending activation email: 7
Days until deletion: 7
```
**Result:** User registers → After 7 days: reminder email → After another 7 days (total 14): deletion

### Example 2: Direct Deletion (Legacy Mode)
```
Days until resending activation email: 0
Days until deletion: 7
```
**Result:** User registers → After 7 days: direct deletion (no reminder)

### Example 3: Extended Grace Period
```
Days until resending activation email: 14
Days until deletion: 14
```
**Result:** User registers → After 14 days: reminder email → After another 14 days (total 28): deletion

### Example 4: With Reputation Protection (v1.4.0+) 🛡️
```
Days until resending activation email: 7
Days until deletion: 7
Maximum age for email sending: 365
Silent deletion of Legacy Accounts: Enabled
```
**Result:** 
- Recent users (< 365 days): Normal workflow with reminder → deletion after 14 days
- Legacy users (> 365 days): **Silently deleted** immediately (no email sent to protect reputation)

</details>

## 📊 Log Pages

### Deleted Users Log
**Location:** ACP → Users → Deleted Unconfirmed Users

View all deleted users with:
- User ID
- Anonymized username
- Anonymized email
- Registration date
- Deletion date
- Deletion type (Automatic/Silent)

**Search & Filter Features (v1.4.0+):**
- Filter by username (partial match)
- Filter by email (partial match)
- Filter by user ID
- Filter by deletion type (Automatic/Silent)
- Sortable columns with persistent filters

### Resent Activation Emails Log
**Location:** ACP → Users → Resent Activation Emails

View all sent reminder emails with:
- User ID
- Anonymized username
- Anonymized email
- Registration date
- Resend date

**Search & Filter Features (v1.4.0+):**
- Filter by username (partial match)
- Filter by user ID
- Sortable columns with persistent filters

### Legacy Account Log ⭐ NEW (v1.4.0+)
**Location:** ACP → Users → Legacy Account Log

View all detected legacy accounts (old unconfirmed users) with:
- User ID (clickable to copy)
- Username
- Email
- Registration date
- Detection date (when identified as legacy)

**Features:**
- Filter by user ID
- Filter by registration date range
- Filter by detection date range
- Bulk deletion via clipboard actions
- Sortable columns with persistent filters
- Shows only pending accounts (not yet deleted)

### Permissions
Set viewing permissions under:
**ACP → User Groups → [Select Group] → Administrative Rights → Users**

Permission: *Can view logs of automatic deletion of unconfirmed users*

## 🔒 GDPR Compliance

All personal data is automatically anonymized before storage and in email notifications.

<details>
<summary><strong>📝 Anonymization Details</strong></summary>

### Username Anonymization

| Length | Pattern | Example |
|--------|---------|---------|
| ≤ 4 chars | Fully masked | `test` → `****` |
| 5-8 chars | First 2 + mask + last 2 | `username` → `us***me` |
| 9+ chars | First 2 + mask + last 4 | `johnsmith123` → `jo***h123` |

### Email Anonymization

**Pattern:** First char of local part + mask @ first 2 chars of domain + mask . first char(s) of TLD + mask

**Examples:**
- `testuser@example.com` → `t***@ex***.co***`
- `admin@domain.org` → `a***@do***.o***`

### Where Applied
1. Database log entries
2. Email notifications to administrators
3. ACP log page displays

</details>

## 📝 Changelog

<details>
<summary><strong>Version 1.4.0 (2025-11-24)</strong> - Latest Release</summary>

### ✨ New Features
- **Email Reputation Protection System**
  - Maximum registration age threshold to identify "risky" legacy accounts
  - Silent deletion mode for legacy accounts (no email sent)
  - Safety quarantine option to ignore old accounts without deletion
  - Intelligent filtering excludes legacy accounts from reminder workflow
- **Enhanced Admin Notifications**
  - Separate notification emails for legacy account deletions
  - Detailed reporting with age threshold and protection rationale

### 🔧 Technical Changes
- New configuration option: `auto_delete_unconfirmed_users_max_registration_age`
- New configuration option: `auto_delete_unconfirmed_users_delete_legacy`
- Extended `UnconfirmedUserService` with legacy account handling
- New method in `DSRUnconfirmedUserMailService` for legacy deletion notifications
- Updated language files (EN/DE) with reputation protection terminology
- New `LegacyAccountService` for handling legacy account operations
- New `DSRGdprAnonymizer` utility class for GDPR-compliant data anonymization
- Refactored cronjob architecture with service-oriented design
- Added search and filter functionality to all ACP log pages
- New `LegacyAccountLog` database table and data classes
- Template-based email system for better maintainability

### 📦 Release
- [Full Changelog v1.4.0](CHANGELOG_1.4.0_EN.md)

</details>

<details>
<summary><strong>Version 1.3.0 (2025-11-21)</strong></summary>

### ✨ New Features
- Two-stage deletion workflow with optional reminder emails
- New database table for tracking resent activation emails
- Separate ACP log page for reminder emails
- Split email notification settings (reminder vs. deletion)
- Professional HTML email templates
- Intelligent contact form module detection
- Enhanced admin notifications for both workflow stages

### 🔧 Technical Changes
- Added `wcf1_resent_activation_email_log` table
- Migration script for v1.2.0 → v1.3.0 upgrade
- New configuration options for two-stage workflow
- Updated language files (EN/DE)

### 📦 Release
- Approved and published in WoltLab Plugin Store

</details>

<details>
<summary><strong>Version 1.2.0 (2024)</strong></summary>

### ✨ New Features
- ACP log page for deleted users
- Sortable log table
- Database logging with permanent storage
- Email notifications to administrators
- GDPR-compliant data anonymization

### 🔧 Technical Changes
- Added `wcf1_deleted_unconfirmed_user_log` table
- New permission system for log access
- Anonymization algorithms for usernames and emails

</details>

<details>
<summary><strong>Version 1.1.0 (2024)</strong></summary>

### ✨ New Features
- Configurable batch size (max 50 users per run)
- Server load optimization

</details>

<details>
<summary><strong>Version 1.0.0 (2024)</strong></summary>

### ✨ Initial Release
- Automatic deletion of unconfirmed users
- Configurable deletion period
- Group-based filtering (Guests only)
- Multilingual support (DE/EN)

</details>

## 🔮 Planned Features

- **Optional Content Deletion** - Configurable removal of all user-generated content (posts, attachments, etc.)

## ⚠️ Important Notes

- Only users **exclusively** in the "Guests" group (ID 2) are deleted
- Users with additional group memberships are **preserved**
- User content (posts, attachments, conversations) **remains** in the system
- The cron job must be properly configured in WoltLab

## 📄 License

This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See the [LICENSE](LICENSE) file for details.

## 🔗 Links

- [WoltLab Plugin Store](https://www.woltlab.com/)
- [GitHub Repository](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers)
- [Report Issues](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/issues)

---

**Made with ❤️ for the WoltLab Community**
