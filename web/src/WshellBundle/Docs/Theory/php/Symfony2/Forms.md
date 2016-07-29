### Создание формы

Для управления формами нужно сделать следующее:

Иметь МОДЕЛЬ, на основе которой генерится форма. (например, src/Acme/TaskBundle/Entity/Task.php)
Затем в контроллере написать примерно следующее:

	$task = new Task();
	$task->setTask('Write a blog post');
	$task->setDueDate(new \DateTime('tomorrow'));

	$form = $this->createFormBuilder($task)
		    ->add('task', 'text')
		    ->add('dueDate', null, array('mapped' => false))
		    ->add('save', 'submit')
		    ->getForm();

	return $this->render('AcmeTaskBundle:Default:new.html.twig', array(
	    'form' => $form->createView(),
	));

Тут мы добавляем поля и их типы + добавляем кнопку сабмита.
$task - инстанс модели(с фиктивными данными), в шаблоне будет просто:

	{{ form(form) }}
	или, для отключения html5 валидации:
	{{ form(form, {'attr': {'novalidate': 'novalidate'}}) }}

Всё! Форма достаточна умна, чтобы связываться с сооветствующими
сеттерами/геттерами модели.
Для boolean соответственно подхватываются HasField() или isField()

Если нужно использование формы в нескольких местах, нужно написать специальный класс для неё.



### Обработка формы

	... то же самое ...
	$form->handleRequest($request);

	if ($form->isValid()) {
	// тут, например, сохраняем в БД
	}
	return $this->redirect($this->generateUrl('task_success'))

Если мы используем несколько submit-кнопок, можно выбрать поведение, выбрав именно нажатую:

	$nextAction = $form->get('saveAndAdd')->isClicked()
		? 'task_new'
		: 'task_success';

С полями формы, не относящимся к модели, можно работать так:
	
	$form->get('dueDate')->getData();
	$form->get('dueDate')->setData(new \DateTime());

### Валидация

Важно понимать, что мы валидируем не саму форму, а именно объект, связанный
с формой. (т.е. происходит валидация модели).
Правила валидации задаются в файле validation.yml или в аннотациях модели.
При этом, для каждого параметра в настройках валидации можно указать группу валидации -
это дает возможность использовать разные наборы валидации для одной модели.

Пример правил валидации:

	#Acme/TaskBundle/Resources/config/validation.yml
	Acme\TaskBundle\Entity\Task:
	    properties:
		task:
		    - NotBlank: ~
		dueDate:
		    - NotBlank: ~
		    - Type: \DateTime

Для удобного управления наборами валидации, вторым параметром в createFormBuilder
может передаваться список групп валидации. Или сразу в классе формы:

	use Symfony\Component\OptionsResolver\OptionsResolverInterface;

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
		'validation_groups' => array('registration'),
	    ));
	}

Установив 'validation_groups' => false можно вообще выключить валидацию,
однако некоторые базовые проверки все равно будут работать. Чтобы отключить и их,
нужно переопределить POST_SUBMIT событие.

Установив 'validation_groups' => array(
            'Acme\AcmeBundle\Entity\Client',
            'determineValidationGroups',
        ),
можно использовать кастомный статический метод для валидации.

И наконец, написав замыкание:

	'validation_groups' => function(FormInterface $form) {
		$data = $form->getData();
		if (Entity\Client::TYPE_PERSON == $data->getType()) {
		return array('person');
		} else {
		return array('company');
		}
	}

мы сразу описываем правило прямо в классе формы.

На разные submit-формы можно навесить разные группы валидации:

	$form = $this->createFormBuilder($task)
	    // ...
	    ->add('nextStep', 'submit')
	    ->add('previousStep', 'submit', ['validation_groups' => false])
	    ->getForm();

Встроенных типов данных очень много, список можно посмотреть здесь:
(http://symfony.com/doc/current/reference/forms/types.html)
Также можно создать свои типы:
(http://symfony.com/doc/current/cookbook/form/create_custom_field_type.html)

Многие встроенные типы имеют разнообразные опции.
Также везде можно использовать опцию required (т.е. поле обязательно для заполнения).
ВАЖНО: опции определяют не только валидацию. Например, mapped => false означает,
что параметр не будет записан в модель( например, такой как "согласен с условиями..."),
а label определяет пояснение к полю формы.

Странная, но возможно полезная особенность форм: можно не указать тип, и symfony
сама попытается угадать нужный тип данных(опции при этом можно передавать):

	->add('task', null, array('attr' => array('maxlength' => 4)))	








### Класс формы

Хорошая практика - выносить форму из контроллера в отдельный класс:

	// src/Acme/TaskBundle/Form/Type/TaskType.php
	namespace Acme\TaskBundle\Form\Type;

	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;

	class TaskType extends AbstractType
	{
	    public function buildForm(FormBuilderInterface $builder, array $options)
	    {
		$builder
		    ->add('task')
		    ->add('dueDate', null, array('widget' => 'single_text'))
		    ->add('save', 'submit');
	    }

	    public function getName()
	    {
		return 'task';
	    }
	}

Соответственно, в контроллере будет:

	$form = $this->createForm(new TaskType(), $task);

Также в форме можно сразу указать класс модели:

	$resolver->setDefaults(array(
		'data_class' => 'Acme\TaskBundle\Entity\Task',
	    ));

Для наиболее удобного вызова формы, её можно определить как сервис:

	services:
	    acme_demo.form.type.task:
		class: Acme\TaskBundle\Form\Type\TaskType
		tags:
		    - { name: form.type, alias: task }

Тогда вызов будет таким:

	$form = $this->createForm('task', $task);

Побочно, это позволит включать одну форму в другую:

	// src/Acme/TaskBundle/Form/Type/ListType.php
	// ...

	class ListType extends AbstractType
	{
	    public function buildForm(FormBuilderInterface $builder, array $options)
	    {
		// ...

		$builder->add('someTask', 'task');
	    }
	}







### Форма в отображении

Типичный каркас формы:

	# src/Acme/TaskBundle/Resources/views/Default/new.html.twig
	{{ form_start(form) }}
	    # глобальные ошибки формы
	    {{ form_errors(form) }}
	    # ошибки, леблы, виджеты и пр.
	    {{ form_row(form.task) }}
	    {{ form_row(form.dueDate) }}
	# скрытые поля, CSRF защита
	{{ form_end(form) }}

form_row - основной хелпер, через него происходит вся кастомизация формы.

{{ form_row(form.task) }} =
<div>
    {{ form_label(form.task) }}
    {{ form_errors(form.task) }}
    {{ form_widget(form.task) }}
</div>

Основной прием кастомизации это добавление атрибутов в тэг input:

	{{ form_widget(form.task, {'attr': {'class': 'task_field'}}) }}

Получение доступа к параметрам поля "вручную":

	{{ form.task.vars.id }}
	{{ form.task.vars.name }}
	{{ form.task.vars.label }}

Если хочется написать именно свой HTML, нужно переопределить twig блоки
(form_row, form_errors, textarea_widget(внутри form_widget для textarea) и т.д.)

	{# src/Acme/TaskBundle/Resources/views/Form/fields.html.twig #}
	{% block form_row %}
	{% spaceless %}
	    <div class="form_row">
		{{ form_label(form) }}
		{{ form_errors(form) }}
		{{ form_widget(form) }}
	    </div>
	{% endspaceless %}
	{% endblock form_row %}

И в view указываем ссылку на новый html:

	{% form_theme form 'AcmeTaskBundle:Form:fields.html.twig' %}

или

	{% form_theme form with 'AcmeTaskBundle:Form:fields.html.twig' %}

То же самое мы можжем сделать глобально, в конфигурации:

	# app/config/config.yml
	twig:
	    form:
		resources:
		    - 'AcmeTaskBundle:Form:fields.html.twig'

И наконец, чтобы быстро кастомизировать twig блок и сразу его использовать
только в текущем шаблоне, можно сделать так:

	{% form_theme form _self %}

	{% block form_row %}
	    {# custom field_row output #}
	{% endblock form_row %}

	{% block content %}
	    {# ... #}
	    {{ form_row(form.task) }}
	{% endblock %}





### Форма и БД

	$em = $this->getDoctrine()->getManager();
	$em->persist($task);
	$em->flush();

Модель можно просто извлечь из формы:

	$task = $form->getData();



### Другое

Установка method и action(3 способа):

	$form = $this->createFormBuilder($task)
	->setAction($this->generateUrl('target_route'))
	->setMethod('GET') ...

	$form = $this->createForm(new TaskType(), $task, array(
	    'action' => $this->generateUrl('target_route'),
	    'method' => 'GET',
	));

	{{ form_start(form, {'action': path('target_route'), 'method': 'GET'}) }}



Встраивание нескольких моделей в форму:

1) Создаем 2 модели. Одна авляется свойством другой. (нужно также сделать сеттер/геттер)
2) Создаем 2 соответствующих класса форм. В одной из них поле - это форма:

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    // ...

	    $builder->add('category', new CategoryType());
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
		'data_class' => 'Acme\TaskBundle\Entity\Task',
		'cascade_validation' => true,
	    ));
	}

И наконец, в шаблоне form_row это дочерняя форма:

	<h3>Category</h3>
	<div class="category">
	    {{ form_row(form.category.name) }}
	</div>

При получении результатов дочернюю модель мы получаем через созданный ранее геттер.


CSRF защита включается во все формы по умолчанию (скрытое поле _token)
CSRF опции(в setDefaults класса формы):

'csrf_protection' => false, // выключить
'csrf_field_name' => '_token', // переименовать
'intention'       => 'task_item', // дополнительный ключ для повышения "уникальности"


Создание формы без модели:

	$defaultData = array('message' => 'Type your message here');
	$form = $this->createFormBuilder($defaultData) ...
	// вернет ассоциативный массив с данными (если не был установлен data_class)
	$data = $form->getData();

В этом случае валидацию нужно "прикрутить" вручную:

	use Symfony\Component\Validator\Constraints\Length;
	use Symfony\Component\Validator\Constraints\NotBlank;

	$builder
	   ->add('firstName', 'text', array(
	       'constraints' => new Length(array('min' => 3)),
	   ))
	   ->add('lastName', 'text', array(
	       'constraints' => array(
		   // если используются группы, указываем их
		   new NotBlank(array('groups' => ['create', 'update']),
		   new Length(array('min' => 3)),
	       ),
	   ));




### "Эталонная" реализация
