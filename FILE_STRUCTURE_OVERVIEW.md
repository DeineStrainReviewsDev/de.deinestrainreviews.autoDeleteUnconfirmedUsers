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

### 12. `files/lib/system/deleted/unconfirmed/user/DeletedUnconfirmedUserLogHandler.class.php`
**Zuständigkeit:** Handler-Klasse für Log-Operationen  
**Namespace:** `wcf\system\deleted\unconfirmed\user`  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUserLogHandler.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/system/deleted/unconfirmed/user/`  
**Dokumentation:** https://docs.woltlab.com/6.1/getting-started/  
**Status:** ⚠️ **ZU PRÜFEN** - Handler-Klassen sind nicht explizit dokumentiert, aber Namespace-Struktur ist korrekt

### 13. `files/lib/acp/page/DeletedUnconfirmedUsersLogPage.class.php`
**Zuständigkeit:** ACP-Seite für Log-Anzeige  
**Namespace:** `wcf\acp\page` ✅  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUsersLogPage.class.php`  
**Verzeichnis:** ✅ Korrekt - `files/lib/acp/page/`  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/page/  
**Status:** ✅ Korrekt - Erweitert `AbstractAcpPage`

### 14. `files/lib/acp/page/DeletedUnconfirmedUsersLogPage.tpl`
**Zuständigkeit:** Template für ACP-Seite  
**Dateiname:** ✅ Korrekt - `DeletedUnconfirmedUsersLogPage.tpl`  
**Verzeichnis:** ✅ Korrekt - `files/lib/acp/page/` (gleicher Ordner wie PHP-Klasse)  
**Dokumentation:** https://docs.woltlab.com/6.1/package/pip/page/  
**Status:** ✅ Korrekt - Template im gleichen Verzeichnis wie die Page-Klasse

---

## 🔍 Potenzielle Probleme / Zu Prüfende Punkte

### ⚠️ Handler-Klasse Namespace
**Datei:** `files/lib/system/deleted/unconfirmed/user/DeletedUnconfirmedUserLogHandler.class.php`  
**Problem:** Handler-Klassen sind nicht explizit in der Dokumentation beschrieben  
**Lösung:** Könnte alternativ in `files/lib/data/deleted/unconfirmed/user/log/` oder als statische Methoden in der Data-Klasse sein  
**Empfehlung:** Prüfen ob Handler-Pattern in WoltLab üblich ist oder ob statische Methoden in Data-Klasse bevorzugt werden

### ⚠️ Namespace-Struktur für "deleted/unconfirmed/user"
**Aktuell:** `wcf\data\deleted\unconfirmed\user\log`  
**Frage:** Ist diese tiefe Verschachtelung Standard?  
**Alternative:** `wcf\data\deletedUnconfirmedUserLog` (flacher)  
**Empfehlung:** In WoltLab-Core prüfen, wie ähnliche Log-Tabellen strukturiert sind

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
- ⚠️ Handler-Klasse Namespace (möglicherweise nicht Standard)
- ⚠️ Tiefe Namespace-Verschachtelung (möglicherweise zu tief)

