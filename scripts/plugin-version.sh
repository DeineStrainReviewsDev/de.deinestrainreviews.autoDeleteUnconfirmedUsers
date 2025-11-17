#!/bin/bash

# Auto Delete Unconfirmed Users Plugin - Version Management Script
# Copyright (c) 2025 DeineStrainReviews.de
# License: GNU General Public License v3.0 (GPL-3.0)
# Repository: https://github.com/DeineStrainReviewsDev/de.deinestrainreviews.autoDeleteUnconfirmedUsers
#
# ⚠️ IMPORTANT: This copyright notice must not be removed.
#
# This script manages version numbers for WoltLab plugins
# It automatically increments the version in package.xml and updates the date
#
# Usage: ./scripts/plugin-version.sh [major|minor|patch] [--no-tag]
#   major: Increment major version (1.1.2 -> 2.0.0) - Breaking changes
#   minor: Increment minor version (1.1.2 -> 1.2.0) - New features
#   patch: Increment patch version (1.1.2 -> 1.1.3) - Bug fixes
#   --no-tag: Only update version, don't create git tag

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$REPO_ROOT"

# Parse arguments
INCREMENT_TYPE=""
NO_TAG=false

for arg in "$@"; do
    case "$arg" in
        major|minor|patch)
            INCREMENT_TYPE="$arg"
            ;;
        --no-tag)
            NO_TAG=true
            ;;
    esac
done

if [ -z "$INCREMENT_TYPE" ]; then
    echo "❌ Fehler: Inkrement-Typ fehlt!"
    echo ""
    echo "Verwendung: $0 [major|minor|patch] [--no-tag]"
    echo ""
    echo "  major: Hauptversion (1.1.2 -> 2.0.0) - Breaking Changes"
    echo "  minor: Nebenversion (1.1.2 -> 1.2.0) - Neue Features"
    echo "  patch: Patch-Version (1.1.2 -> 1.1.3) - Bugfixes"
    echo ""
    echo "Optionen:"
    echo "  --no-tag: Nur Version aktualisieren, kein Git-Tag erstellen"
    exit 1
fi

# Prüfe ob package.xml existiert
if [ ! -f "package.xml" ]; then
    echo "❌ Fehler: package.xml nicht gefunden in $REPO_ROOT"
    exit 1
fi

# Lese aktuelle Version
CURRENT_VERSION=$(grep -oP '<version>\K[^<]+' package.xml | head -1)
if [ -z "$CURRENT_VERSION" ]; then
    echo "❌ Fehler: Konnte Version nicht aus package.xml lesen"
    exit 1
fi

# Parse version
IFS='.' read -ra VERSION_PARTS <<< "$CURRENT_VERSION"
MAJOR="${VERSION_PARTS[0]}"
MINOR="${VERSION_PARTS[1]}"
PATCH="${VERSION_PARTS[2]}"

# Calculate new version
case "$INCREMENT_TYPE" in
    major)
        NEW_MAJOR=$((MAJOR + 1))
        NEW_MINOR=0
        NEW_PATCH=0
        ;;
    minor)
        NEW_MAJOR=$MAJOR
        NEW_MINOR=$((MINOR + 1))
        NEW_PATCH=0
        ;;
    patch)
        NEW_MAJOR=$MAJOR
        NEW_MINOR=$MINOR
        NEW_PATCH=$((PATCH + 1))
        ;;
esac

NEW_VERSION="${NEW_MAJOR}.${NEW_MINOR}.${NEW_PATCH}"

echo "═══════════════════════════════════════════════════════════════"
echo "  Plugin-Versionsverwaltung"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "Plugin-Verzeichnis: $REPO_ROOT"
echo "Aktuelle Version: $CURRENT_VERSION"
echo "Neue Version: $NEW_VERSION"
echo "Inkrement-Typ: $INCREMENT_TYPE"
echo ""

read -p "Möchtest du die Version auf $NEW_VERSION erhöhen? (j/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[JjYy]$ ]]; then
    echo "Abgebrochen."
    exit 0
fi

# Update version in package.xml
echo ""
echo "📝 Aktualisiere package.xml..."

# Update version
sed -i "s/<version>$CURRENT_VERSION<\/version>/<version>$NEW_VERSION<\/version>/g" package.xml

# Update date
TODAY=$(date +%Y-%m-%d)
sed -i "s/<date>[0-9]\{4\}-[0-9]\{2\}-[0-9]\{2\}<\/date>/<date>$TODAY<\/date>/g" package.xml

echo "✅ Version aktualisiert: $CURRENT_VERSION → $NEW_VERSION"
echo "✅ Datum aktualisiert: $TODAY"
echo ""

# Create git tag if not disabled
if [ "$NO_TAG" = false ]; then
    echo "🏷️  Erstelle Git-Tag..."
    
    TAG_MESSAGE="Version $NEW_VERSION

$(case "$INCREMENT_TYPE" in
    major) echo "Breaking Changes - Major Update" ;;
    minor) echo "New Features - Minor Update" ;;
    patch) echo "Bugfixes - Patch Update" ;;
esac)"
    
    if git tag -a "v$NEW_VERSION" -m "$TAG_MESSAGE" 2>/dev/null; then
        echo "✅ Git-Tag erstellt: v$NEW_VERSION"
        echo ""
        echo "Nächste Schritte:"
        echo "1. Änderungen committen:"
        echo "   git add package.xml"
        echo "   git commit -m \"chore: Version auf $NEW_VERSION erhöht\""
        echo ""
        echo "2. Tag pushen:"
        echo "   git push origin v$NEW_VERSION"
        echo ""
        echo "3. Änderungen pushen:"
        echo "   git push origin develop"
    else
        echo "⚠️  Warnung: Git-Tag konnte nicht erstellt werden (möglicherweise existiert bereits)"
    fi
fi

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "  ✅ Version erfolgreich auf $NEW_VERSION erhöht"
echo "═══════════════════════════════════════════════════════════════"
echo ""

