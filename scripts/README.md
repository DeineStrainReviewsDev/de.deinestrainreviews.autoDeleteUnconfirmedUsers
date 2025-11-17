# Plugin-Scripts

Nützliche Scripts für die Entwicklung und Wartung des WoltLab-Plugins.

## Verfügbare Scripts

### 1. `plugin-version.sh` - Versionsverwaltung

Verwaltet Versionen in `package.xml` und erstellt Git-Tags.

**Verwendung:**
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
- ✅ Aktualisiert Version in `package.xml`
- ✅ Aktualisiert Datum in `package.xml`
- ✅ Erstellt Git-Tag `vX.Y.Z` (falls nicht `--no-tag`)

**Beispiel-Workflow:**
```bash
# 1. Version erhöhen
./scripts/plugin-version.sh patch

# 2. Änderungen prüfen
git diff package.xml

# 3. Committen
git add package.xml
git commit -m "chore: Version auf 1.1.3 erhöht"

# 4. Tag pushen
git push origin v1.1.3

# 5. Änderungen pushen
git push origin develop
```

---

### 2. `validate-plugin.sh` - Plugin-Validierung

Validiert die Plugin-Struktur und prüft auf Fehler.

**Verwendung:**
```bash
./scripts/validate-plugin.sh
```

**Was macht das Script?**
- ✅ Prüft `package.xml` (Existenz, XML-Syntax, Package-Name, Version)
- ✅ Prüft XML-Dateien (PIPs): `page.xml`, `acpMenu.xml`, `cronjob.xml`, `option.xml`
- ✅ Prüft SQL-Dateien: `install.sql`
- ✅ Prüft Sprachdateien: `language/*.xml`
- ✅ Prüft PHP-Syntax aller PHP-Dateien in `files/`

**Ausgabe:**
- ✅ Erfolg: Alle Prüfungen bestanden
- ⚠️ Warnungen: Nicht-kritische Probleme gefunden
- ❌ Fehler: Kritische Probleme gefunden

**Beispiel:**
```bash
$ ./scripts/validate-plugin.sh
═══════════════════════════════════════════════════════════════
  WoltLab Plugin Validierung
═══════════════════════════════════════════════════════════════

🔍 Prüfe package.xml...
✓ package.xml gefunden
✓ XML-Syntax ist korrekt
✓ Package-Name: de.deinestrainreviews.autoDeleteUnconfirmedUsers
✓ Version: 1.1.2

🔍 Prüfe XML-Dateien (PIPs)...
✓ page.xml gefunden
  ✓ page.xml ist syntaktisch korrekt
✓ acpMenu.xml gefunden
  ✓ acpMenu.xml ist syntaktisch korrekt
...

✅ Validierung erfolgreich! Keine Fehler oder Warnungen gefunden.
```

---

## Abhängigkeiten

### Erforderlich:
- `bash` - Shell-Interpreter
- `git` - Versionskontrolle (für `plugin-version.sh`)

### Optional (empfohlen):
- `xmllint` - XML-Validierung (für `validate-plugin.sh`)
  - **Installation:** 
    - Debian/Ubuntu: `sudo apt install libxml2-utils`
    - Arch: `sudo pacman -S libxml2`
- `php` - PHP CLI (für PHP-Syntax-Prüfung in `validate-plugin.sh`)

---

## Basierend auf

Diese Scripts basieren auf dem [Simple WoltLab Plugin Manager](https://github.com/SunnyCueq/simple-woltlab-plugin-manager) von SunnyCueq.

**Anpassungen für dieses Plugin:**
- Angepasst an unser Plugin-Verzeichnis
- Vereinfacht für unsere spezifischen Bedürfnisse
- Copyright auf DeineStrainReviews.de geändert
- GPL-3.0 Lizenz

---

## Lizenz

Diese Scripts sind Teil des "Auto Delete Unconfirmed Users" Plugins und stehen unter der **GNU General Public License v3.0 (GPL-3.0)**.  
Siehe [LICENSE](../LICENSE) für Details.

---

**Letzte Aktualisierung:** 2025-01-15

