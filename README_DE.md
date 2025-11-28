# Automatische Löschung unbestätigter Benutzer

> 🌐 **Language / Sprache**: [English](README.md) | [Deutsch](#readme)

Ein WoltLab-Plugin zur automatischen Löschung von Benutzern, die ihre E-Mail-Adresse nicht innerhalb einer festgelegten Zeit bestätigt haben. Mit zweistufigem Löschprozess, optionalen Erinnerungs-E-Mails und umfassender Protokollierung.

[![Lizenz: GPL v3](https://img.shields.io/badge/Lizenz-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![WoltLab Plugin Store](https://img.shields.io/badge/WoltLab-Plugin%20Store-orange)](https://www.woltlab.com/)

## 🚀 Aktuelle Version: 1.4.0

### Hauptfunktionen

- ✅ **E-Mail-Reputationsschutz** - NEU in v1.4.0: Schützen Sie Ihren Server vor Bounces und Spam-Fallen
- ✅ **Stille Legacy-Löschung** - Automatische Bereinigung alter "Geisterkonten" ohne Gefährdung der E-Mail-Reputation
- ✅ **Zweistufiger Löschprozess** - Optionale Erinnerungs-E-Mail vor der Löschung
- ✅ **Umfassende Protokollierung** - Nachverfolgung gelöschter Benutzer und versendeter Erinnerungen
- ✅ **DSGVO-konform** - Automatische Datenanonymisierung
- ✅ **E-Mail-Benachrichtigungen** - Administratoren bleiben über Löschungen und Erinnerungen informiert
- ✅ **Flexible Konfiguration** - Anpassbare Zeiträume und Verhaltensweisen
- ✅ **Mehrsprachig** - Vollständige Unterstützung für Deutsch und Englisch

<details>
<summary><strong>📋 Vollständige Funktionsliste</strong></summary>

### Kernfunktionen (v1.0.0+)
- Automatische Löschung unbestätigter Benutzer per Cronjob
- Konfigurierbare Löschfrist
- Löscht nur Benutzer, die ausschließlich in der Gruppe "Gäste" (ID 2) sind
- Erhält Benutzerinhalte (Beiträge, Dateianhänge, Konversationen)
- Mehrsprachige Unterstützung (Deutsch & Englisch)

### Erweiterte Funktionen (v1.1.0+)
- Konfigurierbare Stapelgröße (max. 50 Benutzer pro Cronjob-Durchlauf)
- Optimierung der Serverlast

### Protokollierung & Benachrichtigungen (v1.2.0+)
- ACP-Log-Seite für gelöschte Benutzer
- Sortierbare Log-Tabelle (ID, Benutzername, E-Mail, Daten)
- Datenbank-Protokollierung mit permanenter Speicherung
- E-Mail-Benachrichtigungen an Administratoren
- DSGVO-konforme Datenanonymisierung

### Zweistufiger Workflow (v1.3.0+)
- Optionale Erinnerungs-E-Mail vor der Löschung
- Separate Log-Seite für erneut versendete Aktivierungs-E-Mails
- Getrennte Benachrichtigungseinstellungen (Erinnerung vs. Löschung)
- Intelligente Kontaktformular-Erkennung
- Professionelle HTML-E-Mail-Templates

### E-Mail-Reputationsschutz (v1.4.0+)
- Konfigurierbarer maximaler Altersschwellenwert für E-Mail-Versand
- Stiller Löschmodus für Legacy-Konten (keine E-Mail gesendet)
- Sicherheitsquarantäne-Option (alte Konten ignorieren)
- Separate Admin-Benachrichtigungen für Legacy-Löschungen
- Automatische Filterung riskanter Konten aus dem Erinnerungs-Workflow

</details>

## 📦 Installation

1. Lade die neueste Version aus dem [WoltLab Plugin Store](https://www.woltlab.com/) oder von [GitHub Releases](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases) herunter
2. Lade die `.tar.gz`-Datei über das WoltLab ACP hoch
3. Konfiguriere die Einstellungen unter **ACP → Konfiguration → Optionen → Benutzer → Registrierung**
4. Setze Berechtigungen für Benutzergruppen, die auf die Log-Seiten zugreifen sollen

## ⚙️ Konfiguration

### Ort
**ACP → Konfiguration → Optionen → Benutzer → Registrierung**

### Verfügbare Optionen

| Option | Beschreibung | Standard |
|--------|--------------|----------|
| **Automatische Löschung aktivieren** | Aktiviert die automatische Löschfunktion | Deaktiviert |
| **Tage bis zum erneuten Versand der Aktivierungs-E-Mail** | Tage vor dem Versand der Erinnerungs-E-Mail (0 = deaktiviert, direkte Löschung) | 0 |
| **Tage bis zur Löschung** | Tage nach der Erinnerung bis zur endgültigen Löschung | 7 |
| **Benutzer pro Cronjob-Ausführung** | Maximale Anzahl verarbeiteter Benutzer pro Durchlauf (max. 50) | 10 |
| **E-Mail-Benachrichtigung (Erinnerungen)** | Benachrichtigt Admins beim Versand von Erinnerungs-E-Mails | Aktiviert |
| **E-Mail-Benachrichtigung (Löschungen)** | Benachrichtigt Admins bei gelöschten Benutzern | Aktiviert |
| **Maximales Alter für E-Mail-Versand** ⭐ NEU | Maximales Alter (Tage) für Konten, die E-Mails erhalten (0 = deaktiviert) | 0 |
| **Stille Löschung von Legacy-Konten** ⭐ NEU | Löscht alte Konten ohne E-Mail zum Reputationsschutz | Deaktiviert |

<details>
<summary><strong>🔧 Konfigurationsbeispiele</strong></summary>

### Beispiel 1: Zweistufiger Prozess (Empfohlen)
```
Tage bis zum erneuten Versand der Aktivierungs-E-Mail: 7
Tage bis zur Löschung: 7
```
**Ergebnis:** Benutzer registriert sich → Nach 7 Tagen: Erinnerungs-E-Mail → Nach weiteren 7 Tagen (gesamt 14): Löschung

### Beispiel 2: Direkte Löschung (Legacy-Modus)
```
Tage bis zum erneuten Versand der Aktivierungs-E-Mail: 0
Tage bis zur Löschung: 7
```
**Ergebnis:** Benutzer registriert sich → Nach 7 Tagen: Direkte Löschung (keine Erinnerung)

### Beispiel 3: Erweiterte Schonfrist
```
Tage bis zum erneuten Versand der Aktivierungs-E-Mail: 14
Tage bis zur Löschung: 14
```
**Ergebnis:** Benutzer registriert sich → Nach 14 Tagen: Erinnerungs-E-Mail → Nach weiteren 14 Tagen (gesamt 28): Löschung

### Beispiel 4: Mit Reputationsschutz (v1.4.0+) 🛡️
```
Tage bis zum erneuten Versand der Aktivierungs-E-Mail: 7
Tage bis zur Löschung: 7
Maximales Alter für E-Mail-Versand: 365
Stille Löschung von Legacy-Konten: Aktiviert
```
**Ergebnis:**
- Aktuelle Benutzer (< 365 Tage): Normaler Workflow mit Erinnerung → Löschung nach 14 Tagen
- Legacy-Benutzer (> 365 Tage): **Stillschweigend gelöscht** sofort (keine E-Mail zum Reputationsschutz)

</details>

## 📊 Log-Seiten

### Log gelöschter Benutzer
**Ort:** ACP → Benutzer → Gelöschte unbestätigte Benutzer

Zeigt alle gelöschten Benutzer mit:
- Benutzer-ID
- Anonymisierter Benutzername
- Anonymisierte E-Mail
- Registrierungsdatum
- Löschdatum

### Log erneut versendeter Aktivierungs-E-Mails
**Ort:** ACP → Benutzer → Erneut versendete Aktivierungs-E-Mails

Zeigt alle versendeten Erinnerungs-E-Mails mit:
- Benutzer-ID
- Anonymisierter Benutzername
- Anonymisierte E-Mail
- Registrierungsdatum
- Versanddatum

### Berechtigungen
Berechtigungen setzen unter:
**ACP → Benutzergruppen → [Gruppe auswählen] → Administrative Rechte → Benutzer**

Berechtigung: *Kann Log der automatischen Löschung unbestätigter Benutzer sehen*

## 🔒 DSGVO-Konformität

Alle personenbezogenen Daten werden automatisch vor der Speicherung und in E-Mail-Benachrichtigungen anonymisiert.

<details>
<summary><strong>📝 Anonymisierungsdetails</strong></summary>

### Anonymisierung von Benutzernamen

| Länge | Muster | Beispiel |
|-------|--------|----------|
| ≤ 4 Zeichen | Vollständig maskiert | `test` → `****` |
| 5-8 Zeichen | Erste 2 + Maske + letzte 2 | `username` → `us***me` |
| 9+ Zeichen | Erste 2 + Maske + letzte 4 | `johnsmith123` → `jo***h123` |

### Anonymisierung von E-Mail-Adressen

**Muster:** Erstes Zeichen des lokalen Teils + Maske @ erste 2 Zeichen der Domain + Maske . erste(s) Zeichen der TLD + Maske

**Beispiele:**
- `testuser@example.com` → `t***@ex***.co***`
- `admin@domain.org` → `a***@do***.o***`

### Anwendungsbereiche
1. Datenbank-Log-Einträge
2. E-Mail-Benachrichtigungen an Administratoren
3. ACP-Log-Seiten-Anzeige

</details>

## 📝 Changelog

<details>
<summary><strong>Version 1.4.0 (2025-11-24)</strong> - Aktuelle Version</summary>

### ✨ Neue Funktionen
- **E-Mail-Reputationsschutzsystem**
  - Maximaler Registrierungsalter-Schwellenwert zur Identifizierung „riskanter" Legacy-Konten
  - Stiller Löschmodus für Legacy-Konten (keine E-Mail gesendet)
  - Sicherheitsquarantäne-Option zum Ignorieren alter Konten ohne Löschung
  - Intelligente Filterung schließt Legacy-Konten vom Erinnerungs-Workflow aus
- **Erweiterte Admin-Benachrichtigungen**
  - Separate Benachrichtigungs-E-Mails für Legacy-Kontolöschungen
  - Detaillierte Berichterstattung mit Altersschwelle und Schutzgründen

### 🔧 Technische Änderungen
- Neue Konfigurationsoption: `auto_delete_unconfirmed_users_max_registration_age`
- Neue Konfigurationsoption: `auto_delete_unconfirmed_users_delete_legacy`
- Erweiterter `UnconfirmedUserService` mit Legacy-Konten-Verarbeitung
- Neue Methode in `DSRUnconfirmedUserMailService` für Legacy-Löschbenachrichtigungen
- Aktualisierte Sprachdateien (EN/DE) mit Reputationsschutz-Terminologie

### 📦 Release
- [Vollständiger Changelog v1.4.0](CHANGELOG_1.4.0_DE.md)

</details>

<details>
<summary><strong>Version 1.3.0 (2025-11-21)</strong></summary>

### ✨ Neue Funktionen
- Zweistufiger Löschprozess mit optionalen Erinnerungs-E-Mails
- Neue Datenbanktabelle zur Nachverfolgung erneut versendeter Aktivierungs-E-Mails
- Separate ACP-Log-Seite für Erinnerungs-E-Mails
- Getrennte E-Mail-Benachrichtigungseinstellungen (Erinnerung vs. Löschung)
- Professionelle HTML-E-Mail-Templates
- Intelligente Kontaktformular-Modul-Erkennung
- Erweiterte Admin-Benachrichtigungen für beide Workflow-Stufen

### 🔧 Technische Änderungen
- Tabelle `wcf1_resent_activation_email_log` hinzugefügt
- Migrations-Script für Upgrade von v1.2.0 → v1.3.0
- Neue Konfigurationsoptionen für zweistufigen Workflow
- Aktualisierte Sprachdateien (EN/DE)

### 📦 Release
- Genehmigt und im WoltLab Plugin Store veröffentlicht

</details>

<details>
<summary><strong>Version 1.2.0 (2024)</strong></summary>

### ✨ Neue Funktionen
- ACP-Log-Seite für gelöschte Benutzer
- Sortierbare Log-Tabelle
- Datenbank-Protokollierung mit permanenter Speicherung
- E-Mail-Benachrichtigungen an Administratoren
- DSGVO-konforme Datenanonymisierung

### 🔧 Technische Änderungen
- Tabelle `wcf1_deleted_unconfirmed_user_log` hinzugefügt
- Neues Berechtigungssystem für Log-Zugriff
- Anonymisierungsalgorithmen für Benutzernamen und E-Mails

</details>

<details>
<summary><strong>Version 1.1.0 (2024)</strong></summary>

### ✨ Neue Funktionen
- Konfigurierbare Stapelgröße (max. 50 Benutzer pro Durchlauf)
- Optimierung der Serverlast

</details>

<details>
<summary><strong>Version 1.0.0 (2024)</strong></summary>

### ✨ Erstes Release
- Automatische Löschung unbestätigter Benutzer
- Konfigurierbare Löschfrist
- Gruppenbasierte Filterung (nur Gäste)
- Mehrsprachige Unterstützung (DE/EN)

</details>

## 🔮 Geplante Funktionen

- **Optionale Inhaltslöschung** - Konfigurierbare Entfernung aller benutzergenerierter Inhalte (Beiträge, Dateianhänge usw.)

## ⚠️ Wichtige Hinweise

- Es werden nur Benutzer gelöscht, die **ausschließlich** in der Gruppe "Gäste" (ID 2) sind
- Benutzer mit zusätzlichen Gruppenmitgliedschaften werden **erhalten**
- Benutzerinhalte (Beiträge, Dateianhänge, Konversationen) **bleiben** im System erhalten
- Der Cronjob muss in WoltLab ordnungsgemäß konfiguriert sein

## 📄 Lizenz

Dieses Projekt steht unter der **GNU General Public License v3.0 (GPL-3.0)**.  
Siehe die [LICENSE](LICENSE)-Datei für Details.

## 🔗 Links

- [WoltLab Plugin Store](https://www.woltlab.com/)
- [GitHub Repository](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers)
- [Probleme melden](https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/issues)

---

**Mit ❤️ für die WoltLab-Community erstellt**
