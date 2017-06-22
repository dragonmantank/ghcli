FROM php:cli

RUN apt-get update && \
    apt-get install -y git && \
    echo "phar.readonly = Off" > /usr/local/etc/php/conf.d/phar.ini && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app
ENTRYPOINT ["php", "vendor/bin/box", "build", "-c", "box.json"]