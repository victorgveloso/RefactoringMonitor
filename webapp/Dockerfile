FROM node:6.9.5-wheezy

RUN echo "deb http://archive.debian.org/debian wheezy main" | tee /etc/apt/sources.list &&  \
    echo "deb http://archive.debian.org/debian-security wheezy/updates main" >> /etc/apt/sources.list && \
    apt-get -o Acquire::Check-Valid-Until=false update \
    && apt-get install --force-yes -y gettext-base \
    && rm -rf /var/lib/apt/lists/* \
    && npm install webpack@^4 -g \
    && npm install tslint -g \
    && mkdir -p /home/angular/project  \
    && chown -R 1000:1000 /home/angular/

USER 1000:1000

WORKDIR /home/angular/project

COPY --chown=1000:1000 tslint.json tsconfig.json protractor.conf.js package.json karma.conf.js angular-cli.json /home/angular/project/

RUN npm install --production

COPY --chown=1000:1000 e2e /home/angular/project/e2e/

COPY --chown=1000:1000 src /home/angular/project/src/

ENTRYPOINT ["npm"]
CMD ["start"]

EXPOSE 443
