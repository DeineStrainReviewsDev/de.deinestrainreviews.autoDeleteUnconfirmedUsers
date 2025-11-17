# Analyse der Namespace-Struktur für Log-Tabellen

## Aktuelle Struktur

**Unser Namespace:** `wcf\data\deleted\unconfirmed\user\log`  
**Pfad:** `files/lib/data/deleted/unconfirmed/user/log/`  
**Ebenen:** 5 Ebenen (`deleted/unconfirmed/user/log`)

## Vergleich mit WoltLab Core Log-Tabellen

### 1. CronjobLog (3 Ebenen)
**Namespace:** `wcf\data\cronjob\log`  
**Pfad:** `/lib/data/cronjob/log/`  
**Struktur:** `wcf\data\[hauptkategorie]\log`

**Dateien:**
- `CronjobLog.class.php`
- `CronjobLogList.class.php`
- `CronjobLogEditor.class.php`
- `CronjobLogAction.class.php`

**Tabelle:** `wcf1_cronjob_log`

---

### 2. EmailLogEntry (4 Ebenen)
**Namespace:** `wcf\data\email\log\entry`  
**Pfad:** `/lib/data/email/log/entry/`  
**Struktur:** `wcf\data\[hauptkategorie]\log\[detail]`

**Dateien:**
- `EmailLogEntry.class.php`
- `EmailLogEntryList.class.php`
- `EmailLogEntryEditor.class.php`
- `EmailLogEntryAction.class.php`

**Tabelle:** `wcf1_email_log_entry`

---

### 3. ACPSessionLog (4 Ebenen)
**Namespace:** `wcf\data\acp\session\log`  
**Pfad:** `/lib/data/acp/session/log/`  
**Struktur:** `wcf\data\[hauptkategorie]\[unterkategorie]\log`

**Dateien:**
- `ACPSessionLog.class.php`
- `ACPSessionLogList.class.php`
- `ACPSessionLogEditor.class.php`
- `ACPSessionLogAction.class.php`

**Tabelle:** `wcf1_acp_session_log`

---

### 4. ACPSessionAccessLog (5 Ebenen) ⭐
**Namespace:** `wcf\data\acp\session\access\log`  
**Pfad:** `/lib/data/acp/session/access/log/`  
**Struktur:** `wcf\data\[hauptkategorie]\[unterkategorie]\[detail]\log`

**Dateien:**
- `ACPSessionAccessLog.class.php`
- `ACPSessionAccessLogList.class.php`
- `ACPSessionAccessLogEditor.class.php`
- `ACPSessionAccessLogAction.class.php`

**Tabelle:** `wcf1_acp_session_access_log`

---

### 5. PaidSubscriptionTransactionLog (5 Ebenen) ⭐
**Namespace:** `wcf\data\paid\subscription\transaction\log`  
**Pfad:** `/lib/data/paid/subscription/transaction/log/`  
**Struktur:** `wcf\data\[hauptkategorie]\[unterkategorie]\[detail]\log`

**Dateien:**
- `PaidSubscriptionTransactionLog.class.php`
- `PaidSubscriptionTransactionLogList.class.php`
- `PaidSubscriptionTransactionLogEditor.class.php`
- `PaidSubscriptionTransactionLogAction.class.php`

**Tabelle:** `wcf1_paid_subscription_transaction_log`

---

### 6. ModificationLog (3 Ebenen)
**Namespace:** `wcf\data\modification\log`  
**Pfad:** `/lib/data/modification/log/`  
**Struktur:** `wcf\data\[hauptkategorie]\log`

**Dateien:**
- `ModificationLog.class.php`
- `ModificationLogList.class.php`
- `ModificationLogEditor.class.php`
- `ModificationLogAction.class.php`

**Tabelle:** `wcf1_modification_log`

---

## Analyse der Struktur-Patterns

### Pattern 1: Einfache Log-Struktur (3 Ebenen)
```
wcf\data\[hauptkategorie]\log
```
**Beispiele:**
- `wcf\data\cronjob\log`
- `wcf\data\modification\log`

**Verwendung:** Wenn die Log-Tabelle direkt zu einer Hauptkategorie gehört.

---

### Pattern 2: Unterkategorie mit Log (4 Ebenen)
```
wcf\data\[hauptkategorie]\[unterkategorie]\log
```
**Beispiele:**
- `wcf\data\acp\session\log`
- `wcf\data\email\log\entry` (Variante mit `entry` statt direkt `log`)

**Verwendung:** Wenn die Log-Tabelle zu einer spezifischen Unterkategorie gehört.

---

### Pattern 3: Tiefe Verschachtelung (5 Ebenen) ⭐
```
wcf\data\[hauptkategorie]\[unterkategorie]\[detail]\log
```
**Beispiele:**
- `wcf\data\acp\session\access\log` (Access-Log zu Sessions)
- `wcf\data\paid\subscription\transaction\log` (Transaction-Log zu Subscriptions)
- `wcf\data\deleted\unconfirmed\user\log` (Unser Log)

**Verwendung:** Wenn die Log-Tabelle zu einem sehr spezifischen Detail-Bereich gehört.

---

## Vergleich: Unsere Struktur vs. WoltLab Core

### Unsere Struktur:
```
wcf\data\deleted\unconfirmed\user\log
```

### Ähnliche Strukturen im Core:

1. **PaidSubscriptionTransactionLog** (5 Ebenen):
   ```
   wcf\data\paid\subscription\transaction\log
   ```
   - `paid` = Hauptkategorie
   - `subscription` = Unterkategorie
   - `transaction` = Detail
   - `log` = Log-Ebene

2. **ACPSessionAccessLog** (5 Ebenen):
   ```
   wcf\data\acp\session\access\log
   ```
   - `acp` = Hauptkategorie
   - `session` = Unterkategorie
   - `access` = Detail
   - `log` = Log-Ebene

3. **Unsere Struktur** (5 Ebenen):
   ```
   wcf\data\deleted\unconfirmed\user\log
   ```
   - `deleted` = Hauptkategorie
   - `unconfirmed` = Unterkategorie
   - `user` = Detail
   - `log` = Log-Ebene

---

## Fazit

### ✅ Unsere Struktur ist Standard-konform!

**Begründung:**
1. ✅ **Tiefe Verschachtelung ist Standard:** WoltLab Core verwendet ebenfalls 5 Ebenen für spezifische Logs
2. ✅ **Struktur-Pattern passt:** Folgt dem gleichen Muster wie `PaidSubscriptionTransactionLog` und `ACPSessionAccessLog`
3. ✅ **Logische Hierarchie:** 
   - `deleted` = Was wurde gelöscht? (Hauptkategorie)
   - `unconfirmed` = Welche Art? (Unterkategorie)
   - `user` = Welcher Typ? (Detail)
   - `log` = Log-Ebene

### ❌ Alternative wäre NICHT Standard-konform

**Alternative:** `wcf\data\deletedUnconfirmedUserLog` (flach, 2 Ebenen)

**Warum das nicht passt:**
- ❌ Keine Hierarchie (alles in einem Namen)
- ❌ Nicht konsistent mit WoltLab Core-Patterns
- ❌ Schwerer erweiterbar (z.B. wenn später andere "deleted" Logs hinzukommen)
- ❌ Widerspricht dem WoltLab-Konzept der logischen Verschachtelung

### ✅ Empfehlung

**Aktuelle Struktur beibehalten!**  
Die 5-stufige Verschachtelung `wcf\data\deleted\unconfirmed\user\log` ist:
- ✅ Standard-konform mit WoltLab Core
- ✅ Logisch strukturiert
- ✅ Erweiterbar für zukünftige Features
- ✅ Konsistent mit ähnlichen Core-Logs

---

## Vergleichstabelle

| Log-Typ | Namespace | Ebenen | Hauptkategorie | Unterkategorie | Detail | Log |
|---------|-----------|--------|----------------|----------------|--------|-----|
| **CronjobLog** | `wcf\data\cronjob\log` | 3 | cronjob | - | - | log |
| **ModificationLog** | `wcf\data\modification\log` | 3 | modification | - | - | log |
| **ACPSessionLog** | `wcf\data\acp\session\log` | 4 | acp | session | - | log |
| **EmailLogEntry** | `wcf\data\email\log\entry` | 4 | email | log | entry | - |
| **ACPSessionAccessLog** | `wcf\data\acp\session\access\log` | 5 | acp | session | access | log |
| **PaidSubscriptionTransactionLog** | `wcf\data\paid\subscription\transaction\log` | 5 | paid | subscription | transaction | log |
| **DeletedUnconfirmedUserLog** | `wcf\data\deleted\unconfirmed\user\log` | 5 | deleted | unconfirmed | user | log |

**Alle 5-stufigen Logs im Core folgen dem gleichen Pattern wie unsere Struktur!**

