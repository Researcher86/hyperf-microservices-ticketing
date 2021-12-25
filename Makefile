build:
	docker-compose build

up: php-clear
	docker-compose up -d
	#docker-compose exec hyperf php bin/hyperf.php migrate

restart: down up

logs:
	docker-compose logs

down:
	docker-compose down

down-all:
	docker-compose down -v


#client-bash:
#	docker-compose exec client sh

auth-bash:
	docker-compose exec auth bash

test:
	docker-compose exec auth composer test


gen-producer:
	docker-compose exec hyperf php bin/hyperf.php gen:amqp-producer DemoProducer

gen-consumer:
	docker-compose exec hyperf php bin/hyperf.php gen:amqp-consumer DemoConsumer

gen-command:
	docker-compose exec hyperf php bin/hyperf.php gen:command FooCommand

run-command:
	docker-compose exec hyperf php bin/hyperf.php demo:command

user-gen-migration:
	docker-compose exec user-ms php bin/hyperf.php gen:migration create_users_table

user-gen-model:
	docker-compose exec user-ms php bin/hyperf.php gen:model users

migrate:
	docker-compose exec auth php bin/hyperf.php migrate
	docker-compose exec auth php bin/hyperf.php users:fixture

php-clear:
	rm -rf auth/runtime/container

# make bench
bench:
    # https://github-wiki-see.page/m/giltene/wrk2/wiki/Installing-wrk2-on-Linux#:~:text=Installing%20wrk2%20on,wrk%20and%20build.
	wrk -t10 -c1000 -R5000 http://localhost:9501/api/users/currentuser

