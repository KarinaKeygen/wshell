<?php

class Demo
{
    static $login_documentation = "return auth token";

    public function login($user, $passwd)
    {
        if (strcmp($user, 'demo') == 0 &&
            strcmp($passwd, 'demo') == 0
        ) {
            // If you need to handle more than one user you can
            // create new token and save it in database
            return md5($user . ":" . $passwd);
        } else {
            throw new Exception("Wrong Password");
        }
    }

    static $whoami_documentation = "return user information";

    public function whoami()
    {
        return array(
            "user-agent"  => $_SERVER["HTTP_USER_AGENT"],
            "your ip"     => $_SERVER['REMOTE_ADDR'],
            "referer"     => $_SERVER["HTTP_REFERER"],
            "request uri" => $_SERVER["REQUEST_URI"]);
    }
}

$server = new Junior\Server(new Demo());
$server->process();
header('Content-Type: text/html');
