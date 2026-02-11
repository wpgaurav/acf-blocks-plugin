#!/usr/bin/env bash
set -euo pipefail

SLUG="acf-blocks"
VERSION=$(grep -m1 "Version:" acf-blocks.php | sed 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')
BUILD_DIR="/tmp/${SLUG}-build"
ZIP_NAME="${SLUG}-${VERSION}.zip"

echo "Building ${SLUG} v${VERSION}..."

rm -rf "${BUILD_DIR}"
mkdir -p "${BUILD_DIR}/${SLUG}"

# Copy plugin files.
cp acf-blocks.php "${BUILD_DIR}/${SLUG}/"
cp -r includes "${BUILD_DIR}/${SLUG}/"
cp -r assets "${BUILD_DIR}/${SLUG}/"
cp -r blocks "${BUILD_DIR}/${SLUG}/"

# Clean junk files.
find "${BUILD_DIR}" -name ".DS_Store" -delete 2>/dev/null || true
find "${BUILD_DIR}" -name "__MACOSX" -exec rm -rf {} + 2>/dev/null || true
find "${BUILD_DIR}" -name ".gitignore" -delete 2>/dev/null || true

# Build zip.
cd "${BUILD_DIR}"
zip -rq "${ZIP_NAME}" "${SLUG}"
mv "${ZIP_NAME}" "${OLDPWD}/"
cd "${OLDPWD}"

# Cleanup.
rm -rf "${BUILD_DIR}"

echo "Done: ${ZIP_NAME} ($(du -h "${ZIP_NAME}" | cut -f1))"
