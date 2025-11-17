# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- No planned changes

---

## [1.2.0] - 2025-11-17

### ✨ Added
- **Logging functionality for deleted users**
  - New database table `wcf1_deleted_unconfirmed_user_log` for log entries
  - Added `DeletedUnconfirmedUserLog` DatabaseObject class
  - Added `DeletedUnconfirmedUserLogList` DatabaseObjectList class
  - Added `DeletedUnconfirmedUserLogEditor` DatabaseObjectEditor class (follows WoltLab standards)

- **ACP page for log display**
  - New ACP page `DeletedUnconfirmedUsersLogPage` to display deleted users
  - Extends `SortablePage` (follows WoltLab standards)
  - Added template `DeletedUnconfirmedUsersLogPage.tpl`
  - Added ACP menu item "Deleted Unconfirmed Users"
  - Accessible under: ACP → User Management → Deleted Unconfirmed Users

- **Email notifications for administrators**
  - Administrators automatically receive email notifications when users are deleted
  - Dynamic admin detection using `UserGroup::isAdminGroup()`
  - Multilingual email templates (German & English)
  - Pluralization for number of deleted users

- **Language files**
  - Added German language variables for logging and notifications
  - Added English language variables for logging and notifications
  - Added language variables for ACP menu and log page

### 🔧 Improved
- **Cronjob logic**
  - Improved admin detection: Now uses `UserGroup::isAdminGroup()` instead of manual query
  - Optimized SQL queries: Uses `PreparedStatementConditionBuilder` for `IN (?)` array handling
  - Log entries are created before user deletion

- **Code quality**
  - All code comments and PHPDoc blocks translated from German to English
  - Cronjob description translated to English
  - Code now fully follows WoltLab standards

### 🐛 Fixed
- **SQL IN clause with arrays**
  - Problem: PDO does not automatically expand `IN (?)` for arrays
  - Solution: Usage of `PreparedStatementConditionBuilder` (WoltLab standard)
  - Affected file: `DeleteUnconfirmedUsersCronjob.class.php`

- **ACP page structure**
  - Problem: ACP page extended `AbstractAcpPage` instead of `SortablePage`
  - Solution: Changed to `SortablePage` for better WoltLab standards compliance
  - Affected file: `DeletedUnconfirmedUsersLogPage.class.php`

- **Logging mechanism**
  - Problem: Custom `DeletedUnconfirmedUserLogHandler` class used
  - Solution: Replaced with standard `DatabaseObjectEditor::create()` pattern
  - Affected file: Custom handler removed, replaced with Editor

### 📝 Documentation
- All code comments translated to English
- PHPDoc blocks expanded and translated to English
- Improved internal code documentation

### 🔄 Technical Changes
- **package.xml**
  - Version bumped to 1.2.0
  - Added new PIP instructions: `sql`, `acpMenu`, `page`
  - Added update instructions for version 1.2.0

- **install.sql**
  - Added new database table `wcf1_deleted_unconfirmed_user_log`
  - Added indexes for `logID`, `userID` and `deletionDate`

- **New files**
  - `files/lib/data/deleted/unconfirmed/user/log/DeletedUnconfirmedUserLog.class.php`
  - `files/lib/data/deleted/unconfirmed/user/log/DeletedUnconfirmedUserLogList.class.php`
  - `files/lib/data/deleted/unconfirmed/user/log/DeletedUnconfirmedUserLogEditor.class.php`
  - `files/lib/acp/page/DeletedUnconfirmedUsersLogPage.class.php`
  - `files/lib/acp/page/DeletedUnconfirmedUsersLogPage.tpl`
  - `page.xml`
  - `acpMenu.xml`

- **Modified files**
  - `files/lib/system/cronjob/DeleteUnconfirmedUsersCronjob.class.php` (Logging, Notifications, SQL fix)
  - `language/de.xml` (New language variables)
  - `language/en.xml` (New language variables)
  - `cronjob.xml` (Description translated to English)
  - `package.xml` (Version, new PIPs)

---

## [1.1.1] - 2025-03-06

### 🐛 Fixed
- **Group membership of unconfirmed users**
  - Problem: Incorrect logic when checking group membership
  - Solution: Corrected SQL query for group membership
  - Result: Now only users from the "Guests" group (ID 2) are considered for automatic deletion
  - Additionally: Users must exclusively be in groups "Everyone" (ID 1) and "Guests" (ID 2)

### 🔧 Improved
- More precise user selection for deletion
- Improved security through stricter group membership check

### 🔄 Technical Changes
- SQL query in `DeleteUnconfirmedUsersCronjob.class.php` adjusted
- Added HAVING clause for group membership check

---

## [1.1.0] - 2025-02-25

### ✨ Added
- **New option: Users per cron job execution**
  - Configurable limit for the number of users to be deleted per cron job run
  - Default value: 10 users per run
  - Minimum value: 1 user
  - Maximum value: 50 users
  - Purpose: Reduction of server load with large user numbers
  - Accessible under: ACP → Configuration → Options → User → Users per cron job execution

### 🔧 Improved
- Cronjob performance through limited batch processing
- Server load reduction when many users need to be deleted

### 🔄 Technical Changes
- **option.xml**
  - Added new option `auto_delete_unconfirmed_users_limit`
  - Option linked with `enable_auto_delete_unconfirmed_users`

- **DeleteUnconfirmedUsersCronjob.class.php**
  - Added LIMIT clause to SQL query
  - Uses new option as limit

- **Language files**
  - Added new language variables for limit option (German & English)

---

## [1.0.0] - 2025-02-20

### ✨ Added
- **Initial release**
  - Automatic deletion of unconfirmed users after configurable time period
  - Daily cronjob to check unconfirmed users
  - Configurable number of days until deletion
  - Default value: 7 days

### 🎯 Features
- **Cronjob functionality**
  - Daily execution at midnight
  - Automatic detection of unconfirmed users
  - Deletion only of users in default groups (Everyone/Guests)

- **ACP options**
  - Enable/disable automatic deletion
  - Configurable number of days until deletion (1-365 days)
  - Accessible under: ACP → Configuration → Options → User

- **Multilingual support**
  - German language files
  - English language files

### 🔄 Technical Details
- **Files**
  - `files/lib/system/cronjob/DeleteUnconfirmedUsersCronjob.class.php` - Cronjob implementation
  - `cronjob.xml` - Cronjob definition
  - `option.xml` - ACP options
  - `package.xml` - Package definition
  - `language/de.xml` - German language variables
  - `language/en.xml` - English language variables

- **Database integration**
  - Uses WoltLab Core tables (`wcf1_user`, `wcf1_user_to_group`)
  - SQL query for unconfirmed users
  - Integration with WoltLab UserAction for deletion

- **Security**
  - Deletion only of users in default groups
  - No deletion of administrators or users with special groups
  - Integration with WoltLab permission system

---

## Version History Summary

| Version | Date | Type | Main Change |
|---------|------|------|-------------|
| 1.2.0 | 2025-11-17 | Feature | Logging, email notifications, ACP page |
| 1.1.1 | 2025-03-06 | Bugfix | Group membership logic corrected |
| 1.1.0 | 2025-02-25 | Feature | Limit option for cronjob execution |
| 1.0.0 | 2025-02-20 | Initial | Initial release |

---

## Change Types

- **✨ Added**: New features
- **🔧 Improved**: Improvements to existing features
- **🐛 Fixed**: Bug fixes
- **📝 Documentation**: Documentation updates
- **🔄 Technical Changes**: Technical changes without visible impact for end users
- **🔒 Security**: Security-related changes

---

## Additional Information

- **Repository**: https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers
- **License**: GNU General Public License v3.0 (GPL-3.0)
- **WoltLab Suite**: Compatible with version 6.1.0 and higher

---

**Last Updated**: 2025-11-17
