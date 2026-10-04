
FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY index.php /app/index.php

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app"]
