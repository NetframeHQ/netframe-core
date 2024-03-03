setup: # Setup the development environment
	$(info Setting up services)
	@docker-compose pull
	@docker-compose build --no-cache
	@docker-compose up -d search database
	@make "install:storage"
	@make "install:php"
	@make "generate:env"
	@make "install:node"
	@make "generate:emojis"
	@make "generate:assets"
	#@make "generate:search"
	@docker-compose stop

update: # Update the environment after changing .env, docker-compose.yml or a Dockerfile
	$(info Updating services)
	@sed -i.bak '/^APP_KEY=/!d' .env && cat docker/laravel-base-env >> .env
	@docker-compose pull
	@docker-compose build --no-cache

start: # Start environment
	$(info Starting all services)
	@test "$(shell docker-compose images -q | wc -l)" != "0" || make setup
	@docker-compose up

start\:background: # Start environment in background
	$(info Starting all services)
	@test "$(shell docker-compose images -q | wc -l)" != "0" || make setup
	@docker-compose up -d

stop: # Stop environment
	$(info Stopping all services)
	@docker-compose stop

restart: # Restart environment
	$(info Restarting all services)
	@docker-compose restart

status: # Display infos about the environment
	@test "$(shell docker-compose images -q | wc -l)" != "0" && echo "System is installed"
	@docker-compose ps


clean: # Remove objects generated by the environment
	$(info Remove all containers, volumes and networks)
	@make "clean:containers"
	@make "clean:files"

clean\:containers: # Clean containers, volumes and images generated by the environment
	@docker-compose down
	@docker-compose down -v

clean\:files: # Remove generated directories
	@rm -rf vendor/ bin/ node_modules/ node/node_modules/
	@rm .env


install\:php: # Install PHP/Composer dependencies
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh init "vendor/"
	@rm -rf "bin/"
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh init "bin/"
	@docker-compose run --rm --no-deps --workdir "/app" netframe /bin/composer install
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh lock "vendor/"
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh lock "bin/"

install\:node: # Install NodeJS/npm dependencies
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh init "node_modules/"
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh init "node/node_modules/"
	@docker run --rm -it --ulimit nofile=65535 -v "${CURDIR}:/app" --workdir "/app" node:10-stretch \
  	npx npm@5.6.0 install
	@docker run --rm -it --ulimit nofile=65535 -v "${CURDIR}:/app" --workdir "/app/node" node:11-stretch \
  	npx npm@5.6.0 install
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh lock "node_modules/"
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh lock "node/node_modules/"

install\:storage: # Prepare storage for Laravel
	@docker-compose run --rm --no-deps netframe mkdir -p /data/storage
	@docker-compose run --rm --no-deps netframe chown -R www-data:www-data /data
	@docker run --rm -it -v "${CURDIR}:/app:z" -e "USER=$(shell id -u)" -e "GROUP=$(shell id -g)" --workdir "/app" debian:stretch \
		/app/docker/init-directory.sh lock "storage/" "757"
	@docker-compose run --rm --no-deps --workdir "/app" -u www-data netframe mkdir -p \
		"/data/storage/uploads/documents/pdf" "/data/storage/uploads/documents/preview"


services\:list: # List existing services
	@grep '^    \w\+:$' docker-compose.yml | sed -e 's/:$//g' | tr -d ' '

services\:status: # Show status of services
	@docker-compose ps


enter\:php: # Enter in a PHP container for debug purpose
	$(info Entering PHP container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env --workdir /app netframe bash

enter\:assets: # Enter in a front-end container for debug purpose
	$(info Entering NodeJS container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env assets bash

enter\:node: # Enter in a NodeJS container for debug purpose
	$(info Entering NodeJS container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env collab bash

enter\:echo: # Enter in a NodeJS container for debug purpose
	$(info Entering NodeJS Laravel-echo container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env broadcast bash

enter\:mysql: # Enter in a MariaDB container for debug purpose
	$(info Entering MariaDB container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env database bash

enter\:redis: # Enter in a Redis container for debug purpose
	$(info Entering Redis container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env cache bash

enter\:elastic: # Enter in an ElasticSearch container for debug purpose
	$(info Entering ElasticSearch container)
	@docker-compose run --rm --no-deps --entrypoint /usr/bin/env search bash


generate\:env: # Generate environment file
	@test -f .env || cp ./docker/laravel-base-env ./.env
	@grep -q '^APP_KEY=' ./.env || docker-compose run -T --rm --no-deps --workdir "/app" netframe \
		php artisan key:generate --show --no-ansi \
		| sed -e 's/^/APP_KEY=/' >> .env

generate\:search: # Generate search indexes
	@docker-compose run --rm --workdir "/app" netframe \
		sh /app/docker/wait-for.sh search:9200 -t 60 -- \
		sh /app/docker/wait-for.sh database:3306 -t 60 -- \
		sh -c 'sleep 5 && php artisan search:reindex'

generate\:assets: # Generate assets with Webpack
	@docker run --rm -it --ulimit nofile=65535 -v "${CURDIR}:/app" --workdir "/app" node:10-stretch \
		npx npm@5.6.0 run dev

generate\:thumbnail: # Generate documents thumbnails
	@docker-compose run --rm -u www-data --workdir "/app" netframe \
		/app/docker/wait-for.sh database:3306 -t 60 -- \
		sh -c 'sleep 1 && php artisan documents:thumbnail:generate'

generate\:emojis: # Generate emojis
	@docker-compose run --rm -u www-data --workdir "/app" netframe \
		/app/docker/wait-for.sh database:3306 -t 60 -- \
		sh -c 'sleep 1 && php artisan generate:emojis'


lint\:php: # Lint the PHP code
	@docker-compose run --rm --workdir "/app" netframe ./bin/phplint ./
	@docker-compose run --rm --workdir "/app" netframe ./bin/phpcs

lint\:node: # Lint the NodeJS code
	@docker-compose run --rm collab ./node_modules/.bin/eslint .

lint: # Lint all the code
	@make "lint:php"
	@make "lint:node"

format\:php: # Format the PHP code
	@docker-compose run --rm --workdir "/app" netframe ./bin/phpcbf

format: # Format all the code
	@make "format:php"

test\:unit: # Launch unit tests
	@docker-compose run --rm --no-deps --workdir "/app" netframe \
		php /app/bin/phpunit --testsuite "Unit Tests"

test\:feature: # Launch feature tests
	@docker-compose run --rm --no-deps --workdir "/app" netframe \
		php /app/bin/phpunit --testsuite "Feature Tests"

test: # Launch unit and feature tests
	@make "test:unit"
	@make "test:feature"

test\:integration: # Launch integration/end-to-end tests
	@docker-compose run --rm cypress run --config-file false

test\:all: # Launch unit, feature and integration/end-to-end tests
	@make "test"
	@make "test:integration"


help: # Print help on Makefile
	@grep '^[^.#]\+:\s\+.*#' Makefile \
		| sed "s/\\\\//g" \
		| sed "s/\(.\+\):\s*\(.*\) #\s*\(.*\)/`printf "\033[93m"`\1`printf "\033[0m"`	\3 [\2]/" \
		| expand -t32
