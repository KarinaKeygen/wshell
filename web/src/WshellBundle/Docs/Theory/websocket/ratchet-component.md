### Chat

Простой пример компонента MessageComponentInterface.

	<?php

	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;

	class Chat implements MessageComponentInterface {
	    public function onOpen(ConnectionInterface $conn) {
	    }

	    public function onMessage(ConnectionInterface $from, $msg) {
	    }

	    public function onClose(ConnectionInterface $conn) {
	    }

	    public function onError(ConnectionInterface $conn, \Exception $e) {
	    }
	}

Все четыре метода описывают то, что будет происходить на сервере при получении
определенных эвентов от клиента. Первый параметр - это всегда соединение с этим клиентом.


Теперь нужно создать инстанс чата. Для этого мы используем IoServer - web-socket сервер,
запускаемый отдельным скриптом.

	<?php
	use Ratchet\Server\IoServer;
	use MyApp\Chat;

	    require dirname(__DIR__) . '/vendor/autoload.php';

	    $server = IoServer::factory(
		new Chat(),
		8080
	    );

	    $server->run();

Теперь нужно дописать реализацию чата и можно конннектится:
telnet localhost 8080
