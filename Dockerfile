ARG IMAGE_VERSION=2.0.0-npm
FROM yuridefranca/php:${IMAGE_VERSION}

COPY ./.docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]