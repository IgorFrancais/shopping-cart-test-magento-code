FROM gitpod/workspace-full

# PHP 8.4
RUN sudo add-apt-repository ppa:ondrej/php -y
RUN sudo apt-get update && sudo apt-get install -y \
    php8.4 php8.4-cli php8.4-fpm php8.4-mysql \
    php8.4-xml php8.4-gd php8.4-intl php8.4-mbstring \
    php8.4-curl php8.4-zip php8.4-bcmath php8.4-soap

# MariaDB 11
RUN sudo apt-get install -y mariadb-server
RUN sudo service mariadb start && \
    sudo mysql -e "CREATE DATABASE magento;"

# OpenSearch 2.x
RUN wget https://artifacts.opensearch.org/releases/bundle/opensearch/2.11.0/opensearch-2.11.0-linux-x64.tar.gz && \
    tar -xzf opensearch-2.11.0-linux-x64.tar.gz && \
    sudo mv opensearch-2.11.0 /usr/local/opensearch

RUN sudo bash -c 'cat <<EOF >/etc/systemd/system/opensearch.service
[Unit]
Description=OpenSearch
[Service]
ExecStart=/usr/local/opensearch/bin/opensearch
User=gitpod
[Install]
WantedBy=multi-user.target
EOF'

RUN sudo systemctl enable opensearch
