FROM php:8.4-cli

ARG UID
ARG GID

ENV WORKDIR=/app
WORKDIR ${WORKDIR}

# Создание пользователя с проверкой существования UID/GID
RUN if getent group $GID >/dev/null; then \
      group_name=$(getent group $GID | cut -d: -f1); \
    else \
      groupadd -g $GID appuser; \
      group_name=appuser; \
    fi && \
    if id -u $UID >/dev/null 2>&1; then \
      echo "User with UID $UID already exists."; \
    else \
      useradd -u $UID -g "$group_name" -m -s /bin/bash appuser; \
    fi

RUN chown -R appuser:appuser ${WORKDIR} \
    && chmod -R u+rwX ${WORKDIR}

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
 && docker-php-ext-install intl ctype \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

USER appuser

CMD ["php", "-a"]