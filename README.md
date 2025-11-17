# de.deinestrainreviews.autoDeleteUnconfirmedUsers

A WoltLab Suite Core 6.1 plugin that automatically deletes unconfirmed users after a configurable period with flexible logging options.

## Features
- Periodic check for unconfirmed users via cron job
- Automatic deletion after a configurable period
- **Flexible logging system** with two modes:
  - **Anonymous mode (recommended)**: Only logs the count of deleted users (GDPR-safe)
  - **Full mode (opt-in)**: Logs userID, username, and email with automatic cleanup
- **ACP log page** to view deletion logs
- **Automatic cleanup** of PII logs after retention period
- Multilingual support (German & English)
- Admin panel settings to configure deletion time and logging behavior

## Requirements
- WoltLab Suite Core 6.1.0 or higher

## Admin Panel Options
The plugin adds the following settings in the **WoltLab ACP (Administration Control Panel)**:

### Deletion Settings
- **Enable automatic deletion of unconfirmed users**  
  If enabled, unconfirmed users will be automatically deleted after the specified number of days.  

- **Days until unconfirmed users are deleted**  
  The number of days after which unconfirmed users will be removed (1-365 days).  

- **Users per cron job execution**  
  Maximum number of users that can be deleted per cron job execution to reduce server load (1-50 users).

### Logging Settings
- **Save logs anonymously (Recommended)**  
  If enabled (default), only the count of deleted users is logged (GDPR-safe). If disabled, userID, username, and email are logged (High GDPR risk!).

- **Retention period for full logs (days)**  
  How many days should full logs (with email/username) be kept? After this period, they MUST be automatically deleted for data protection reasons. A value of 0 (never delete) is strongly discouraged. This option is only visible when anonymous logging is disabled.

## ACP Log Page
The plugin provides an ACP log page (`Auto Delete Logs`) to view all deletion logs:
- **Anonymous logs**: Shows the count of deleted users per execution
- **Full logs**: Shows individual user details (userID, username, email) with execution time

## Installation
1. Download the latest `.tar.gz` release from the [Releases](https://github.com/YOUR_GITHUB_USERNAME/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases) section.
2. Upload the `.tar.gz` file via the WoltLab ACP.
3. Activate the plugin and configure the desired settings:
   - Configure deletion time and limits
   - Choose logging mode (anonymous recommended for GDPR compliance)
   - If using full logging, set retention period

## GDPR / Data Protection
- **Anonymous logging (default)**: GDPR-safe, only stores counts
- **Full logging (opt-in)**: Stores PII (userID, username, email) - use with caution
- **Automatic cleanup**: Full logs are automatically deleted after the retention period
- **Conditional display**: Retention period option only appears when full logging is enabled

## License
This project is licensed under the **GNU General Public License v3.0 (GPL-3.0)**.  
See the [LICENSE](LICENSE) file for details.

---

## Deutsch: Automatische Löschung unbestätigter Benutzer

Ein WoltLab Suite Core 6.1 Plugin zur automatischen Löschung von Benutzern, die ihre E-Mail nicht innerhalb einer festgelegten Zeit bestätigt haben, mit flexiblem Logging-System.

### Funktionen
- Regelmäßige Prüfung unbestätigter Benutzer per Cronjob
- Automatische Löschung nach einer konfigurierbaren Zeit
- **Flexibles Logging-System** mit zwei Modi:
  - **Anonymer Modus (empfohlen)**: Protokolliert nur die Anzahl gelöschter Benutzer (DSGVO-sicher)
  - **Vollständiger Modus (Opt-in)**: Protokolliert userID, username und E-Mail mit automatischer Löschung
- **ACP-Log-Seite** zur Anzeige der Löschprotokolle
- **Automatische Löschung** von PII-Logs nach Aufbewahrungsfrist
- Mehrsprachige Unterstützung (Deutsch & Englisch)
- Einstellbare Optionen im Admin-Panel

### Anforderungen
- WoltLab Suite Core 6.1.0 oder höher

### Admin-Panel-Optionen
Das Plugin fügt folgende Einstellungen im **WoltLab ACP (Administrationsbereich)** hinzu:

#### Lösch-Einstellungen
- **Automatische Löschung unbestätigter Benutzer aktivieren**  
  Falls aktiviert, werden unbestätigte Benutzer nach der eingestellten Anzahl an Tagen automatisch gelöscht.  

- **Tage bis zur Löschung unbestätigter Benutzer**  
  Anzahl der Tage, nach denen unbestätigte Benutzer automatisch gelöscht werden (1-365 Tage).  

- **Benutzer pro Cronjob-Ausführung**  
  Maximale Anzahl von Benutzern, die pro Cronjob-Ausführung gelöscht werden dürfen, um die Serverlast zu reduzieren (1-50 Benutzer).

#### Logging-Einstellungen
- **Logs anonym speichern (Empfohlen)**  
  Wenn aktiviert (Standard), wird nur die Anzahl der gelöschten Benutzer protokolliert (DSGVO-sicher). Wenn deaktiviert, werden userID, username und E-Mail protokolliert (Hohes DSGVO-Risiko!).

- **Vollständige Logs aufbewahren (Tage)**  
  Wie viele Tage sollen die vollständigen Logs (mit E-Mail/Username) aufbewahrt werden? Nach dieser Frist MÜSSEN sie aus Datenschutzgründen automatisch gelöscht werden. Ein Wert von 0 (nie löschen) wird dringend abgeraten. Diese Option wird nur angezeigt, wenn anonymes Logging deaktiviert ist.

### ACP-Log-Seite
Das Plugin bietet eine ACP-Log-Seite (`Auto Delete Logs`) zur Anzeige aller Löschprotokolle:
- **Anonyme Logs**: Zeigt die Anzahl gelöschter Benutzer pro Ausführung
- **Vollständige Logs**: Zeigt individuelle Benutzerdetails (userID, username, E-Mail) mit Ausführungszeit

### Installation
1. Lade die neueste `.tar.gz`-Version aus dem [Releases](https://github.com/YOUR_GITHUB_USERNAME/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases) Bereich herunter.
2. Lade die `.tar.gz`-Datei über das WoltLab ACP hoch.
3. Aktiviere das Plugin und konfiguriere die gewünschten Einstellungen:
   - Konfiguriere Löschzeit und Limits
   - Wähle den Logging-Modus (anonym wird für DSGVO-Konformität empfohlen)
   - Bei vollständigem Logging die Aufbewahrungsfrist einstellen

### DSGVO / Datenschutz
- **Anonymes Logging (Standard)**: DSGVO-sicher, speichert nur Zählungen
- **Vollständiges Logging (Opt-in)**: Speichert personenbezogene Daten (userID, username, E-Mail) - mit Vorsicht verwenden
- **Automatische Löschung**: Vollständige Logs werden nach der Aufbewahrungsfrist automatisch gelöscht
- **Bedingte Anzeige**: Aufbewahrungsfrist-Option wird nur angezeigt, wenn vollständiges Logging aktiviert ist

### Lizenz
Dieses Projekt steht unter der **GNU General Public License v3.0 (GPL-3.0)**.  
Siehe die [LICENSE](LICENSE)-Datei für Details.
