5. Оборачивайте объявления callback-функций в циклах в замыкание
var elements = document.getElementsByTagName("div");
for (var i = 0, len = elements.length; i < len; i++) {
    elements[i].addEventListener("click", function () {
        console.log("Div number", i);
    });
}
Вопреки ожиданиям, на какой бы элемент div я ни нажал, в сообщении будет указан индекс последнего элемента div. Связано это с тем, что внутрь callback-функции, передаваемой в обработчик события, доступно значение переменной i, а не его копия в момент объявления функции. Решить это проблему можно созданием замыкания внутри цикла:

var elements = document.getElementsByTagName("div");
for (var i = 0, len = elements.length; i < len; i++) {
    (function () {
        var j = i;
        elements[i].addEventListener("click", function () {
            console.log("Div number", j);
        });
    })();
}
Теперь внутри замыкания мы копируем значение переменной i в переменную j, и используем ее внутри обработчика события.




