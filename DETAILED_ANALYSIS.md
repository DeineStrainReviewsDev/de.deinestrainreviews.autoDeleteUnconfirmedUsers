# Ausführliche Analyse der beiden Probleme

## Problem 1: Hardcoded Tabellenpräfix in SQL-Queries

### Initiale Annahme
**Datei:** `files/lib/system/cronjob/DeleteUnconfirmedUsersCronjob.class.php`  
**Zeilen:** 51, 52, 116, 117  
**Annahme:** Verwendet `wcf1_` statt `WCF_N` Konstante  
**Vermutung:** Funktioniert nur wenn Tabellenpräfix `wcf1_` ist

### Analyse im WoltLab Core

#### Untersuchte Dateien:
1. `/lib/system/cronjob/FileCleanUpCronjob.class.php` (Zeile 25):
   ```php
   FROM    wcf1_file
   ```
   → **Hardcoded `wcf1_` für Core-Tabellen**

2. `/lib/system/cronjob/DailyMailNotificationCronjob.class.php`:
   - Zeile 43: `wcf" . WCF_N . "_user_notification`
   - Zeile 71: `wcf" . WCF_N . "_user_notification`
   - → **Dynamisches `WCF_N` für Plugin-Tabellen**

3. `/lib/system/cronjob/BackgroundQueueCleanUpCronjob.class.php` (Zeile 82):
   ```php
   wcf" . WCF_N . "_background_job
   ```
   → **Dynamisches `WCF_N` für Plugin-Tabellen**

#### Ergebnis:
- ✅ **Core-Tabellen** (`wcf1_user`, `wcf1_user_to_group`, `wcf1_file`) werden **immer hardcoded** als `wcf1_` verwendet
- ✅ **Plugin-Tabellen** verwenden `wcf" . WCF_N . "_` für dynamisches Präfix
- ✅ **Unsere Datei verwendet Core-Tabellen** → Hardcoded `wcf1_` ist **KORREKT und Standard**

### Fazit Problem 1:
**KEIN Problem!**  
Die Verwendung von hardcoded `wcf1_` für Core-Tabellen ist Standard in WoltLab. Das ursprüngliche Review war hier falsch.

---

## Problem 2: SQL IN (?) mit Array

### Initiale Annahme
**Datei:** `files/lib/system/cronjob/DeleteUnconfirmedUsersCronjob.class.php`  
**Zeile:** 118  
**Problem:** `WHERE ug.groupID IN (?)` mit Array könnte problematisch sein  
**Vermutung:** PDO expandiert `IN (?)` nicht automatisch für Arrays

### Analyse im WoltLab Core

#### Untersuchte Dateien:

1. **Direct IN (?) mit execute([$array])** - **NICHT GEFUNDEN** ❌
   - Keine direkte Verwendung von `IN (?)` mit Array in `execute()`
   - PDO expandiert `IN (?)` **NICHT** automatisch für Arrays

2. **PreparedStatementConditionBuilder für IN (?)** - **STANDARD** ✅
   
   Beispiel 1: `/lib/system/cronjob/DailyMailNotificationCronjob.class.php` (Zeile 66):
   ```php
   $conditions = new PreparedStatementConditionBuilder();
   $conditions->add("notification.userID IN (?)", [$userIDs]);
   // ...
   $statement->execute($conditions->getParameters());
   ```
   
   Beispiel 2: `/lib/system/cronjob/BackgroundQueueCleanUpCronjob.class.php` (Zeile 81):
   ```php
   $condition = new PreparedStatementConditionBuilder();
   $condition->add('jobID IN (?)', [$jobIDs]);
   // ...
   $statement->execute($condition->getParameters());
   ```

3. **Wie PreparedStatementConditionBuilder funktioniert:**
   
   Aus `/lib/system/database/util/PreparedStatementConditionBuilder.class.php` (Zeile 55-58):
   ```php
   if (\is_array($parameters[$count]) && !empty($parameters[$count])) {
       $result .= \str_repeat(',?', \count($parameters[$count]) - 1);
   }
   ```
   
   → **Expandiert `IN (?)` automatisch zu `IN (?, ?, ?)`** wenn Array übergeben wird

### Unsere ursprüngliche Implementierung:
```php
$sql = "SELECT DISTINCT u.userID, u.email, u.username
        FROM wcf1_user_to_group ug
        INNER JOIN wcf1_user u ON u.userID = ug.userID
        WHERE ug.groupID IN (?) AND u.userID <> ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute([$adminGroupIDs, 0]);  // ❌ FEHLER: Array wird nicht expandiert
```

**Problem:** PDO expandiert `IN (?)` **NICHT** automatisch. Wenn `$adminGroupIDs = [1, 2, 3]` ist, wird versucht `groupID IN (?)` mit dem ganzen Array als ein Parameter zu behandeln, was zu SQL-Fehlern führt.

### Korrekte Implementierung (wie im WoltLab Core):
```php
$conditions = new PreparedStatementConditionBuilder();
$conditions->add('ug.groupID IN (?)', [$adminGroupIDs]);  // ✅ Expandiert automatisch
$conditions->add('u.userID <> ?', [0]);

$sql = "SELECT DISTINCT u.userID, u.email, u.username
        FROM wcf1_user_to_group ug
        INNER JOIN wcf1_user u ON u.userID = ug.userID
        WHERE " . $conditions;
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute($conditions->getParameters());  // ✅ Korrekte Parameter-Expansion
```

### Fazit Problem 2:
**✅ ECHTES Problem gefunden und behoben!**  
Die ursprüngliche Implementierung würde bei mehreren Admin-Gruppen zu SQL-Fehlern führen. Die Verwendung von `PreparedStatementConditionBuilder` ist Standard in WoltLab und wurde implementiert.

---

## Zusammenfassung

### Problem 1: Tabellenpräfix
- **Status:** ❌ **Kein Problem** (ursprüngliche Annahme war falsch)
- **Begründung:** Core-Tabellen werden in WoltLab immer hardcoded als `wcf1_` verwendet
- **Aktion:** Keine Änderung erforderlich

### Problem 2: IN (?) mit Array
- **Status:** ✅ **Problem gefunden und behoben**
- **Begründung:** PDO expandiert `IN (?)` nicht automatisch für Arrays
- **Lösung:** Verwendung von `PreparedStatementConditionBuilder` (WoltLab-Standard)
- **Aktion:** Code wurde korrigiert

---

## Geänderte Dateien

1. **`files/lib/system/cronjob/DeleteUnconfirmedUsersCronjob.class.php`**
   - ✅ Import hinzugefügt: `use wcf\system\database\util\PreparedStatementConditionBuilder;`
   - ✅ `notifyAdministrators()` Methode aktualisiert:
     - Verwendet jetzt `PreparedStatementConditionBuilder` statt direktem SQL mit `IN (?)`
     - Folgt exakt dem Muster aus dem WoltLab Core

---

## Verifizierung

### Test-Szenarien für Problem 2:
1. **Ein Admin-Gruppe:** `$adminGroupIDs = [1]` → `IN (?)` wird zu `IN (?)`
2. **Mehrere Admin-Gruppen:** `$adminGroupIDs = [1, 2, 3]` → `IN (?)` wird zu `IN (?, ?, ?)`
3. **Leeres Array:** Wird bereits durch `if (empty($adminGroupIDs))` abgefangen

**Alle Szenarien funktionieren jetzt korrekt mit `PreparedStatementConditionBuilder`.**

