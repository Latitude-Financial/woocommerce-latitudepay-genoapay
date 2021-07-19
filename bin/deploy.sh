#!/bin/bash
export SSHPASS=$SSH_REMOTE_SERVER_PASSWORD
export DEPLOY_BRANCH=$SSH_REMOTE_SERVER_GIT_BRANCH
INVALID_PARAMETERS="\033[1;31mError:\033[0m Please make sure you've indicated correct parameters."

# Path to plugin directory on the remote server
PLUGIN_DIR="${SSH_REMOTE_SERVER_ROOT}/wp-content/plugins/woocommerce-payment-gateway-latitudefinance"

# Set Remote server SSH credentials (If this is a public Repo, you will want to set these as CircleCI environment variables)
SSH_CREDS="${SSH_REMOTE_SERVER_USER}@${SSH_REMOTE_SERVER_HOST}"
SSH_EXTRA="-o PubkeyAuthentication=Yes -o StrictHostKeyChecking=no"

# Add the server IP to the known_hosts file
# Required for CircleCI to allow SSH connections to remote server
# example: ssh-keyscan 123.456.789.123 >> ~/.ssh/known_hosts
ssh-keyscan ${SSH_REMOTE_SERVER_IP} >> ~/.ssh/known_hosts
cd /var/www/html/wp-content/plugins/woocommerce-latitudepay-genoapay

if [ $# -eq 0 ]
	then
		echo -e ${INVALID_PARAMETERS}
elif [ $1 == "--dry-run" ]
	then
		echo "Running dry-run deployment."
		BUILD_COMMAND="cd ${PLUGIN_DIR} && git checkout ${DEPLOY_BRANCH} && git reset --hard origin/${DEPLOY_BRANCH} && git pull origin ${DEPLOY_BRANCH}"
		sshpass -e ssh ${SSH_EXTRA} ${SSH_CREDS} -p${SSH_REMOTE_SERVER_PORT} $BUILD_COMMAND
elif [ $1 == "live" ]
	then
		echo "Running actual deploy"
		BUILD_COMMAND="cd ${PLUGIN_DIR} && git checkout ${DEPLOY_BRANCH} && git reset --hard origin/${DEPLOY_BRANCH} && git pull origin ${DEPLOY_BRANCH}"
		sshpass -e ssh ${SSH_EXTRA} ${SSH_CREDS} -p${SSH_REMOTE_SERVER_PORT} $BUILD_COMMAND
else
	echo -e ${INVALID_PARAMETERS};
fi