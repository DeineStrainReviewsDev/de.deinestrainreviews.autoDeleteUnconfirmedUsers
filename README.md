# de.deinestrainreviews.autoDeleteUnconfirmedUsers

> 🌐 **Language / Sprache**: [English](#readme) | [Deutsch](README_DE.md)

A WoltLab plugin that automatically deletes unconfirmed users after a configurable period.

## Features

### ✅ Fully Functional (Version 1.1.1+)
- Periodic check for unconfirmed users via cron job
- Automatic deletion after a configurable period
- Only deletes users who are exclusively in the "Guests" group (ID 2)
- Users with additional group memberships are preserved
- ⚠️ **Important**: Only users are deleted - their content (posts, attachments, conversations, etc.) remains in the system
- Multilingual support (German & English)
- Admin panel settings to configure deletion time
- Configurable maximum number of users deleted per cron job execution (max 50 per run)

### ✅ Fully Functional (Version 1.2.0)
- **ACP Log Page** - View all deleted unconfirmed users in the administration panel
- **Sortable Log Table** - Sort by ID, username, email, registration date, or deletion date
- **Database Logging** - Log entries are stored in the database
- **Automatic Log Entry Creation** - Log entries are created automatically during deletion
- **Email Notifications** - Administrators receive email notifications when users are deleted
- **GDPR-Compliant Data Anonymization** - All personal data (usernames and email addresses) are automatically anonymized before storage and in email notifications

### 📋 Planned Features
- **Optional Content Deletion** - An optional feature to completely remove all content of deleted users (posts, attachments, etc.). This feature will be configurable, allowing administrators to decide whether content should remain for better readability of threads or be completely removed.
- **Resend Confirmation Email with Deletion Warning** - Resend the confirmation email with a notice about the upcoming deletion. This could help activate users who simply forgot to click the confirmation link.

## Admin Panel Options
The plugin adds the following settings in the **WoltLab ACP (Administration Control Panel)**:

**Location**: ACP → Configuration → Options → User → Registration

- **Enable automatic deletion of unconfirmed users**  
  If enabled, unconfirmed users will be automatically deleted after the specified number of days.  

- **Days until unconfirmed users are deleted**  
  The number of days after which unconfirmed users will be removed.

- **Users per cron job execution** (New in Version 1.1.0)  
  Maximum number of users that can be deleted per cron job execution to reduce server load (maximum 50 per run).

**Important Notes**:
- Only users who are exclusively in the "Guests" group (ID 2) are deleted
- Users with additional group memberships are preserved
- Only users are deleted - their content (posts, attachments, conversations, etc.) remains in the system  

## Configuration

### Permissions
The plugin adds a new permission for viewing the deleted users log:

- **Can view log of automatic deletion of unconfirmed users (email confirmation)**  
  Located in: ACP → User Management → User Groups → [Select Group] → User Permissions  
  This permission allows users to view the log of automatically deleted unconfirmed users in the ACP.

### Accessing the Log Page
After installation, you can access the log page at:
- **ACP → User Management → Deleted Unconfirmed Users**

The log page displays:
- User ID (logID)
- Username
- Email address
- Registration date
- Deletion date

All columns are sortable, and the table is paginated (100 entries per page by default).

### Email Notifications ✅
Administrators receive email notifications when users are deleted. The notification includes:
- Number of deleted users
- Personalized greeting with administrator username
- Link to view details in the ACP log page

Notifications are automatically sent to all users in administrator groups when unconfirmed users are deleted.

### GDPR-Compliant Data Anonymization ✅ (Version 1.2.0+)

This plugin implements comprehensive data anonymization to ensure GDPR compliance. All personal data (usernames and email addresses) are automatically anonymized before being stored in the database or sent via email notifications.

#### Username Anonymization

Usernames are anonymized using a smart masking algorithm that preserves partial readability for administrative purposes while ensuring privacy:

- **Short usernames (≤ 4 characters)**: Fully masked
  - Example: `test` → `****`
  
- **Medium usernames (5-8 characters)**: First 2 characters + mask + last 2 characters
  - Example: `username` → `us***me`
  
- **Long usernames (9+ characters)**: First 2 characters + mask + last 4 characters
  - Example: `johnsmith123` → `jo***h123`

This approach allows administrators to identify users approximately (e.g., distinguishing between "johnsmith" and "johndoe") while protecting personal data.

#### Email Address Anonymization

Email addresses are anonymized more strictly to ensure maximum privacy protection:

**Local Part (before @):**
- Shows only the first character
- Example: `testuser` → `t***`

**Domain Name (before TLD):**
- Shows first 2 characters + mask
- Example: `example` → `ex***`

**Top-Level Domain (TLD):**
- 1 character: Fully masked (`*`)
- 2-3 characters: First character + mask (e.g., `i***` for `.invalid`)
- 4+ characters: First 2 characters + mask (e.g., `co***` for `.com`)

**Complete Examples:**
- `testuser@example.com` → `t***@ex***.co***`
- `admin@domain.invalid` → `a***@do***.in***`
- `john@site.org` → `j***@si***.o***`

#### Where Anonymization is Applied

1. **Database Log Entries**: All usernames and email addresses stored in the log table are anonymized
2. **Email Notifications**: The user list included in administrator email notifications contains only anonymized data
3. **ACP Log Display**: The log page displays anonymized data (as stored in the database)

#### Benefits

- **GDPR Compliance**: Personal data is protected according to GDPR requirements
- **Privacy Protection**: Email addresses are strongly anonymized to prevent identification
- **Administrative Utility**: Usernames remain partially readable for administrative purposes
- **Automatic Processing**: Anonymization happens automatically during deletion - no manual intervention required
- **Consistent Application**: Same anonymization rules apply to both database storage and email notifications

## Installation
1. Download the latest `.tar.gz` release from the [Releases](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases) section.
2. Upload the `.tar.gz` file via the WoltLab ACP.
3. Activate the plugin and configure the desired waiting time before deletion.
4. Configure permissions for user groups that should have access to the log page.

## License
This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See the [LICENSE](LICENSE) file for details.
