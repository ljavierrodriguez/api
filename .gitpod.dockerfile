FROM gitpod/workspace-mysql:branch-mysql

# 1. give write permission to the gitpod-user to apache directories
# 2. let Apache use apache.conf and apache.env.sh from our /workspace/<myproject> folder
RUN chown -R gitpod:gitpod /var/run/apache2 /var/lock/apache2 /var/log/apache2 \
 && echo "include \${GITPOD_REPO_ROOT}/apache.conf" > /etc/apache2/apache2.conf \
 && echo ". \${GITPOD_REPO_ROOT}/apache.env.sh" > /etc/apache2/envvars

ENV x=2