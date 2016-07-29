Описание всех следующих провайдеров состоит из:

+ Списка параметров(при регистрации)
+ composer require (если есть только в Fat Silex)
! Зависимость от других провайдеров.
пример использования

### twig

twig.path (string|array)
twig.templates (array)
twig.options (array)
twig.form.templates (array) ! FormServiceProvider

"twig/twig": ">=1.8,<2.0-dev"
! "symfony/twig-bridge": "~2.3"
	path(), url() из UrlGeneratorServiceProvider
	trans(), transchoice() из TranslationServiceProvider
	get a set of helpers из FormServiceProvider
	is_granted() из SecurityServiceProvider

Использование см. в документации Twig.

### url_generator

$app['url_generator']->generate('hello', array('name' => 'Igor'))
В twig:
{{ path('homepage') }}
{{ url('homepage') }}

### session

session.storage.save_path (string)
session.storage.options (array)
	name: The cookie name (_SESS by default)
	id: The session id (null by default)
	cookie_lifetime: Cookie lifetime
	cookie_path: Cookie path
	cookie_domain: Cookie domain
	cookie_secure: Cookie secure (HTTPS)
	cookie_httponly: Whether the cookie is http only

$app['session']->set('user', array('username' => $username));
$app['session']->get('user');


### validator

"symfony/validator": "~2.3"

use Symfony\Component\Validator\Constraints as Assert;
...
$errors = $app['validator']->validateValue($email, new Assert\Email());

Также можно гибко валидировать массивы и классы.


### form

form.secret - сид для CSRF защиты. например, md5(__DIR__).
form.factory: An instance of FormFactory, that is used for build a form.


"symfony/form": "~2.3"

!   "symfony/validator": "~2.3",
    "symfony/config": "~2.3",
    "symfony/translation": "~2.3" -> validation
    "symfony/locale": "~2.3" -> intl extension
    "symfony/twig-bridge": "~2.3" -> twig

Использование:

	$data = array(
	'name' => 'Your name',
	'email' => 'Your email',
	);

	$form = $app['form.factory']->createBuilder('form', $data)
	->add('name')
	->add('email')
	->add('gender', 'choice', array(
	'choices' => array(1 => 'male', 2 => 'female'),
	'expanded' => true,
	))
	->getForm();

	$form->handleRequest($request);

	if ($form->isValid()) {
	$data = $form->getData();


	<form action="#" method="post">
	    {{ form_widget(form) }}

	    <input type="submit" name="submit" />
	</form>


	$form = $app['form.factory']->createBuilder('form')
	->add('name', 'text', array(
	'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
	))
	->add('email', 'text', array(
	'constraints' => new Assert\Email()
	))
	->add('gender', 'choice', array(
	'choices' => array(1 => 'male', 2 => 'female'),
	'expanded' => true,
	'constraints' => new Assert\Choice(array(1, 2)),
	))
	->getForm();


### security
### rememberMe
### mailer
### monolog
### translator
### serializer

### controller
Провайдер для управления контроллерами.
http://silex.sensiolabs.org/doc/providers/service_controller.html
