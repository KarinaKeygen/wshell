### Подключение

$(function(){
    // код
});

Или песочница:

(function($, undefined){
    // тут тихо и уютно
    // мы всегда будем уверены, что $ == jQuery
    // a undefined не переопределен ;)
})(jQuery);


### Selectors

$("div article h2") === $("div").find("article").find("h2")

$("*")
$("article > h2")
$("article > *")
$("h1 + h2")
$("#stick ~ article")

$("article").children()
$("p").parent()

$("#stick").prev()
$("#stick").next()

Поиск по атрибутам см. css3. Кратко:

a[href] a[href=#] a[href~=#] (# есть подстрока для href)
a[href*=] a[href^=] a[href$=] - как в регулярках

НО нужно помнить о экранировании спец.символов: $("a[href^=\\/]")

+ продвинутые селекторы:
:first-child
:last-child
:nth-child(2n+1)
:not(...)

Нативный js для селекта:

getElementById(id)
getElementsByName(name)
getElementsByClassName(class)
getElementsByTagName(tag)
querySelectorAll(selector)


ВАЖНО: селект не кэшируется со всеми вытекающими,
зато при использовании цепочек мы селектаем лишь 1 раз.

Скорость: id > class > tag


### Атрибуты и свойства

css(property, [value])
css({key: value, key:value})
css(property, function(index, value) { return value })

addClass(className)
addClass(function(index, currentClass){ return className })
hasClass(className)
removeClass(className)
removeClass(function(index, currentClass){ return className })
toggleClass(className)
toggleClass(className, switch)
toggleClass(function(index, currentClass, switch){ return className }, switch)

className - сьрока из списка классов через пробел

attr(attrName, [attrValue])
removeAttr(attrName)

prop(propName, [propValue])
removeProp(propName)

ВАЖНО: для отключения элементов формы, и для проверки/изменения
состояния чекбоксов мы всегда используем функцию prop()


### Events

События пользователя - всё, что можно сделать клавиатурой и мышкой.

$('.class').on(eventName, function(){});
$('.class').trigger(eventName);
$('.class').unbind([eventName]);

Кратко:

mousedown mouseup click dbclick mousemove
change resize scroll select submit
focus blur focusin focusout blur mouseenter mouseleave mouseover mouseout
keydown keyup keypress
load unload
touchstart touchend touchmove touchcancel

На порядок событий (снизу вверх по DOM иерархии) влияют след. методы:

event.preventDefault(); - отмена дефолта
event.stopPropagation(); - отмена "всплытия"?
return false; - то же самое, что верхние два
event.stopImmediatePropagation(); - при конфликте выполнится только ЭТО событие.


Если вместо селектора - js объект, навешиваем событие на него!

$(obj).on('someEvent', function(){
    this.test();
});
$(obj).trigger('someEvent');


Есть также более хитрая модификация on:
$('body').on('click', 'a', function() {});
click навешивается на 'body' но работает на 'a'. Почему сразу не на 'a'?
Дело в том, что это позволяет навешивать события на элементы, которые
могут появиться потом. Другая польза в том, что создается всего 1 обработчик,
а не по экземпляру для каждого 'a'.


У событий могут быть пространства событий. Пример:

click.namespace - ОСОБЫЙ клик. Не вызовется на click! или на click.other
Для, например, unbind всех событий определенного пространства событий:
unbind('.namespace');


### Effects

Скрыть/показать (переключают css display):

hide(speed, endCallback);
show(speed, endCallback);
toggle(speed, endCallback);

speed: 'fast'|'slow'|int

Только на высоту:

slideUp(speed, endCallback);
slideDown(speed, endCallback);
slideToggle(speed, endCallback);

Только на прозрачность:

fadeIn(speed, endCallback);
fadeOut(speed, endCallback);
fadeToggle(speed, endCallback);
fadeTo(speed, value, endCallback);

Обобщаем: всё это есть плавное изменение css свойств, и эту магию
делает функция animate(), у которой множество параметров...


### edit DOM

$("p").after("<hr/>")         == $("<hr/>").insertAfter("p")
$("p").before("<hr/>")        == $("<hr/>").insertBefore("p")
$("p").append("<hr/>")        == $("<hr/>").appendTo("p")
$("p").prepend("<hr/>")       == $("<hr/>").prependTo("p")
$("шило").replaceWith("мыло") == $("мыло").replaceAll("шило")

wrap(element)      // оборачиваем каждый найденный элемент новым элементом
unwrap(element)    // оборачивает найденные элементы новым элементом
wrapAll(element)   // оборачивает контент каждого найденного элемента новым элементом
wrapInner(element) // удаляет родительский элемент у найденных элементов

clone([bool]) - клонирует выбранные элементы, для дальнейшей вставки копий
назад в DOM, позволяет так же копировать и обработчики событий ( bool = true )

Удаление:

detach() - удалить только из DOM
empty()  - очистка содержимого
remove() - удаление

html([content])
text([content])

offset([{ top: 10, left: 30 }]) - относительно document
position([{ top: 10, left: 30 }]) - относительно parent

height([size])
width([size])

innerHeight() и innerWidth()         - вкл padding
outerHeight() и outerWidth()         - вкл padding и border
outerHeight(true) и outerWidth(true) - вкл padding, border и margin

scrollLeft([value])
scrollTop([value])


### Формы

Пример перехвата процесса POST:

$('form').submit(function(){
    $(this).find('.error').remove();
    if ($(this).find('input[type=name]').val() == '') {
        $(this).find('input[name=user]').before('<div class="error">Введите имя</div>');
        return false;
    }
    $.post(
        $(this).attr('action'),
        $(this).serialize()
    );
    return false;
});


serialize() - собирает поля в строку
serializeArray() - собирает поля в массив
val([value])

Примеры для особых полей форм:

$('input[type=radio][name=choose][value=2]').prop('checked', true)
$('input[name=check] ').prop('checked', true)
$('input[name=check] ').is(':checked')
var $select = $('form select[name=Role]');
$select.append('<option>Manager</option>');
$select.find('option:eq(2)').prop('selected', true);
$select.remove('option');
Преобр. в multiselect:
$('select').attr('size', $('select option').length);
$('select').attr('multiple', true);


### AJAX

