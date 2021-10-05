FROM node:16-alpine

ARG HOST_UID

WORKDIR /var/www/html

RUN apk --no-cache add shadow
RUN usermod -u $HOST_UID node

ENTRYPOINT ["tail", "-f", "/dev/null"]
