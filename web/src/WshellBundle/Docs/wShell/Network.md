### Схема сети

    |WAMP Client A|                     =>  |WAMP Client X|
    |WAMP Client B|  =>  |WAMP Router|  =>  |WAMP Client Y|
    |WAMP Client C|                     =>  |WAMP Client Z|

### Схема передачи сообщений

Тут все участники - клиенты из предыдущей схемы

    |Sender|                            |Slave|
    |Sender|    =>    |Master|    =>    |Slave|
    |Sender|                            |Slave|

Senders и Slaves может быть сколько угодно.
Master всегда один, в случае выхода из строя, выбирается новый
из числа Slaves.

### Топики (для группировки клиентов)

* wshell.system.all - подписаны все участники
* wshell.system.master - Senders + Master
* wshell.system.claster - Master + Slaves

### Клиентское ПО

Sender может быть любым ПО, способным работать с web-socket - браузер, decktop/mobile приложение,
микроконтроллер или серверная утилита.
Executor (Master или Slave) - сервер со специальным клиентом, написанном на Golang (для управления
Docker контейнерами). Все Executors вместе составляют вычислительный кластер.
