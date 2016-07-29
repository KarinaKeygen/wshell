// Открытие ссылки без реферера.
// see http://blog.kotowicz.net/2011/10/stripping-referrer-for-fun-and-profit.html
// and http://attacker.kotowicz.net/lose-referer/test.php#
function openEx(url)
{
    w = window.open();
    w.document.location = "data:text/html,<script>location='" + url + '&_=' + Math.random() + "'</scr"+"ipt>";
    //or w.document.location = 'data:text/html,<html><meta http-equiv="refresh" content="0; url='+ url + '"></html>';
    return false;
}