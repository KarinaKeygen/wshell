## Node
Исходники wshell ноды


## Web
web интерфейс


## dev
тут лежат настройки для развертывания рабочего окружения.
Те контейнеры, которые используются как сервисы перечислены в docker-compose.yml
Другие, которые требуется запускать лишь время от времени, представлены в виде
коротких алиасов.

Для большинства сайтов достаточно положить их в директорию sites (они будут
доступны по домену SITE_NAME.dev), корнем сайта будет директория SITE_NAME/web

### nodejs
docker run -it --rm -v $(pwd):/usr/src/app dc_node node
docker run -it --rm -v $(pwd):/usr/src/app dc_node bower install --allow-root -q -s
docker run -it --rm -v $(pwd):/usr/src/app -e UID=$(id -u) dc_node chown -R $UID .

# add this to .bashrc to alias npm
alias npm='docker run --rm -v $(pwd)/:/mnt/ -e UID=$(id -u) -e GID=$(id -g) zenoss/gulp npm'
# and use it like a regular npm command. eg: npm install

# add this to .bashrc to alias gulp
alias gulp='docker run --rm -v $(pwd)/:/mnt/ -e UID=$(id -u) -e GID=$(id -g) zenoss/gulp gulp'
# and use it like a regular gulp command. eg: gulp release

### docker-compose
docker-compose ps - список запущенных контейнеров
docker-compose port
docker-compose logs

docker-compose build - сборка
docker-compose run - параметры для запуска чего-нибудь внутри
docker-compose up - запуск интерактивно
docker-compose start - то же самое, но сразу в detached mode
docker-compose restart
docker-compose stop
docker-compose kill - форсированная остановка
docker-compose scale - маштабировать сервисы

### mongo backup
in mongo container:

cd /data/db
mongodump --db wshell

## docs
Различная сопроводительная документация
