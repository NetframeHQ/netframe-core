FROM node:11-stretch AS installer

RUN mkdir /app
WORKDIR /app

COPY . /app
RUN npx npm@5.6.0 install --only=production

# using multistage he reprevents keeping the npm cache in the final image

FROM node:11-stretch

COPY --from=installer /app /app
WORKDIR /app

CMD ["node", "server.js"]
