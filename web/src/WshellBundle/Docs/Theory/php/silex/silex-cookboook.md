### JSON Request Body

В HTTP выглядит так:

POST /blog/posts
Accept: application/json
Content-Type: application/json
Content-Length: 57

{"title":"Hello World!","body":"This is my first post!"}

Ответ может быть таким:

HTTP/1.1 201 Created
Content-Type: application/json
Content-Length: 65
Connection: close

{"id":"1","title":"Hello World!","body":"This is my first post!"}


Простейший вариант обработки - глобальный:

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

В контроллере:

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->post('/blog/posts', function (Request $request) use ($app) {
    $post = array(
        'title' => $request->request->get('title'),
        'body'  => $request->request->get('body'),
    );

    $post['id'] = createPost($post);

    return $app->json($post, 201);
});

Простой тест:
$ curl http://blog.lo/blog/posts -d '{"title":"Hello World!","body":"This is my first post!"}' -H 'Content-Type: application/json'
{"id":"1","title":"Hello World!","body":"This is my first post!"}



### Converting Errors to Exceptions

По умолчанию, этого не происходит. Сначала нужно зарегать ошибки и/или эксепшены:

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
ErrorHandler::register();

Потом на продакшене можно легко убрать дебаг:

ExceptionHandler::register(false);


http://silex.sensiolabs.org/doc/cookbook/multiple_loggers.html
http://silex.sensiolabs.org/doc/cookbook/assets.html

