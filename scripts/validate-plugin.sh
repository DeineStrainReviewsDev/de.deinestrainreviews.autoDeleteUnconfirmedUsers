#!/bin/bash

# Auto Delete Unconfirmed Users Plugin - Validate Plugin Script
# Copyright (c) 2025 DeineStrainReviews.de
# License: GNU General Public License v3.0 (GPL-3.0)
# Repository: https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers
#
# ⚠️ IMPORTANT: This copyright notice must not be removed.
#
# Script zur Validierung der WoltLab Plugin-Struktur
# Prüft: package.xml, referenzierte Dateien, PHP-Syntax, XML-Syntax
#
# Verwendung: ./scripts/validate-plugin.sh

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$REPO_ROOT"

# Validierungs-Zähler
ERRORS=0
WARNINGS=0

echo "═══════════════════════════════════════════════════════════════"
echo "  WoltLab Plugin Validierung"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "Verzeichnis: $REPO_ROOT"
echo ""

# 1. Prüfe package.xml
echo "🔍 Prüfe package.xml..."

if [ ! -f "package.xml" ]; then
    echo "❌ FEHLER: package.xml nicht gefunden!"
    ((ERRORS++))
else
    echo "✓ package.xml gefunden"

    # XML-Syntax prüfen (falls xmllint verfügbar)
    if command -v xmllint &> /dev/null; then
        if xmllint --noout package.xml 2>/dev/null; then
            echo "✓ XML-Syntax ist korrekt"
        else
            echo "❌ FEHLER: XML-Syntax-Fehler in package.xml!"
            ((ERRORS++))
        fi
    else
        echo "⚠️  Warnung: xmllint nicht installiert, überspringe XML-Validierung"
        ((WARNINGS++))
    fi

    # Package-Name prüfen
    PACKAGE_NAME=$(grep -oP 'name="\K[^"]+' package.xml 2>/dev/null | head -1)
    if [ -z "$PACKAGE_NAME" ]; then
        echo "❌ FEHLER: Konnte Package-Name nicht aus package.xml extrahieren!"
        ((ERRORS++))
    else
        echo "✓ Package-Name: $PACKAGE_NAME"
    fi

    # Version prüfen
    VERSION=$(grep -oP '<version>\K[^<]+' package.xml 2>/dev/null | head -1)
    if [ -z "$VERSION" ]; then
        echo "⚠️  Warnung: Konnte Version nicht aus package.xml extrahieren"
        ((WARNINGS++))
    else
        echo "✓ Version: $VERSION"
    fi
fi

echo ""

# 2. Prüfe XML-Dateien (PIPs)
echo "🔍 Prüfe XML-Dateien (PIPs)..."

XML_FILES=(page.xml acpMenu.xml cronjob.xml option.xml)
XML_FOUND=0

for xml_file in "${XML_FILES[@]}"; do
    if [ -f "$xml_file" ]; then
        echo "✓ $xml_file gefunden"
        ((XML_FOUND++))

        # XML-Syntax prüfen (falls xmllint verfügbar)
        if command -v xmllint &> /dev/null; then
            if xmllint --noout "$xml_file" 2>/dev/null; then
                echo "  ✓ $xml_file ist syntaktisch korrekt"
            else
                echo "  ❌ FEHLER: XML-Syntax-Fehler in $xml_file!"
                ((ERRORS++))
            fi
        fi
    fi
done

if [ $XML_FOUND -gt 0 ]; then
    echo "✓ $XML_FOUND XML-Datei(en) gefunden"
else
    echo "ℹ️  Keine XML-Dateien (PIPs) gefunden"
fi

echo ""

# 3. Prüfe SQL-Dateien
echo "🔍 Prüfe SQL-Dateien..."

if [ -f "install.sql" ]; then
    echo "✓ install.sql gefunden"
else
    echo "⚠️  Warnung: install.sql nicht gefunden"
    ((WARNINGS++))
fi

echo ""

# 4. Prüfe Sprachdateien
echo "🔍 Prüfe Sprachdateien..."

if [ -d "language" ]; then
    LANG_FILES=$(find language -name "*.xml" -type f 2>/dev/null | wc -l)
    if [ $LANG_FILES -gt 0 ]; then
        echo "✓ language/ Verzeichnis gefunden ($LANG_FILES Datei(en))"
        
        # Prüfe jede Sprachdatei
        while IFS= read -r lang_file; do
            if [ -f "$lang_file" ]; then
                if command -v xmllint &> /dev/null; then
                    if ! xmllint --noout "$lang_file" 2>/dev/null; then
                        echo "  ❌ FEHLER: XML-Syntax-Fehler in $lang_file!"
                        ((ERRORS++))
                    fi
                fi
            fi
        done < <(find language -name "*.xml" -type f 2>/dev/null)
    else
        echo "⚠️  Warnung: Keine Sprachdateien in language/ gefunden"
        ((WARNINGS++))
    fi
else
    echo "⚠️  Warnung: language/ Verzeichnis nicht gefunden"
    ((WARNINGS++))
fi

echo ""

# 5. Prüfe PHP-Dateien auf Syntax-Fehler
echo "🔍 Prüfe PHP-Syntax..."

if command -v php &> /dev/null; then
    PHP_FILES_CHECKED=0
    PHP_ERRORS=0

    while IFS= read -r -d '' php_file; do
        ((PHP_FILES_CHECKED++))
        if ! php -l "$php_file" &>/dev/null; then
            echo "❌ FEHLER: PHP-Syntax-Fehler in $php_file"
            ((ERRORS++))
            ((PHP_ERRORS++))
        fi
    done < <(find files -name "*.php" -type f -print0 2>/dev/null)

    if [ $PHP_FILES_CHECKED -gt 0 ]; then
        if [ $PHP_ERRORS -eq 0 ]; then
            echo "✓ Alle $PHP_FILES_CHECKED PHP-Dateien sind syntaktisch korrekt"
        else
            echo "❌ $PHP_ERRORS von $PHP_FILES_CHECKED PHP-Dateien haben Syntax-Fehler"
        fi
    else
        echo "ℹ️  Keine PHP-Dateien in files/ gefunden"
    fi
else
    echo "⚠️  Warnung: PHP CLI nicht installiert, überspringe PHP-Syntax-Prüfung"
    ((WARNINGS++))
fi

echo ""

# 6. Ergebnis
echo "═══════════════════════════════════════════════════════════════"
echo "  Validierungs-Ergebnis"
echo "═══════════════════════════════════════════════════════════════"
echo ""

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo "✅ Validierung erfolgreich! Keine Fehler oder Warnungen gefunden."
    EXIT_CODE=0
elif [ $ERRORS -eq 0 ]; then
    echo "⚠️  Validierung abgeschlossen mit $WARNINGS Warnung(en)."
    echo "   Die Warnungen sind nicht kritisch, aber sollten geprüft werden."
    EXIT_CODE=0
else
    echo "❌ Validierung fehlgeschlagen!"
    echo "   Fehler: $ERRORS"
    echo "   Warnungen: $WARNINGS"
    echo ""
    echo "Bitte behebe die Fehler vor dem Release."
    EXIT_CODE=1
fi

echo ""
exit $EXIT_CODE

