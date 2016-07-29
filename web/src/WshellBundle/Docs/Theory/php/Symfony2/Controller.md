hello:
    path:      /hello/{firstName}/{lastName}
    defaults:  { _controller: AcmeHelloBundle:Hello:index, color: green }



namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelloController extends Controller
{
    // порядок аргументов не важен. Request по желанию
    public function indexAction(Request $request, $firstName, $lastName, $color)
    {
        // ...
    }
}
