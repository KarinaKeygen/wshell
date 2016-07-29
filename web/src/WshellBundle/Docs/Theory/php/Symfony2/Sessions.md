$session = $request->getSession();

// сохранение
$session->set('foo', 'bar');

// получение
$foobar = $session->get('foobar', 'default value');

