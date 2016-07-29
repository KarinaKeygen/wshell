### index.php

	require_once __DIR__.'/../vendor/autoload.php';

	$app = new Silex\Application();
	$app->run();
	$app['debug'] = true;


### Application.php

В первую очередь, по неймспейсам мы можем определить, какие компоненты входят в микрофреймворк:

из Symfony:

HttpKernel
	HttpKernel
	HttpKernelInterface
	TerminableInterface
	Event
		FilterResponseEvent
		GetResponseEvent
		PostResponseEvent
	EventListener
		ResponseListener
		RouterListener
	Exception
		HttpException
	KernelEvents
HttpFoundation
	BinaryFileResponse
	Request
	RequestStack
	Response
	RedirectResponse
	StreamedResponse
	JsonResponse
Routing
	RouteCollection
	RequestContext

Собственные компоненты:

EventListener
	LocaleListener
	MiddlewareListener
	ConverterListener
	StringToResponseListener

Application является DI-контейнером (наследует Pimple)
и реализует HttpKernel и Terminable интерфейсы.
Т.е. этот класс преобрабразует запросы в ответы и после
ответа что-то делает.

### Кратко о Pimple

Это очень простой класс, реализующий глобальный контейнер.
Он не просто хранит в себе набор данных, но и помогает управлять
зависимостями.
Вот так это работает:

// $c - текущий контейнер
$container['service'] = function ($c) {
  return new Service($c['arg']);
};
// расширить класс
$container['service'] = $container->extend('service', function($service, $c) {
  $service->setFrom($c['service.default_from']);
  return $service;
});
// синглтон
$container['single'] = $container->share(function ($c) {
  return new Single($c['arg']);
});
// расширить синглтон
$container['single'] = $container->share($container->extend('single', function ($single, $c) {
  $single->addExtension(new MySingleExtension());
  return $single;
}));

$container['rand'] = $container->protect(function () { return rand(); });


container = new Pimple();
$container['param'] = 'value';
// выполнить сервис и использовать
$container['service']->run();
// получить сервис
$container->raw['service'];

Также одни контейнеры могут использоваться внутри других.

### Возможности $app

Включить дебаг:
$app['debug'] = true;

Роутинг:

$app->get('/', 'Acme\\Foo::bar');
$app->get('/blog', function () use ($globalVar) {
$app->get('/blog/{id}', function ($app, $id) use ($globalVar) {
$app->post('/feedback', function (Request $request) {
$app->match('/blog', function () {
    // ...
})
->method('PUT|POST');
// предобрабтка параметра, в т.ч. сервисом
$app->get('/user/{id}', function ($id) {
    // ...
})->convert('id', function ($id) { return (int) $id; });
// или ассертами
->assert('postId', '\d+')
->assert('commentId', '\d+');
// default
->value('pageName', 'index');
// имя для роута
->bind('blog_post');

Глобально:
$app['controllers']
    ->value('id', '1')
    ->assert('id', '\d+')
    ->requireHttps()
    ->method('get')
    ->convert('id', function () { /* ... */ })
    ->before(function () { /* ... */ });

Перехват ошибок:
$app->error(function (\Exception $e, $code) {
    return new Response('We are sorry, but something went terribly wrong.');
});
Можно кидать ошибки через abort:
$app->abort(404, "Post $id does not exist.");

Редирект внешний:
$app->redirect('/hello');
Редирект внутренний:
$subRequest = Request::create('/hello', 'GET');
return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

Отправить JSONом:
$app->json($user);

Отправить потоком:
$stream = function () use ($file) {
        readfile($file);
    };
return $app->stream($stream, 200, array('Content-Type' => 'image/png'));

Чанками:
$stream = function () {
    $fh = fopen('http://www.example.com/', 'rb');
    while (!feof($fh)) {
      echo fread($fh, 1024);
      ob_flush();
      flush();
    }
    fclose($fh);
};
return $app->stream($stream, 200, array('Content-Type' => 'image/png'));

Простая отправка файла:
return $app->sendFile('/base/path/' . $path);
И с установкой параметров(см.http://api.symfony.com/master/Symfony/Component/HttpFoundation/BinaryFileResponse.html)
$app
    ->sendFile('/base/path/' . $path)
    ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'pic.jpg');

Безопасность:

//HTML экранирование
$app->escape($name);
// JSONирование:
$app->json(array('name' => $name));


LifeCicle:
// request...
$app->before(function (Request $request) {
// controller...
$app->after(function (Request $request, Response $response) {
// response...
$app->finish(function (Request $request, Response $response) {

Можно использовать в контексте приложения, а также для отдельных роутов
и их наборов.
Вторым параметром можно указать приоритет:
Application::EARLY_EVENT ...цифры... Application::LATE_EVENT


Собрать кучу роутов:
$blog = $app['controllers_factory'];
$blog->get('/create', function () {
$blog->get('/read', function () {
$blog->get('/edit', function () {
$blog->get('/delete', function () {
$app->mount('/blog', $blog);


### Провайдеры

Позволяют использовать одну часть приложения в другой. Похожи на сервисы,
но по сути являются не функционалом, а "связующим" звеном со сторонним кодом.
(Могут зарегать пачку сервисов или контроллеров).

Сначала провайдер нужно зарегистрировать и, возможно, настроить:

$app->register(new Acme\DatabaseServiceProvider(), array(
    'database.dsn'      => 'mysql:host=localhost;dbname=myapp',
    'database.user'     => 'root',
    'database.password' => 'secret_root_password',
));


Провайдеры для сервисов регистрируют в системе сервисы:
Для БД, логгирования, сессий, сериализации, почты, шаблонизатора,
переводов, урлогенератора, валидатора, http кэша, форм, безопасности, сервисов,
а также RememberMe.

Можно написать свой провайдер сервисов, он должен реализовывать этот интерфейс:
interface ServiceProviderInterface
{
    function register(Application $app);
    function boot(Application $app);
}

Или провайдер контроллеров:
interface ControllerProviderInterface
{
    function connect(Application $app);
}


### Тесты

PHPUnit и Selenium наше всё.
