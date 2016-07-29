blog_show:
    path:      /blog/{page}
    defaults:  { _controller: AcmeBlogBundle:Blog:show, page : 123 }
    requirements:
        page:  \d+|test
            _format:  html|rss
    methods:  [GET]

Требования у параметрам описываются регулярными выражениями.
Также в symfony 2.4 появились condition
(см. http://symfony.com/doc/current/components/expression_language/syntax.html):
 condition: "context.getMethod() in ['GET', 'HEAD'] and request.headers.get('User-Agent') matches '/firefox/i'"

где context это RequestContext. Выражение выше скомпилится в это:
if (rtrim($pathinfo, '/contact') === '' && (
    in_array($context->getMethod(), array(0 => "GET", 1 => "HEAD"))
    && preg_match("/firefox/i", $request->headers->get("User-Agent"))
)) 

В роутинге могут использоваться магические параметры:

_controller
_format
_locale



Подключение других роутов:
acme_hello:
    resource: "@AcmeHelloBundle/Resources/config/routing.yml"
    prefix:   /admin

Да, это означает что ко всем этим роутам добавиться префикс




php app/console router:debug
php app/console router:debug blog_show
