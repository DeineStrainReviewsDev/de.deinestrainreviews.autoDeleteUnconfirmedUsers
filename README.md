# de.deinestrainreviews.autoDeleteUnconfirmedUsers

> ⚠️ **WARNING / WARNUNG**  
> **This is a development version and is NOT intended for production use.**  
> **Dies ist eine Entwicklungsversion und ist NICHT für den Produktionseinsatz gedacht.**
> 
> This code is provided "as is" without warranty of any kind. The author assumes no liability for any damages, data loss, or other issues that may arise from the use of this software. Use at your own risk and only in development/test environments.
> 
> Dieser Code wird "wie besehen" ohne jegliche Gewährleistung bereitgestellt. Der Autor übernimmt keine Haftung für Schäden, Datenverluste oder andere Probleme, die durch die Nutzung dieser Software entstehen können. Nutzung auf eigene Gefahr und nur in Entwicklungs-/Testumgebungen.
> 
> ---

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

---

## Deutsch: Automatische Löschung unbestätigter Benutzer

Ein WoltLab-Plugin zur automatischen Löschung von Benutzern, die ihre E-Mail nicht innerhalb einer festgelegten Zeit bestätigt haben.

### Funktionen

#### ✅ Vollständig funktionsfähig (Version 1.1.1+)
- Regelmäßige Prüfung unbestätigter Benutzer per Cronjob
- Automatische Löschung nach einer konfigurierbaren Zeit
- Löscht ausschließlich Benutzer, die sich nur in der Gruppe "Gäste" (ID 2) befinden
- Benutzer mit zusätzlichen Gruppenzugehörigkeiten bleiben erhalten
- ⚠️ **Wichtig**: Nur die Benutzer selbst werden gelöscht – ihre Inhalte (Beiträge, Dateianhänge, Konversationen etc.) bleiben im System erhalten
- Mehrsprachige Unterstützung (Deutsch & Englisch)
- Einstellbare Optionen im Admin-Panel
- Konfigurierbare maximale Anzahl von Benutzern pro Cronjob-Ausführung (max. 50 pro Durchlauf)

#### ✅ Vollständig funktionsfähig (Version 1.2.0)
- **ACP-Log-Seite** - Anzeige aller gelöschten unbestätigten Benutzer im Administrationsbereich
- **Sortierbare Log-Tabelle** - Sortierung nach ID, Benutzername, E-Mail, Registrierungsdatum oder Löschdatum
- **Datenbank-Protokollierung** - Log-Einträge werden in der Datenbank gespeichert
- **Automatische Log-Eintrag-Erstellung** - Log-Einträge werden automatisch bei der Löschung erstellt
- **E-Mail-Benachrichtigungen** - Administratoren erhalten E-Mail-Benachrichtigungen bei gelöschten Benutzern
- **DSGVO-konforme Datenanonymisierung** - Alle personenbezogenen Daten (Benutzernamen und E-Mail-Adressen) werden automatisch vor der Speicherung und in E-Mail-Benachrichtigungen anonymisiert

#### 📋 Geplante Features
- **Optionale Inhaltslöschung** - Eine optionale Funktion zum vollständigen Entfernen aller Inhalte gelöschter Benutzer (Beiträge, Dateianhänge etc.). Diese Funktion wird einstellbar sein, sodass Administratoren selbst entscheiden können, ob Inhalte zur besseren Lesbarkeit von Themen bestehen bleiben sollen oder vollständig entfernt werden.
- **Erneuter Versand der Bestätigungsmail mit Löschhinweis** - Erneuter Versand der Bestätigungsmail samt Hinweis auf die Löschung. Damit könnte man Nutzer aktivieren, die schlicht vergessen hatten zu klicken.

### Admin-Panel-Optionen
Das Plugin fügt folgende Einstellungen im **WoltLab ACP (Administrationsbereich)** hinzu:

**Zu finden unter**: ACP → Konfiguration → Optionen → Benutzer → Registrierung

- **Automatische Löschung unbestätigter Benutzer aktivieren**  
  Falls aktiviert, werden unbestätigte Benutzer nach einer festgelegten Anzahl von Tagen automatisch gelöscht.  

- **Tage bis zur Löschung unbestätigter Benutzer**  
  Anzahl der Tage, nach denen unbestätigte Benutzer entfernt werden.

- **Benutzer pro Cronjob-Ausführung** (Neu ab Version 1.1.0)  
  Maximale Anzahl von Benutzern, die pro Cronjob-Ausführung gelöscht werden dürfen, um die Serverlast zu reduzieren (maximal 50 Benutzer pro Durchlauf).

**Wichtige Hinweise**:
- Das Plugin löscht Benutzer, die sich ausschließlich in der Gruppe "Gäste" (ID 2) befinden
- Benutzer mit zusätzlichen Gruppenzugehörigkeiten bleiben erhalten
- Nur die Benutzer selbst werden gelöscht – ihre Inhalte (Beiträge, Dateianhänge, Konversationen etc.) bleiben im System erhalten  

### Konfiguration

#### Berechtigungen
Das Plugin fügt eine neue Berechtigung zum Anzeigen des Logs gelöschter Benutzer hinzu:

- **Kann Log der automatischen Löschung unbestätigter Benutzer (E-Mail-Bestätigung) sehen**  
  Zu finden unter: ACP → Benutzerverwaltung → Benutzergruppen → [Gruppe auswählen] → Benutzerrechte  
  Diese Berechtigung erlaubt es Benutzern, das Log der automatisch gelöschten unbestätigten Benutzer im ACP anzuzeigen.

#### Zugriff auf die Log-Seite
Nach der Installation können Sie auf die Log-Seite zugreifen unter:
- **ACP → Benutzerverwaltung → Gelöschte unbestätigte Benutzer**

Die Log-Seite zeigt:
- Benutzer-ID (logID)
- Benutzername
- E-Mail-Adresse
- Registrierungsdatum
- Löschdatum

Alle Spalten sind sortierbar, und die Tabelle ist paginiert (standardmäßig 100 Einträge pro Seite).

#### E-Mail-Benachrichtigungen ✅
Administratoren erhalten E-Mail-Benachrichtigungen, wenn Benutzer gelöscht werden. Die Benachrichtigung enthält:
- Anzahl der gelöschten Benutzer
- Personalisierte Begrüßung mit Administrator-Benutzername
- Link zur Anzeige der Details in der ACP-Log-Seite

Benachrichtigungen werden automatisch an alle Benutzer in Administratorgruppen gesendet, wenn unbestätigte Benutzer gelöscht werden.

#### DSGVO-konforme Datenanonymisierung ✅ (Version 1.2.0+)

Dieses Plugin implementiert eine umfassende Datenanonymisierung, um die DSGVO-Konformität sicherzustellen. Alle personenbezogenen Daten (Benutzernamen und E-Mail-Adressen) werden automatisch anonymisiert, bevor sie in der Datenbank gespeichert oder per E-Mail-Benachrichtigung versendet werden.

##### Anonymisierung von Benutzernamen

Benutzernamen werden mit einem intelligenten Maskierungsalgorithmus anonymisiert, der die partielle Lesbarkeit für administrative Zwecke erhält, während die Privatsphäre gewährleistet wird:

- **Kurze Benutzernamen (≤ 4 Zeichen)**: Vollständig maskiert
  - Beispiel: `test` → `****`
  
- **Mittlere Benutzernamen (5-8 Zeichen)**: Erste 2 Zeichen + Maske + letzte 2 Zeichen
  - Beispiel: `username` → `us***me`
  
- **Lange Benutzernamen (9+ Zeichen)**: Erste 2 Zeichen + Maske + letzte 4 Zeichen
  - Beispiel: `johnsmith123` → `jo***h123`

Dieser Ansatz ermöglicht es Administratoren, Benutzer ungefähr zu identifizieren (z.B. um "johnsmith" von "johndoe" zu unterscheiden), während personenbezogene Daten geschützt werden.

##### Anonymisierung von E-Mail-Adressen

E-Mail-Adressen werden strenger anonymisiert, um maximalen Datenschutz zu gewährleisten:

**Lokaler Teil (vor @):**
- Zeigt nur das erste Zeichen
- Beispiel: `testuser` → `t***`

**Domain-Name (vor TLD):**
- Zeigt die ersten 2 Zeichen + Maske
- Beispiel: `example` → `ex***`

**Top-Level-Domain (TLD):**
- 1 Zeichen: Vollständig maskiert (`*`)
- 2-3 Zeichen: Erstes Zeichen + Maske (z.B. `i***` für `.invalid`)
- 4+ Zeichen: Erste 2 Zeichen + Maske (z.B. `co***` für `.com`)

**Vollständige Beispiele:**
- `testuser@example.com` → `t***@ex***.co***`
- `admin@domain.invalid` → `a***@do***.in***`
- `john@site.org` → `j***@si***.o***`

##### Wo die Anonymisierung angewendet wird

1. **Datenbank-Log-Einträge**: Alle Benutzernamen und E-Mail-Adressen, die in der Log-Tabelle gespeichert werden, sind anonymisiert
2. **E-Mail-Benachrichtigungen**: Die in E-Mail-Benachrichtigungen an Administratoren enthaltene Benutzerliste enthält nur anonymisierte Daten
3. **ACP-Log-Anzeige**: Die Log-Seite zeigt anonymisierte Daten an (wie in der Datenbank gespeichert)

##### Vorteile

- **DSGVO-Konformität**: Personenbezogene Daten werden gemäß DSGVO-Anforderungen geschützt
- **Datenschutz**: E-Mail-Adressen werden stark anonymisiert, um eine Identifikation zu verhindern
- **Administrative Nützlichkeit**: Benutzernamen bleiben teilweise lesbar für administrative Zwecke
- **Automatische Verarbeitung**: Die Anonymisierung erfolgt automatisch während der Löschung - keine manuelle Intervention erforderlich
- **Konsistente Anwendung**: Die gleichen Anonymisierungsregeln gelten sowohl für die Datenbankspeicherung als auch für E-Mail-Benachrichtigungen

### Installation
1. Lade die neueste `.tar.gz`-Version aus dem [Releases](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases) Bereich herunter.
2. Lade die `.tar.gz`-Datei über das WoltLab ACP hoch.
3. Aktiviere das Plugin und konfiguriere die gewünschte Wartezeit bis zur Löschung.
4. Konfiguriere die Berechtigungen für Benutzergruppen, die Zugriff auf die Log-Seite haben sollen.

### Lizenz
Dieses Projekt steht unter der **GNU General Public License v3.0 (GPL-3.0)**.  
Siehe die [LICENSE](LICENSE)-Datei für Details.
