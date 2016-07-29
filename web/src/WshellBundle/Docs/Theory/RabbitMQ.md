### Что есть

Библиотека, реализующая сервер обмена данными по протоколу AMQP, написана на Erlang.
AMQP клиенты есть для большинства известных языков программирования.

Сам сервер состоит из точки обмена (exchange) и очередей сообщений.
Любой клиент может быть или Producer или Consumer.


### AMQPChannel::queue_declare()

$queue="",          // имя канала(очереди)

$passive=false,     //
$durable=false,     //
$exclusive=false,   //
$auto_delete=true,  //
$nowait=false,      //

$arguments=null,    //
$ticket=null        //


### AMQPChannel::exchange_declare()


$exchange,
$type,              // direct, topic, headers, fanout
$passive=false,
$durable=false,
$auto_delete=true,
$internal=false,
$nowait=false,
$arguments=null,
$ticket=null



### AMQPChannel::basic_publish()

$msg                // передаваемые данные
$exchange="",
$routing_key="",
$mandatory=false,
$immediate=false,
$ticket=null



### AMQPChannel::basic_consume()

$queue="",
$consumer_tag="",
$no_local=false,
$no_ack=false,
$exclusive=false,
$nowait=false,
$callback=null,
$ticket=null,
$arguments = []



### Message

Данные передаются вместе со служебными полями.