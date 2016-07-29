### Введение

Autobahn - js клиент для wсокетов, обертка над встроенным в браузеры клиентом.
Позволяет из коробки использовать RPC и Publish/Subscriber паттерны.

Дополнительно можно использовать следующие либы:
when.js - бесконечные последовательности, паралельности, некоторые node-style фичи.
crypto-js - для шифрования.


### API

    autobahn.version()
    AUTOBAHN_DEBUG = true;

Новое соединение. Если соединение непреднамеренно разорвется,
оно будет автоматичски перезапущено.

    var connection = new autobahn.Connection({
       url: 'ws://127.0.0.1:9000/',
       realm: 'realm1'

       // reconnect options
       max_retries: 15,
       initial_retry_delay: 1.5,
       max_retry_delay: 300,
       retry_delay_growth: 1.5,
       retry_delay_jitter: 0.1,
    });

    connection.open()
    connection.close(reason, message)

Callbacks:

    connection.onopen function(session) {}
    connection.onclose function(reason, details) {}

reason = closed(специально) && lost(случайно) && unreachable(фатальная ошибка)

Если onclose верет false, рекконнект при lost не произойдет.

Cвойства соединения:

    connection.session - session instance
    connection.isOpen
    connection.isRetrying
    connection.defer - (перев.:отложенный) - фабрика коннектов
    connection.defer()

Свойства сессии (доступна внутри opopen, в основном read-only):

    session.id
    session.realm
    session.isOpen
    session.features - объект с данными о ролях и их вохможностях(для WAMP)
    session.subscriptions - активные подписки
    session.registrations - активные процедуры
    session.defer
    session.defer()
    session.log('log me!')

Log также может использоваться как event handler:

    session.call('com.timeservice.now').then(
             session.log(now);
       );

Подписка:

    function on_event1(args, kwargs, details) {
       // event received, do something ..
    }

    session.subscribe('com.myapp.topic1', on_event1).then(
       function (subscription) {
          // subscription succeeded, subscription is an instance of autobahn.Subscription
       },
       function (error) {
          // subscription failed, error is an instance of autobahn.Error
       }
    );

Отписка:

    session.unsubscribe(sub1).then(
       function (gone) {
          // successfully unsubscribed sub1
       },
       function (error) {
          // unsubscribe failed
       }
    );


Публикация:

    session.publish('com.myapp.hello', ['Hello, world!']);

Подробней см. в PubSub

Регистрация смоей процедуры:

    function myproc1(args, kwargs, details) {
       // invocation .. do something and return a plain value or a promise ..
    }

    session.register('com.myapp.proc1', myproc1).then(
       function (registration) {
          // registration succeeded, registration is an instance of autobahn.Registration
       },
       function (error) {
          // registration failed, error is an isntance of autobahn.Error
       }
    );

Удаление процедуры:

    var reg1;

    session.register('com.myapp.proc1', myproc1).then(
       function (registration) {
          reg1 = registration;
       }
    );

    ...

    session.unregister(reg1).then(
       function () {
          // successfully unregistered reg1
       },
       function (error) {
          // unregister failed
       }
    );

Вызов процедры:

    session.call('com.arguments.add2', [2, 3]).then(
       function (result) {
          // call was successful
       },
       function (error) {
          // call failed
       }
    );

подробнее см. в RPC

### PubSub

### RPC

### WAMP