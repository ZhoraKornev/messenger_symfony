# Description

MAIN repo where this update peoject come - https://raw.githubusercontent.com/MadCzarls/symfonycasts-messenger/

https://symfonycasts.com/screencast/messenger

Running on Docker (utilizing docker-compose) with PHP 8.0 + nginx 1.18 + PostgreSQL. By default, includes xdebug extension and PHP_CodeSniffer for easy development and basic configuration for opcache for production. Includes instruction for setting it in PhpStorm.

- https://symfony.com/
- https://www.docker.com/
- https://docs.docker.com/compose/
- https://www.php.net/
- https://www.nginx.com/
- https://www.postgresql.org/
- https://xdebug.org/
- https://github.com/squizlabs/PHP_CodeSniffer
- https://www.php.net/manual/en/intro.opcache.php
- https://www.jetbrains.com/phpstorm/

Clone and tweak it to your needs. Tested on Linux (Ubuntu 20.04):

1. Docker version 20.10.11, build 847da18
1. docker-compose version 1.29.2, build 5becea4c

and Windows 10:

1. Use `Docker for Windows`, at least version `3.2.1`.
1. Switch to `Linux containers`.
1. Go to `Settings` -> `Docker Engine` and set `experimental mode` to `true`.

# Usage

1. Clone repository, `cd` inside.
1. Create `.env` file in `docker/php` directory according to your environment, one of - `dev`, `test`, `prod` - just copy correct template `.env.dist`, but remember to define your own `APP_SECRET`!
1. Review `docker-compose.yml` and change according to the comments inside.
1. You can change PHP memory limit in `docker/php/config/docker-php-memlimit.init` file if you want.

Afterwards run:
<pre>
docker-compose build
docker-compose up
</pre>

After that log into container with `docker exec -it messenger.php bash`, where `messenger.php` is the default container name from `docker-compose.yml`. Then run:

<pre>
composer install
npm install
npm run dev
php bin/console doctrine:migrations:migrate
</pre>

From this point forward, application should be available under `http://localhost:8050/`, where port `8050` is default defined in `docker-compose.yml`.

### A note concerning Supervisor and Chapter 24

To mimic production environment and to follow `Chapter 24` repository contains Supervisor - tool to control processes on your system.
It is used to run - constantly - Symfony's Messenger's consumer. Configuration is stored in `docker/supervisor/*.conf`.

But since we are using dockerized environment there are a few issues with that:
* we can have (and probably should) have separate container for Supervisor - but since Messenger's `messenger:consume` command is integral part of Symfony we would need to include another copy of whole application in that container
* we can have Supervisor installed in the same container as PHP but, because of the way it starts (`CMD` command) it blocks PHP-FPM process from starting (only one `CMD` may be used in `Dockerfile`) (and this way you are treating Docker more like virtual machine for everything - and you should not)

For sandbox-learning purposes I have decided to go with the second approach and resolve the issue of not-starting PHP-FPM by starting it
automatically on `docker-compose up` by using... Supervisor ;) - check `docker/supervisor/php-fpm.conf` file -
but in the end, since it's a sandbox, it's disabled - you can uncomment lines in `docker/php/Dockerfile`:
`CMD ["/usr/bin/supervisord"]` and `COPY supervisor/* /etc/supervisor/conf.d/` if you want to try it out.
## Running tests

Environment variable `APP_ENV` must be set to `test` to be able to run Kernel-/Web-TestCases based tests because
`Real environment variables win over .env files` and this is the case in docker-based environments.

# Overview

All PHP extensions can be installed via `docker-php-ext-install` command in `docker/php/Dockerfile`. Examples and usage:
`https://gist.github.com/giansalex/2776a4206666d940d014792ab4700d80`.

## PhpStorm configuration
_Based on PhpStorm version: 2021.1.4_

Open directory including cloned repository as directory in PhpStorm.

### Interpreter

1. `Settings` -> `PHP` -> `Servers`: create server with name `docker` (the same as in ENV variable `PHP_IDE_CONFIG`), host `localhost`, port `8050` (default from `docker-compose.yml`).
1. Tick `Use path mappings` -> set `File/Directory` <-> `Absolute path on the server` as: `</absolute/path>/app` <-> `/var/www/app` (default from `docker-compose.yml`).
1. `Settings` -> `PHP`: three dots next to the field `CLI interpreter` -> `+` button -> `From Docker, Vagrant(...)` -> from `docker-compose`, from service `php`, server `Docker`, configuration files `./docker-compose`. After creating in `Lifecycle` section ensure to pick `Always start a new container (...)`, in `General` refresh interpreter data.

### xdebug

1. `Settings` -> `PHP` -> `Debug`  -> `Xdebug` -> `Debug port`: `9003` (set by default) and check `Can accept external connections`.
1. Click `Start Listening for PHP Debug connections` -> `+` button, set breakpoints and refresh website.

### PHPCS

1. Copy `app/phpcs.xml.dist` and name it `phpcs.xml`. Tweak it to your needs.
1. `Settings` -> `PHP` -> `Quality Tools` -> `PHP_CodeSniffer` -> `Configuration`: three dots, add interpreter with `+` and validate paths. By default, there should be correct path mappings and paths already set to `/var/www/app/vendor/bin/phpcs` and `/var/www/app/vendor/bin/phpcbf`.
1. `Settings` -> `Editor` -> `Inspections` -> `PHP` -> `Quality tools` -> tick `PHP_CodeSniffer validation` -> tick `Show sniff name` -> set coding standard to `Custom` -> three dots and type `/var/www/app/phpcs.xml` (path in container).

### PostgreSQL

Open `Database` section on the right bar of IDE -> `Data Source` -> `PostgreSQL` -> set host to `localhost`, set user to `app_user`, pass `app_pass`, database to `app` (defaults from `docker-compose.yml`) Set url to `jdbc:postgresql://localhost:5432/app`.

### PHPUnit

1. Copy `phpunit.xml.dist` into `phpunit.xml`.
1. Login into `messenger.php` container where `messenger.php` is the default container name from `docker-compose.yml`, and run `./bin/phpunit`.
1. `Settings` -> `PHP` -> `Test frameworks`. Click `+` and `PHPUnit by Remote Intepreter` -> pick interpreter. In `PHPUnit library` tick `Path to phpunit.phar` and type `bin/phpunit`. Click refresh icon. In `Test runner` section set `Default configuration file` to `phpunit.xml` and `Default bootstrap file` to `tests/bootstrap.php`.

# Disclaimer

Although there are present different files for `prod` and `dev` environments these are only stubs and this repo is not suitable to run on `prod` environment. The idea was to create as much integral, self-contained and flexible environment for `development` as possible and these files are here merely to easily mimic `prod` env and point out differences in configuration.




Sandbox for getting to know and learn Symfony Messenger component, based on https://symfonycasts.com/screencast/messenger/.

**Status: FINISHED**
- [x] Chapter 1
- [x] Chapter 2
- [x] Chapter 3
- [x] Chapter 4
- [x] Chapter 5
- [x] Chapter 6
- [x] Chapter 7
- [x] Chapter 8
- [x] Chapter 9
- [x] Chapter 10
- [x] Chapter 11
- [x] Chapter 12
- [x] Chapter 13
- [x] Chapter 14
- [x] Chapter 15
- [x] Chapter 16
- [x] Chapter 17
- [x] Chapter 18
- [x] Chapter 19
- [x] Chapter 20
- [x] Chapter 21
- [x] Chapter 22
- [x] Chapter 23
- [x] Chapter 24
- [x] Chapter 25
- [x] Chapter 26
- [x] Chapter 27
- [x] Chapter 28
- [x] Chapter 29
- [x] Chapter 30
- [x] Chapter 31
- [x] Chapter 32
- [x] Chapter 33
- [x] Chapter 34
- [x] Chapter 35
- [x] Chapter 36
- [x] Chapter 37
- [x] Chapter 38
- [x] Chapter 39
- [x] Chapter 40
- [x] Chapter 41
- [x] Chapter 42
- [x] Chapter 43
- [x] Chapter 44
- [x] Chapter 45
- [x] Chapter 46
- [x] Chapter 47
- [x] Chapter 48