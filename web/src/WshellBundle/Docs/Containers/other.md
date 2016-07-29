docker stop $(docker ps -a -q)
docker rm $(docker ps -a -q)

docker rmi $(docker images -q)

смонтированные файловые системы
df -h

разная инфа по использованию ресурсов
avdm -m

размер файла в строках
wc -l file

grep -rnw '/path/to/somewhere/' -e "pattern"


I have a blag.
You mean blog, right?

NO! A blag!


По умолчанию, контейнеры закрыты для доступа извне
https://docs.docker.com/v1.8/articles/networking/#binding-container-ports-to-the-host
поэтому нужно линковать порты с ip адреса контейнера (не localhost!) на хост.

UI для контейнеров
docker run -d -p 9010:9000 --privileged -v /var/run/docker.sock:/var/run/docker.sock uifd/ui-for-docker
