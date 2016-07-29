// в HttpFoundation входят запрос, ответ, сессии, обработка файлов

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


### Request

$request->getPathInfo(); // URL, без параметров
$request->isXmlHttpRequest(); // is it an Ajax request?

// GET и POST
$request->query->get('foo');
$request->request->get('bar', 'default value if bar does not exist');

// SERVER
$request->server->get('HTTP_HOST');

// FILES
$request->files->get('foo');

// COOKIE
$request->cookies->get('PHPSESSID');

// Headers
$request->headers->get('content_type');

// Получить имя метода
$request->getMethod();


### Response

$response = new Response();
$response->setContent('<html><body><h1>Hello world!</h1></body></html>');
$response->setStatusCode(Response::HTTP_OK);
// или
$response = new Response('Hello '.$name, Response::HTTP_OK);

$response->headers->set('Content-Type', 'application/pdf');
$response->send();

// create a JSON-response with a 200 status code
$response = new Response(json_encode(['name' => $name]));
$response->headers->set('Content-Type', 'application/json');


There are also special classes to make certain kinds of responses easier:

For JSON, there is JsonResponse. See Creating a JSON Response.
For files, there is BinaryFileResponse. See Serving Files.
