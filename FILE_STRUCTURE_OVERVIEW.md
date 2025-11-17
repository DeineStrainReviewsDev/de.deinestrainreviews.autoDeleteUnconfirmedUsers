# Datei-Struktur Übersicht - WoltLab Standards Abgleich

## 📋 Root-Verzeichnis Dateien

### 1. `package.xml`
**Zuständigkeit:** Hauptpaket-Definition, steuert Installation/Update  
**PIP:** Kein PIP, sondern Hauptdatei  
**Dokumentation:** https://docs.woltlab.com/6.1/package/package-xml/  
**Status:** ✅ Korrekt - Enthält alle PIP-Instructions

### 2. `cronjob.xml`
**Zuständigkeit:** Cronjob-Definition  
**PIP:** `cronjob`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/cronjob/  
**Dateiname:** ✅ Korrekt - `cronjob.xml` im Root  
**Status:** ✅ Korrekt

### 3. `option.xml`
**Zuständigkeit:** ACP-Optionen-Definition  
**PIP:** `option`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/option/  
**Dateiname:** ✅ Korrekt - `option.xml` im Root  
**Status:** ✅ Korrekt

### 4. `acpMenu.xml`
**Zuständigkeit:** ACP-Menü-Definition  
**PIP:** `acpMenu`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/acp-menu/  
**Dateiname:** ✅ Korrekt - `acpMenu.xml` im Root  
**Status:** ✅ Korrekt

### 5. `page.xml`
**Zuständigkeit:** Page-Registrierung (ACP-Seite)  
**PIP:** `page`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/page/  
**Dateiname:** ✅ Korrekt - `page.xml` im Root  
**Status:** ✅ Korrekt

### 6. `install.sql`
**Zuständigkeit:** SQL-Anweisungen für Datenbanktabelle  
**PIP:** `sql`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/sql/  
**Dateiname:** ✅ Korrekt - `install.sql` im Root  
**Status:** ✅ Korrekt

---

## 📁 `language/` Verzeichnis

### 7. `language/de.xml`
**Zuständigkeit:** Deutsche Sprachvariablen  
**PIP:** `language`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/language/  
**Dateiname:** ✅ Korrekt - `de.xml` im `language/` Verzeichnis  
**Status:** ✅ Korrekt

### 8. `language/en.xml`
**Zuständigkeit:** Englische Sprachvariablen  
**PIP:** `language`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/language/  
**Dateiname:** ✅ Korrekt - `en.xml` im `language/` Verzeichnis  
**Status:** ✅ Korrekt

---

## 📁 `files/lib/` Verzeichnis (wird über `file` PIP installiert)

### 9. `files/lib/system/cronjob/DeleteUnconfirmedUsersCronjob.class.php`
**Zuständigkeit:** Cronjob-Implementierung  
**Namespace:** `wcf\system\cronjob` ✅  
**Dateiname:** ✅ Korrekt - `DeleteUnconfirmedUsersCronjob.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/system/cronjob/`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/cronjob/  
**Status:** ✅ Korrekt

### 10. `files/lib/data/deleted/unconfirmed/user/log/DeletedUnconfirmedUserLog.class.php`
**Zuständigkeit:** DatabaseObject für Log-Einträge  
**Namespace:** `wcf\data\deleted\unconfirmed\user\log`  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUserLog.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/data/deleted/unconfirmed/user/log/`  
**Dokumentation:** https://docs.woltlab.com/6.1/php/database-objects/  
**Status:** ✅ Korrekt - Erweitert `DatabaseObject`

### 11. `files/lib/data/deleted/unconfirmed/user/log/DeletedUnconfirmedUserLogList.class.php`
**Zuständigkeit:** DatabaseObjectList für Log-Abfragen  
**Namespace:** `wcf\data\deleted\unconfirmed\user\log`  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUserLogList.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/data/deleted/unconfirmed/user/log/`  
**Dokumentation:** https://docs.woltlab.com/6.1/php/database-objects/  
**Status:** ✅ Korrekt - Erweitert `DatabaseObjectList`

### 12. `files/lib/data/deleted/unconfirmed/user/log/DeletedUnconfirmedUserLogEditor.class.php`
**Zuständigkeit:** Editor-Klasse für Log-Einträge  
**Namespace:** `wcf\data\deleted\unconfirmed\user\log`  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUserLogEditor.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/data/deleted/unconfirmed/user/log/`  
**Dokumentation:** https://docs.woltlab.com/6.1/php/database-objects/  
**Status:** ✅ Korrekt - Erweitert `DatabaseObjectEditor` (wie CronjobLogEditor im WoltLab Core)

### 13. `files/lib/acp/page/DeletedUnconfirmedUsersLogPage.class.php`
**Zuständigkeit:** ACP-Seite für Log-Anzeige  
**Namespace:** `wcf\acp\page` ✅  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUsersLogPage.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/acp/page/`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/page/  
**Status:** ✅ Korrekt - Erweitert `SortablePage` (wie CronjobLogListPage im WoltLab Core)

### 14. `files/lib/acp/page/DeletedUnconfirmedUsersLogPage.tpl`
**Zuständigkeit:** Template für ACP-Seite  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUsersLogPage.tpl`  
**Verzeichnis:** ✅ Korrekt - `files/lib/acp/page/` (gleicher Ordner wie PHP-Klasse)  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/page/  
**Status:** ✅ Korrekt - Template im gleichen Verzeichnis wie die Page-Klasse

---

## 🔍 Potenzielle Probleme / Zu Prüfende Punkte

~~### ⚠️ Handler-Klasse Namespace~~  
**Status:** ✅ **BEHOBEN** - Handler-Klasse wurde durch Standard `DatabaseObjectEditor` ersetzt

### ✅ Namespace-Struktur für "deleted/unconfirmed/user"
**Aktuell:** `wcf\data\deleted\unconfirmed\user\log`  
**Status:** ✅ **KORREKT** (ursprüngliche Annahme war falsch)  
**Begründung:** Tiefe Verschachtelung (5 Ebenen) ist Standard in WoltLab Core. Ähnliche Strukturen:
- `wcf\data\acp\session\access\log` (ACPSessionAccessLog) - 5 Ebenen
- `wcf\data\paid\subscription\transaction\log` (PaidSubscriptionTransactionLog) - 5 Ebenen
- Unsere Struktur folgt dem gleichen Pattern: `wcf\data\[hauptkategorie]\[unterkategorie]\[detail]\log`
**Dokumentation:** Siehe `NAMESPACE_ANALYSIS.md` für ausführliche Analyse

---

## ✅ Zusammenfassung

**Korrekt implementiert:**
- ✅ Alle PIP-Dateien im Root-Verzeichnis
- ✅ Sprachdateien im `language/` Verzeichnis
- ✅ PHP-Klassen in `files/lib/` mit korrekten Namespaces
- ✅ ACP-Seite mit Template im gleichen Verzeichnis
- ✅ DatabaseObject und DatabaseObjectList korrekt implementiert
- ✅ Cronjob korrekt implementiert

**Zu prüfen:**
- ✅ Alle Punkte geprüft und korrekt

**Bekannte Abweichungen:**
- ⚠️ Plugin-Name verwendet CamelCase (`autoDeleteUnconfirmedUsers`) statt Bindestriche (`auto-delete-unconfirmed-users`)
  - **Grund:** Name wird beibehalten, um Update-Kompatibilität zu gewährleisten
  - **Hinweis:** Für zukünftige Plugins die WoltLab-Namenskonventionen beachten (siehe: https://github.com/SunnyCueq/simple-woltlab-plugin-manager/blob/main/docs/PLUGIN-NAMING.md)

