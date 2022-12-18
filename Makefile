.SILENT:

include .env

dc = docker compose -p ${APP_NAME}

bridge = ${DOCKER_BRIDGE}
port = ${DOCKER_NGINX_OUT_PORT}

http_address = "http://$(bridge):$(port)"

build:
	$(dc) --env-file ./app/.env up --build --force-recreate -d

start:
	$(dc) start
	echo $(http_address)

stop:
	$(dc) stop

down:
	$(dc) down

logs:
	$(dc) logs

logs_f:
	$(dc) logs -f

ps:
	$(dc) ps

restart:
	$(dc) restart
