FROM node:10-stretch

RUN mkdir /app
WORKDIR /app

RUN npx npm@5 install -g laravel-echo-server
COPY docker/laravel-echo-server.dev.json /app/laravel-echo-server.json

ENTRYPOINT ["laravel-echo-server"]
CMD ["start"]
