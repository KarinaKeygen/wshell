bash
sudo docker exec -it <name> bash
and
export TERM=xterm
for using nano and other terminal utilities

https://docs.docker.com/compose/compose-file/

ЛИНКОВКА
Допустим у вас есть 2 контейнера: web и db. Чтобы создать связь, удалите контейнер web и пересоздайте с использованием команды --link name:alias.
docker run -d -P --name web --link db:db trukhinyuri/webapp python app.py

# options

--config=~/.docker   указываем конфигурацию
--debug=true         дебаг
--log-level=info     логлевел

--tls=false                        Use TLS and verify the remote
--tlscacert=~/.docker/ca.pem       Trust certs signed only by this CA
--tlscert=~/.docker/cert.pem       Path to TLS certificate file
--tlskey=~/.docker/key.pem         Path to TLS key file

# global

login     Register or log in to a Docker registry
logout    Log out from a Docker registry
search    Search the Docker Hub for images
export    Export a container's filesystem as a tar archive
import    Import the contents from a tarball to create a filesystem image

network   Manage Docker networks
volume    Manage Docker volumes

# info

LIVE! stats     Display a live stream of container(s) resource usage statistics
LIVE! events    Get real time events from the server (for debug)


+info      Display system-wide information
+diff      Inspect changes on a container's filesystem
+version   Show the Docker version information

+images    List images
+ps        List containers
+-(only containers)inspect   Return low-level information on a container or image
+top       Display the running processes of a container

не надо. images ПОКА рулятся вручную
history   Show the history of an image

# images

ПОКА ВРУЧНУЮ
images генерим из Dockerfile, затем в конфиге дописываем в список

build     Build an image from a Dockerfile
commit    Create a new image from a container's changes
port      List port mappings or a specific mapping for the CONTAINER
cp        Copy files/folders between a container and the local filesystem
load      Load an image from a tar archive or STDIN
save      Save an image(s) to a tar archive
pull      Pull an image or a repository from a registry
push      Push an image or a repository to a registry
rmi       Remove one or more images
tag       Tag an image into a repository
logs      Fetch the logs of a container

# containers

-attach    Attach to a running container
-rename    Rename a container
-wait      Block until a container stops, then print its exit code

+create    Create a new container
+run       Run a command in a new container

+restart   Restart a container

// docker exec 960b20bba19b202ad2640b745fc87566439dbe719222687f49ecdd8658815356 'ls'
+exec      Run a command in a running container

+pause     Pause all processes within a container
+unpause   Unpause all processes within a container

+stop      Stop a running container
+start     Start one or more stopped containers

+kill      Kill a running container

+rm        Remove one or more containers



Архитектура Docker
https://getdev.net/Event/docker

Docker использует архитектуру клиент-сервер. Docker клиент общается с демоном Docker, который берет на себя тяжесть создания, запуска, распределения ваших контейнеров. Оба, клиент и сервер могут работать на одной системе, вы можете подключить клиент к удаленному демону docker. Клиент и сервер общаются через сокет или через RESTful API
https://docs.docker.com/article-img/architecture.svg

Для корректного запуска без sudo:
http://askubuntu.com/questions/477551/how-can-i-use-docker-without-sudo
