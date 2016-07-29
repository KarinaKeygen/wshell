Контекст создается функцией
resoutce stream_context_create( [опции] );


string basename(путь, [удаляемое расширение]);
bool   copy(откуда, куда, [контекст])
bool   unlink(путь, [контекст])
string dirname(путь)




### Обертки:

string file_get_contents ( string $filename [, bool $use_include_path = false [, resource $context [, int $offset = -1 [, int $maxlen ]]]] )
int file_put_contents ( string $filename , mixed $data [, int $flags = 0 [, resource $context ]] )
array file ( string $filename [, int $flags = 0 [, resource $context ]] )

### Информация о файле

ts  fileatime(путь) - последний лоступ
ts  filectime(путь) - последнее изменение индекса
ts  filemtime (путь) - последнее изменение
filesize — Возвращает размер файла
filetype — Возвращает тип файла



### Потоковая работа с файлом:
