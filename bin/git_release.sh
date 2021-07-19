#!/usr/bin/env bash
# Create a release on GitHub.
# Extracts the most recent changelog section from readme.txt
# if readme.txt is in standard WordPress format.
# Also makes a separate backup of the whole thing including hidden files and phpunit tests.
# Prerequisite: Main plugin file slug must be the same as the plugin folder name.
# Prerequisite: Existing git repo with its remote origin set up on GitHub. Both repo names must match the plugin slug, exactly.
# Configure the first few variables.
cd ..
set -e
#config
SLUG=${PWD##*/}
CURRENTDIR=`pwd`
MAINFILE="LatitudeFinance.php"
timestamp=$(date +%Y%m%d_%H%M%S) # +%Y%m%d_%H%M%S
# Get version from main plugin file
NEWVERSION=`grep -Po "(^|\s)+(Version: )\K([0-9]|\.)*(?=\s|$)" "$CURRENTDIR/$MAINFILE"`
if [[ -z "$NEWVERSION" ]]; then echo "ERROR: Cannot find version. Exiting early...."; exit 1; fi
 
STABLEVERSION=`grep -Po "(^|\s)+(Stable tag: )\K([0-9]|\.)*(?=\s|$)" "$CURRENTDIR/readme.txt"`
 
echo "readme version: $STABLEVERSION"
echo "$MAINFILE version: $NEWVERSION"
 
if [ "$STABLEVERSION" != "$NEWVERSION" ]; then echo "Versions don't match. Exiting...."; exit 1; fi
 
echo "Versions match in readme.txt and PHP file. Let's proceed..."
 
# Create a Release on GitHub
 
echo "Creating a new release on GitHub"
# Get changelog text from readme
BEGINLINE=`awk '/Changelog/{ print NR; exit }' "$CURRENTDIR/readme.txt"`
BEGINLINE=$((BEGINLINE+1)) 
ENDLINE=`grep -n '= ' "$CURRENTDIR/readme.txt"| sed -n '4 s/:.*//p'`
ENDLINE=$((ENDLINE-1)) 
CHANGELOG=`sed -n -e "${BEGINLINE},${ENDLINE}p" "$CURRENTDIR/readme.txt"`
CHANGELOG_JSON="${CHANGELOG//$'\n'/'\n'}"
 
API_JSON=$(printf '{"tag_name": "%s","target_commitish": "ci-cd","name": "%s","body": "%s","draft": false,"prerelease": false}' $STABLEVERSION $STABLEVERSION "$CHANGELOG_JSON")

curl --data "$API_JSON" https://api.github.com/repos/${GITHUB_USER}/${SLUG}/releases?access_token=${GITHUB_TOKEN}

echo "*** FIN ***"