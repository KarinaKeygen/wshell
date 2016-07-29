Каждый пользователь имеет login+UID+GID
UID(user identificator) - число в дипозоне [0-65535].
	0 - root
	[0 и выше] - псевдопользователи (системные)
	[500 и выше] - обычные пользователи
	max - nobody
GID(group identificator) - идентификатор группы по умолчанию
/home/(name)/ - домашняя папка

CRUD:
user*
group*
passwd user [pass] - установить пароль
Все новые записи добавляются в конец

Файлы:
/etc/passwd - данные учетных записей
	login : x : UID : GID : (любые данные) : home : shell
/etc/shadow - зашифрованные пароли
	login : $id_algo$salt$hash : change : min : max : warn : inactive : expire
/etc/group - данные групп
	group_name:password:group_id:list

Связанные утилиты:
id - сбор инфы о пользователе по id
getent shadow root -> получить строчку из shadow для root
useradd -D -> посмотреть настройки по умолчанию
mkpasswd -m sha-512 --salt=KlLePXXM qwerty11 - сгенерить хэш как в shadow
