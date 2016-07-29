
// по умолчанию, статус редиректа 302
return $this->redirect($this->generateUrl('homepage'), 301);
// или
return new RedirectResponse($this->generateUrl('homepage'));

// Внутренний редирект (без дополнительного запроса на клиенте)
// Тут вместо роута сразу указывается экшн.
$response = $this->forward('AcmeHelloBundle:Hello:fancy', [
	'name'  => $name,
	'color' => 'green',
]);

// ... можно модифицировать ответ или сразу вернуть
return $response;
