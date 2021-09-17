FROM node

ARG HOST_UID

WORKDIR /var/www/html

RUN usermod -u 200 node
RUN groupmod -g 200 node
RUN adduser --uid $HOST_UID app

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update
RUN apt -y install --no-install-recommends yarn

ENTRYPOINT ["tail", "-f", "/dev/null"]
