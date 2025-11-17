# Versionsverwaltung - Auto Delete Unconfirmed Users Plugin

**Copyright (c) 2025 DeineStrainReviews.de**  
**License:** GNU General Public License v3.0 (GPL-3.0)  
**Repository:** https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers

> **⚠️ WICHTIG:** Dieser Copyright-Hinweis darf nicht entfernt werden.

---

## Übersicht

Dieses WoltLab-Plugin verwendet **Semantic Versioning** (SemVer) nach dem Schema `MAJOR.MINOR.PATCH`, genau wie WoltLab Suite selbst.

**Format:** `X.Y.Z`
- **X (Major):** Breaking Changes, API-Änderungen, nicht-rückwärtskompatible Änderungen
- **Y (Minor):** Neue Features, rückwärtskompatible Erweiterungen
- **Z (Patch):** Bugfixes, Dokumentations-Updates, rückwärtskompatible Korrekturen

**Beispiele:**
- `1.1.2` → `1.1.3` (Patch: Bugfix oder Dokumentation)
- `1.1.2` → `1.2.0` (Minor: Neues Feature, rückwärtskompatibel)
- `1.1.2` → `2.0.0` (Major: Breaking Change, nicht-rückwärtskompatibel)

---

## Version in package.xml

Für WoltLab-Plugins wird die Version **primär in `package.xml`** verwaltet:

```xml
<packageinformation>
    <version>1.1.2</version>
    <date>2025-01-15</date>
    ...
</packageinformation>
```

### Wichtig: Update-Instructions

Bei jeder Version müssen auch die **Update-Instructions** in `package.xml` aktualisiert werden:

```xml
<instructions type="update" fromversion="1.1.2">
    <instruction type="sql"/>
    <instruction type="file" application="wcf"/>
    <instruction type="language"/>
    <instruction type="acpMenu"/>
    <instruction type="page"/>
</instructions>
```

Die `fromversion` gibt an, **von welcher Version aus** das Update möglich ist.

---

## Wann welche Version erhöhen?

### Patch (1.1.2 → 1.1.3)

**Verwende Patch für:**
- ✅ Bugfixes im Code
- ✅ Dokumentations-Updates
- ✅ Kommentar-Übersetzungen
- ✅ Code-Refactoring (ohne Funktionsänderung)
- ✅ Performance-Verbesserungen
- ✅ Kleinere Korrekturen

**Beispiele:**
- SQL-Query-Bugfix
- Kommentare von Deutsch zu Englisch übersetzt
- Code-Formatierung
- README.md aktualisiert

**Beispiel-Workflow:**
```bash
# 1. Version in package.xml erhöhen: 1.1.2 → 1.1.3
# 2. Date aktualisieren: <date>2025-01-15</date>
# 3. Update-Instruction hinzufügen (wenn nötig)
# 4. Committen und pushen
git add package.xml
git commit -m "chore: Version auf 1.1.3 erhöht (Bugfix)"
git push origin develop

# 5. Tag erstellen und pushen
git tag -a v1.1.3 -m "Version 1.1.3 - Bugfix"
git push origin v1.1.3
```

---

### Minor (1.1.2 → 1.2.0)

**Verwende Minor für:**
- ✅ Neue Features
- ✅ Neue ACP-Seiten
- ✅ Neue Optionen
- ✅ Neue Datenbanktabellen
- ✅ Erweiterte Funktionalität
- ✅ Rückwärtskompatible Änderungen

**Beispiele:**
- Logging-Funktion hinzugefügt (wie in Version 1.1.2)
- Neue ACP-Seite für Statistiken
- Neue Option für erweiterte Konfiguration
- Email-Benachrichtigungen hinzugefügt

**Beispiel-Workflow:**
```bash
# 1. Version in package.xml erhöhen: 1.1.2 → 1.2.0
# 2. Date aktualisieren
# 3. Update-Instruction hinzufügen:
#    <instructions type="update" fromversion="1.1.2">
#       ...
#    </instructions>
# 4. Neue Features implementieren
# 5. Committen und pushen
git add package.xml
git commit -m "feat: Version auf 1.2.0 erhöht (neues Feature: Logging)"
git push origin develop

# 6. Tag erstellen und pushen
git tag -a v1.2.0 -m "Version 1.2.0 - Logging Feature"
git push origin v1.2.0
```

---

### Major (1.1.2 → 2.0.0)

**Verwende Major für:**
- ✅ Breaking Changes
- ✅ Nicht-rückwärtskompatible Änderungen
- ✅ API-Änderungen
- ✅ Strukturelle Änderungen
- ✅ Datenbankschema-Änderungen (die nicht automatisch migriert werden können)

**Beispiele:**
- Option-Name geändert (bricht Konfiguration)
- Datenbankstruktur geändert (ohne Migration)
- PHP-Klassen umbenannt
- Plugin-Identifier geändert

**Beispiel-Workflow:**
```bash
# 1. Version in package.xml erhöhen: 1.1.2 → 2.0.0
# 2. Date aktualisieren
# 3. Update-Instruction hinzufügen
# 4. Breaking Changes implementieren
# 5. Migration-Script erstellen (falls nötig)
# 6. Committen und pushen
git add package.xml
git commit -m "feat!: Version auf 2.0.0 erhöht (Breaking Changes)"
git push origin develop

# 7. Tag erstellen und pushen
git tag -a v2.0.0 -m "Version 2.0.0 - Breaking Changes"
git push origin v2.0.0
```

---

## Automatische Versionsverwaltung

### Script verwenden

Das Script `scripts/plugin-version.sh` hilft dir, die Version automatisch zu erhöhen:

```bash
# Patch-Version erhöhen (1.1.2 -> 1.1.3)
./scripts/plugin-version.sh patch

# Minor-Version erhöhen (1.1.2 -> 1.2.0)
./scripts/plugin-version.sh minor

# Major-Version erhöhen (1.1.2 -> 2.0.0)
./scripts/plugin-version.sh major

# Nur Version aktualisieren, kein Git-Tag
./scripts/plugin-version.sh patch --no-tag
```

**Was macht das Script?**
1. ✅ Liest die aktuelle Version aus `package.xml`
2. ✅ Berechnet die neue Version basierend auf dem Inkrement-Typ
3. ✅ Aktualisiert die Version in `package.xml`
4. ✅ Aktualisiert das Datum in `package.xml`
5. ✅ Erstellt einen Git-Tag `vX.Y.Z` (falls nicht `--no-tag`)

**Siehe:** [`scripts/README.md`](scripts/README.md) für detaillierte Dokumentation.

---

## Manuelle Versionsverwaltung

### 1. Version in package.xml aktualisieren

**Aktuell:**
```xml
<version>1.1.2</version>
<date>2025-01-15</date>
```

**Neu (z.B. Patch):**
```xml
<version>1.1.3</version>
<date>2025-01-16</date>
```

### 2. Update-Instruction hinzufügen (wenn nötig)

Falls sich in der neuen Version Datenbankstruktur, Dateien, Sprachen oder Menüs geändert haben:

```xml
<instructions type="update" fromversion="1.1.2">
    <instruction type="sql"/>
    <instruction type="file" application="wcf"/>
    <instruction type="language"/>
    <instruction type="acpMenu"/>
    <instruction type="page"/>
</instructions>
```

**Welche Instructions sind nötig?**
- ✅ `sql` - Wenn `install.sql` oder Datenbankstruktur geändert wurde
- ✅ `file` - Wenn PHP/Template-Dateien geändert wurden
- ✅ `language` - Wenn `language/de.xml` oder `language/en.xml` geändert wurden
- ✅ `acpMenu` - Wenn `acpMenu.xml` geändert wurde
- ✅ `page` - Wenn `page.xml` geändert wurde
- ✅ `option` - Wenn `option.xml` geändert wurde
- ✅ `cronjob` - Wenn `cronjob.xml` geändert wurde

### 3. Git-Tag erstellen

```bash
git tag -a v1.1.3 -m "Version 1.1.3 - Bugfix: SQL IN clause array handling"
```

**Tag-Format:** `vX.Y.Z` (mit `v` Präfix)

**Tag-Message sollte enthalten:**
- Versionsnummer
- Typ der Änderung (Bugfix, Feature, Breaking Change)
- Kurze Beschreibung der wichtigsten Änderungen

### 4. Tag pushen

```bash
git push origin v1.1.3
```

---

## Plugin-Validierung

Vor dem Release sollte das Plugin validiert werden:

```bash
./scripts/validate-plugin.sh
```

Das Script prüft:
- ✅ `package.xml` (XML-Syntax, Package-Name, Version)
- ✅ XML-Dateien (PIPs): `page.xml`, `acpMenu.xml`, `cronjob.xml`, `option.xml`
- ✅ SQL-Dateien: `install.sql`
- ✅ Sprachdateien: `language/*.xml`
- ✅ PHP-Syntax aller PHP-Dateien in `files/`

**Siehe:** [`scripts/README.md`](scripts/README.md) für detaillierte Dokumentation.

---

## Best Practices

### 1. Version immer vor dem Push erhöhen
- ✅ Erhöhe die Version **bevor** du auf GitHub pushst
- ✅ Committe die Version-Änderung zusammen mit den Code-Änderungen

### 2. Klare Commit-Messages
**Gut:**
```bash
git commit -m "fix: SQL IN clause array handling using PreparedStatementConditionBuilder

Fixed critical bug where SQL queries with IN (?) clauses would fail
when passing arrays directly to execute(). PDO does not automatically
expand IN (?) for arrays.

Version: 1.1.3"
```

**Schlecht:**
```bash
git commit -m "fix bug"
```

### 3. Konsistente Tag-Messages
**Format:**
```
Version X.Y.Z - [Typ]: [Beschreibung]
```

**Beispiele:**
- `Version 1.1.3 - Bugfix: SQL IN clause array handling`
- `Version 1.2.0 - Feature: Logging and notifications`
- `Version 2.0.0 - Breaking: Renamed option names`

### 4. GitHub Releases erstellen
Nach dem Erstellen eines Tags:
1. Gehe zu: https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers/releases
2. Klicke auf "Draft a new release"
3. Wähle den Tag (z.B. `v1.1.3`)
4. Titel: `Version 1.1.3`
5. Beschreibung: Changelog mit allen Änderungen
6. Klicke auf "Publish release"

---

## Versionshistorie

### Version 1.1.2 (2025-01-15)
- ✅ Logging-Funktion hinzugefügt
- ✅ Email-Benachrichtigungen für Administratoren
- ✅ ACP-Seite für Log-Anzeige
- ✅ Alle Kommentare ins Englische übersetzt
- ✅ Code-Review und Dokumentation

### Version 1.1.1 (Vorher)
- Initiale Version mit grundlegender Funktionalität

---

## WoltLab Versionsschema

Dieses Plugin folgt dem gleichen Versionsschema wie WoltLab Suite:

- **WoltLab Suite 6.0.0** → Major Version
- **WoltLab Suite 6.1.0** → Minor Version
- **WoltLab Suite 6.1.14** → Patch Version

Wir verwenden dasselbe Schema für Konsistenz und Vertrautheit.

---

## Referenz

Diese Dokumentation basiert auf:
- [Semantic Versioning 2.0.0](https://semver.org/)
- [WoltLab Package Documentation](https://docs.woltlab.com/6.1/package/)
- [Simple WoltLab Plugin Manager - Versioning](https://github.com/SunnyCueq/simple-woltlab-plugin-manager/blob/main/docs/VERSIONING.md)

---

**Letzte Aktualisierung:** 2025-01-15

