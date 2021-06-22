#!/usr/bin/env bash
# Create a release on Wordpress.org.
# Extracts the most recent changelog section from readme.txt
# if readme.txt is in standard WordPress format.
# Also makes a separate backup of the whole thing including hidden files and phpunit tests.
# Prerequisite: Main plugin file slug must be the same as the plugin folder name.
# Prerequisite: Existing git repo with its remote origin set up on GitHub. Both repo names must match the plugin slug, exactly.
# Configure the first few variables.
cd ..
set -e
#config
SVNDIR="/var/www/svn"
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
 
# Create a Release on Wordpress.org
 
echo "Creating a new release on Wordpress.org"
# Get changelog text from readme
BEGINLINE=`awk '/Changelog/{ print NR; exit }' "$CURRENTDIR/readme.txt"`
BEGINLINE=$((BEGINLINE+1)) 
ENDLINE=`grep -n '= ' "$CURRENTDIR/readme.txt"| sed -n '4 s/:.*//p'`
ENDLINE=$((ENDLINE-1)) 
CHANGELOG=`sed -n -e "${BEGINLINE},${ENDLINE}p" "$CURRENTDIR/readme.txt"`
CHANGELOG_JSON="${CHANGELOG//$'\n'/'\n'}"

#NEWVERSION=2.0.9
TAG_DIR="${SVNDIR}/tags/${NEWVERSION}"
TRUNK_DIR="${SVNDIR}/trunk"
#rm -rf "$TRUNK_DIR/*"
#rm -rf $TAG_DIR
#mkdir -p $TRUNK_DIR
cd $SVNDIR
svn update

cd $CURRENTDIR
echo "Copy to new version to $TRUNK_DIR"
rsync --exclude={'.git/','bin/','.docker/','vendor/','log/*','tests/','.vscode/','.circleci/','.idea/','codeception.yml','codeception.dev.yml','docker-compose.yml','default.env.template','supervisord.log','supervisord.pid','phpcs.xml.dist','phpmd.xml.dist','composer.lock','composer.json','core','default.env','.env.circleci','.env.local','.gitignore'} --stats --delete -avz ./ $TRUNK_DIR

cd $SVNDIR
svn rm "tags/${NEWVERSION}"
svn cp trunk "tags/${NEWVERSION}"
svn stat
#svn ci -m "Release version ${NEWVERSION}"
echo "*** FIN ***"