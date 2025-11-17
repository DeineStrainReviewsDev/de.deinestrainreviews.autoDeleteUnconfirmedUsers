# Auto Delete Unconfirmed Users

A WoltLab Suite plugin that automatically deletes users who have not confirmed their email address within a configurable time period.

---

## Features

### Core Functionality
- **Automatic deletion** of unconfirmed users via daily cronjob
- **Configurable time period** (1-365 days) before deletion
- **Batch processing** with configurable limit (1-50 users per run) to reduce server load
- **Safety checks** - Only deletes users in default groups (Everyone/Guests), never administrators or users with special groups

### Logging & Notifications (v1.2.0+)
- **Comprehensive logging** - All deleted users are logged to a database table
- **ACP log page** - View all deleted users in the administration panel
- **Email notifications** - Administrators receive email notifications when users are deleted
- **Multilingual support** - Logging and notifications available in German & English

### Multilingual Support
- German language files
- English language files
- All user-facing strings are translatable

---

## Admin Panel Options

The plugin adds the following settings in **WoltLab ACP → Configuration → Options → User**:

### Enable Automatic Deletion of Unconfirmed Users
If enabled, unconfirmed users will be automatically deleted after the specified number of days.

**Default:** Disabled

### Days Until Unconfirmed Users Are Deleted
The number of days after which unconfirmed users will be removed.

**Default:** 7 days  
**Range:** 1-365 days

### Users Per Cron Job Execution (v1.1.0+)
Maximum number of users that can be deleted per cronjob execution. This helps reduce server load when many users need to be deleted.

**Default:** 10 users  
**Range:** 1-50 users

---

## ACP Log Page (v1.2.0+)

The plugin provides a log page to view all deleted unconfirmed users:

**Access:** ACP → User Management → Deleted Unconfirmed Users

The log page displays:
- User ID
- Username
- Email address
- Registration date
- Deletion date

All entries are sortable and the page supports pagination.

---

## Email Notifications (v1.2.0+)

Administrators automatically receive email notifications when users are deleted. The notifications include:
- Number of deleted users
- Link to the log page in the ACP

**Note:** Only users with administrator permissions receive notifications.

---

## Installation

### Requirements
- **WoltLab Suite:** 6.1.0 or higher
- **PHP:** Version compatible with WoltLab Suite
- **MySQL/MariaDB:** Version compatible with WoltLab Suite

### Installation Steps

1. **Download the latest release**
   - Go to the [Releases](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases) page
   - Download the `.tar.gz` file (e.g., `de.deinestrainreviews.autoDeleteUnconfirmedUsers-1.2.0.tar.gz`)

2. **Install via ACP**
   - Go to **ACP → Package Management → Upload Package**
   - Upload the downloaded `.tar.gz` file
   - Follow the installation wizard

3. **Configure the plugin**
   - Go to **ACP → Configuration → Options → User**
   - Enable "Enable automatic deletion of unconfirmed users"
   - Configure the number of days until deletion
   - (Optional) Configure the limit for users per cronjob execution

4. **Verify cronjob**
   - The cronjob runs daily at midnight
   - You can check cronjob execution in **ACP → System → Cronjobs**

---

## How It Works

### User Detection
The plugin identifies unconfirmed users by checking:
- `emailConfirmed IS NOT NULL` (in WoltLab, unconfirmed users have a hash/value, confirmed users have NULL)
- `registrationDate` older than the configured number of days
- User must be exclusively in default groups (ID 1: Everyone, ID 2: Guests)

### Safety Features
- **Group membership check** - Only users in default groups are deleted
- **Administrator protection** - Administrators are never deleted
- **Batch limit** - Maximum number of deletions per run prevents server overload
- **Logging** - All deletions are logged for audit purposes

### Cronjob Schedule
- **Frequency:** Daily
- **Execution time:** Midnight (00:00)
- **Limit:** Configurable (default: 10 users per run)

---

## Updates & Changelog

For a complete list of changes, see [CHANGELOG.md](CHANGELOG.md).

### Recent Versions

**Version 1.2.0** (2025-11-17)
- Added logging functionality
- Added ACP log page
- Added email notifications for administrators
- Improved code quality and documentation
- Bug fixes (SQL IN clause, ACP page structure)

**Version 1.1.1** (2025-03-06)
- Fixed group membership check logic
- Improved security and precision

**Version 1.1.0** (2025-02-25)
- Added configurable limit for users per cronjob execution
- Improved performance and server load handling

**Version 1.0.0** (2025-02-20)
- Initial release

---

## Development

### Code Quality
- Follows WoltLab development standards
- All code comments and PHPDoc in English
- Uses WoltLab standard patterns (DatabaseObjectEditor, SortablePage)
- Comprehensive error handling and logging

### File Structure
```
de.deinestrainreviews.autoDeleteUnconfirmedUsers/
├── files/
│   └── lib/
│       ├── acp/
│       │   └── page/
│       │       ├── DeletedUnconfirmedUsersLogPage.class.php
│       │       └── DeletedUnconfirmedUsersLogPage.tpl
│       ├── data/
│       │   └── deleted/
│       │       └── unconfirmed/
│       │           └── user/
│       │               └── log/
│       │                   ├── DeletedUnconfirmedUserLog.class.php
│       │                   ├── DeletedUnconfirmedUserLogList.class.php
│       │                   └── DeletedUnconfirmedUserLogEditor.class.php
│       └── system/
│           └── cronjob/
│               └── DeleteUnconfirmedUsersCronjob.class.php
├── language/
│   ├── de.xml
│   └── en.xml
├── acpMenu.xml
├── cronjob.xml
├── install.sql
├── option.xml
├── page.xml
├── package.xml
├── CHANGELOG.md
├── LICENSE
└── README.md
```

---

## Support

- **Issues:** [GitHub Issues](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/issues)
- **Releases:** [GitHub Releases](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases)
- **Repository:** [GitHub Repository](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers)

---

## License

This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See the [LICENSE](LICENSE) file for details.

---

## Credits

- **Author:** DeineStrainReviews.de
- **Website:** https://deinestrainreviews.de
- **Copyright:** 2025 DeineStrainReviews.de

---

## Acknowledgments

This plugin follows WoltLab Suite development standards and best practices.  
Thanks to the WoltLab community for their excellent documentation and support.

---

**Last Updated:** 2025-11-17
