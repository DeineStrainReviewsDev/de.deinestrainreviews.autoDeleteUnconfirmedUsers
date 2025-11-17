#!/bin/bash

PACKAGE_INFO_FILE=".packageinfo"

getPackageInfoProperty() {
  grep "$1" "$PACKAGE_INFO_FILE" | cut -d'=' -f2
}

echo "Starting to build the package..."
echo "Reading the ${PACKAGE_INFO_FILE} file..."

if [ ! -f "$PACKAGE_INFO_FILE" ]; then
  echo "The ${PACKAGE_INFO_FILE} file does not exist, but it required to build the package."
  exit 1
fi

PACKAGE_IDENTIFIER=$(getPackageInfoProperty "packageIdentifier")

if [ -z "$PACKAGE_IDENTIFIER" ]; then
  echo "Please specify a valid packageIdentifier property in the ${PACKAGE_INFO_FILE} file."
  exit 1
fi

BUILD_DIRECTORY="build"
PACKAGE_FILENAME="${PACKAGE_IDENTIFIER}.tar"

echo "Cleaning up build directory..."
rm -rf $BUILD_DIRECTORY
mkdir -p $BUILD_DIRECTORY

echo "Building the archives file..."

ARCHIVES=("acptemplates" "files" "style" "templates")
PACKAGE_ARCHIVES=$(getPackageInfoProperty "packageArchives" | tr ";" "\n")

if [ -n "$PACKAGE_ARCHIVES" ]; then
  ARCHIVES+=("$PACKAGE_ARCHIVES")
fi

printf "%s\n" "${ARCHIVES[@]}" > "$BUILD_DIRECTORY/archives"

echo "Building the package..."

# Disable copying of *_ files on macOS.
export COPYFILE_DISABLE=1

git archive --format=tar --worktree-attributes --output="$BUILD_DIRECTORY/$PACKAGE_FILENAME" HEAD

for ARCHIVE in "${ARCHIVES[@]}"; do
  ARCHIVE_FILENAME="${ARCHIVE}.tar"

  if [ -d "$ARCHIVE" ]; then
    if [ "$ARCHIVE" = "acptemplates" ]; then
      # For acptemplates, files must be in root of archive (no directories)
      # Create archive with files from acptemplates/ directory in root
      if [ -n "$(find "$ARCHIVE" -type f -name "*.tpl" 2>/dev/null | head -1)" ]; then
        TMP_TAR=$(mktemp)
        (cd "$ARCHIVE" && find . -type f -name "*.tpl" | while read file; do
          REL_FILE=$(echo "$file" | sed 's|^\./||')
          if [ -f "$REL_FILE" ]; then
            if [ ! -s "$TMP_TAR" ]; then
              tar -cf "$TMP_TAR" --transform='s|.*/||' "$REL_FILE" 2>/dev/null
            else
              tar -rf "$TMP_TAR" --transform='s|.*/||' "$REL_FILE" 2>/dev/null
            fi
          fi
        done)
        if [ -f "$TMP_TAR" ] && [ -s "$TMP_TAR" ]; then
          mv "$TMP_TAR" "$BUILD_DIRECTORY/$ARCHIVE_FILENAME"
          (cd "$BUILD_DIRECTORY" && tar -rf "$PACKAGE_FILENAME" "$ARCHIVE_FILENAME" && rm "$ARCHIVE_FILENAME")
        else
          rm -f "$TMP_TAR"
        fi
      fi
    else
      git archive --format=tar --worktree-attributes --output="$BUILD_DIRECTORY/$ARCHIVE_FILENAME" HEAD:"$ARCHIVE"
      (cd "$BUILD_DIRECTORY" && tar -rf "$PACKAGE_FILENAME" "$ARCHIVE_FILENAME" && rm "$ARCHIVE_FILENAME")
    fi
  fi
done

echo "Finished building the package!"
exit 0
