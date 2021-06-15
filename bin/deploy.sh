#!/bin/bash
# export SSH_REMOTE_SERVER_USER=mage
# export SSH_REMOTE_SERVER_IP=112.109.73.217
# export SSH_REMOTE_SERVER_HOST=112.109.73.217
# export SSH_REMOTE_SERVER_ROOT=/var/www/staging/wordpress-demo
# export CIRCLE_PROJECT_REPONAME=woocommerce-latitudepay-genoapay

INVALID_PARAMETERS="\033[1;31mError:\033[0m Please make sure you've indicated correct parameters."

# Path to plugin directory on the remote server
PLUGIN_DIR="${SSH_REMOTE_SERVER_ROOT}/wp-content/plugins/${CIRCLE_PROJECT_REPONAME}"

# Set Remote server SSH credentials (If this is a public Repo, you will want to set these as CircleCI environment variables)
SSH_CREDS="${SSH_REMOTE_SERVER_USER}@${SSH_REMOTE_SERVER_HOST}"

# Add the server IP to the known_hosts file
# Required for CircleCI to allow SSH connections to remote server
# example: ssh-keyscan 123.456.789.123 >> ~/.ssh/known_hosts
ssh-keyscan ${SSH_REMOTE_SERVER_IP} >> ~/.ssh/known_hosts
cd ~/latitude

if [ $# -eq 0 ]
	then
		echo -e ${INVALID_PARAMETERS}
elif [ $1 == "--dry-run" ]
	then
		echo "Running dry-run deployment."
		ssh -p22 ${SSH_CREDS} mkdir -p ${PLUGIN_DIR}
		rsync --exclude={'.git/','bin/','.docker/','vendor/','log/','tests/*','.vscode/','.circleci/','.idea/'} --stats --dry-run --delete --progress -avz -e "ssh -p22" ./ ${SSH_CREDS}:${PLUGIN_DIR}
elif [ $1 == "live" ]
	then
		echo "Running actual deploy"
		ssh -p22 ${SSH_CREDS} mkdir -p ${PLUGIN_DIR}
		rsync --exclude={'.git/','bin/','.docker/','vendor/','log/','tests/','.vscode/','.circleci/','.idea/'} --stats --delete -avz -e "ssh -p22" ./ ${SSH_CREDS}:${PLUGIN_DIR}
else
	echo -e ${INVALID_PARAMETERS};
fi