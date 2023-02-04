FROM node:10-stretch

RUN mkdir /app
WORKDIR /app

RUN npx npm@5 install -g laravel-echo-server
COPY docker/laravel-echo-server.production.json /app/laravel-echo-server.json

ENTRYPOINT ["laravel-echo-server"]
CMD ["start"]

# optional env variables
ENV LARAVEL_ECHO_SERVER_AUTH_HOST="http://broadcast.netframe.co"
ENV LARAVEL_ECHO_SERVER_POST="6001"
ENV LARAVEL_ECHO_SERVER_REDIS_PORT="6379"
# required env variables
ENV LARAVEL_ECHO_SERVER_REDIS_HOST=
ENV LARAVEL_ECHO_SERVER_REDIS_PASSWORD=
