<?php

/*
 * NOTE: text how http:// display in html (<a href='...'>...</a>)
 */

class Terminal
{

    static $ls_documentation = "Выдаёт содержимое текущей директории в виде списка, необходим токен. (Использование: ls [token])";

    public function ls($token = null, $path = null)
    {
        if (strcmp(md5("noname:1234"), $token) == 0) {
            if (@is_file($path)) {
                throw new Exception("No. I'ts file, why are you doing this? By hand enter path, bummer");
            } elseif (@preg_match("/\.\./", $path)) {
                throw new Exception("No directory traversal Dude");
            } elseif (@!is_dir($path)) {
                throw new Exception("Nothing. You make me feel sad.");
            } else {
                $base = preg_replace("/(.*\/).*/", "$1", $_SERVER["SCRIPT_FILENAME"]);
                $path = $base . ($path[0] != '/' ? "/" : "") . $path;
                $dir  = opendir($path);
                while($name = readdir($dir)) {
                    $fname = $path . "/" . $name;
                    if (!is_dir($name) && !is_dir($fname)) {
                        $list[] = $name;
                    }
                }
                closedir($dir);
                return $list;
            }
        } else {
            throw new Exception("Access Denied");
        }
    }

    static $login_documentation = "login to the server (return token). Using:\nlogin [login] [password]";

    public function login($user = '', $passwd = '')
    {
        if (strcmp($user, 'noname') == 0 && strcmp($passwd, '1234') == 0) {
            return md5($user . ":" . $passwd);
        } else {
            throw new Exception("Wrong Password. This is not the case, see 'help login'");
        }
    }

    static $soundtest_documentation = "Soundtest command";

    public function soundtest()
    {
        return '<audio controls><source src="sound/test.mp3" type="audio/mpeg"></audio>';
    }

    static $whoami_documentation = "return user information";

    public function whoami()
    {
        return "your User Agent : " . $_SERVER["HTTP_USER_AGENT"] .
        "\nyour IP : " . $_SERVER['REMOTE_ADDR'] .
        "\nyou access this from : " . $_SERVER["HTTP_REFERER"];
    }

    static $asciicam_documentation = "ASCII camera(https://github.com/idevelop/ascii-camera)";

    public function asciicam()
    {
        return '<pre id="ascii"></pre>
<script src="script/camera.js"></script>
<script src="script/ascii.js"></script>
<script src="script/app.js"></script>';
    }


    static $ram_documentation = "check RAM memory usage";

    public function ram()
    {
        return 'pRamPeak: ' . (memory_get_peak_usage(TRUE) >> 10) . 'kB curMemoryPeak: ' . (memory_get_peak_usage() >> 10) . "kB";
    }

    static $test_documentation = "Testing of all elements of the system";

    public function test()
    {
        // dont true. Need make it with help JS!
        //sleep(2);
        $memory = (int)(diskfreespace('.') / 1024 / 1024);
        return "free physical memory: $memory Mb
Character proccesor: 5x4.2 GHz EBP64 Mesk Blesa Company
Visualization driver: not information
Sound driver: not information
I/O controller: NABLA-terminal ver 1.0";
    }

    static $imitation_documentation = "imitation of a typical human conversation from the standpoint computer\n
      The ideal solution for the Turing test";

    public function imitation()
    {
        return "Bla Bla Bla! Bla-blabla bla-bla-bla\n
        bla bla bla bla bla bla\n
        Bla BLALBLABA babla!\n
        bla bla? bla blabla. Bla.";
    }

    static $random_documentation = 'Generate a random number. most random';

    public function random()
    {
        return '42';
    }

    static $bb_documentation = 'Beavis & Butt-head ASCII';

    public function bb()
    {
        return <<<EOD
          ________________
         /                \
        / /          \ \   \
        |                  |
       /                  /
      |      ___\ \| | / /
      |      /          \
      |      |           \
     /       |      _    |
     |       |       \   |
     |       |       _\ /|     I am Corn Julio!!! I need TP for my
     |      __\     <_o)\o-    bunghole!!!! Where we come from we
     |     |             \     have no bungholes...Would you like
      \    ||             \    to be my bunghole?
       |   |__          _  \    /
       |   |           (*___)  /
       |   |       _     |    /
       |   |    //_______/
       |  /       | UUUUU__
        \|        \_nnnnnn_\-\
         |       ____________/
         |      /
         |_____/


                                      .-------------.
                                     /               \
                                    / .-----.         \
 I am the Great Cornholio!!         |/ --`-`-\         \
                                    |         \        |
 I need TP for my bunghole!!         |   _--   \       |
                                     _| =-.     |      |
 Come out with your pants down!      o|/o/      |      |
                                     /  ~       |      |
 ARE YOU THREATENING ME??          (____@)  ___ |      |
                                       _===~~~.`|      |
 Oh. heh-heh.  Sorry about that.   _______.--~  |      |
                                    \_______    |      |
 heh-heh.  This is cool.  heh-heh        |  \   |      |
                                          \_/__/       |
                                        /            __\
                                        -| Metallica|| |
                                        ||          || |
                                        ||          || |
                                        /|          / /

     ________________                            ______________
    /                \                          / /            \-___
   / /          \ \   \                         |     -    -         \
   |                  |                         | /         -   \  _  |
  /                  /                          \    /  /   //    __   \
 |      ___\ \| | / /                            \/ // // / ///  /      \
 |      /         \                              |             //\ __   |
 |      |           \                            \              ///     \
/       |      _    |                             \               //  \ |
|       |       \   |                              \   /--          //  |
|       |       _\ /|                               / (o-            / \|
|      __\     <_o)\o-                             /            __   /\ |
|     |             \                             /               )  /  |
 \    ||             \                           /   __          |/ / \ |
  |   |__          _  \                         (____ *)         -  |   |
  |   |           (*___)                            /               |   |
  |   |       _     |                               (____            |  |
  |   |    //_______/                                ####\           | |
  |  /       | UUUUU__                                ____/ )         |_/
   \|        \_nnnnnn_\-\                             (___             /
    |       ____________/                              \____          |
    |      /                                              \           |
    |_____/                                                \___________\
   /\/\/\/\/\/\/\/\/\                        /\/\/\/\/\/\/\/\/\/\/\/\/\
  /                  \                      /                          \
 <     B E A V I S    >          AND       <     B U T T - H E A D      >
  \                  /                      \                          /
   \/\/\/\/\/\/\/\/\/                        \/\/\/\/\/\/\/\/\/\/\/\/\/


AVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVIS
BEAVIS ________________BEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVI
SBEAV /                \ISASSWIPEBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISB
AVISB/ /          \ \   \EAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAV
ISBEA|                  |VISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVIS
BEAV/                  /BEAVISBEAVISBEAVISFARTKNOCKERSBEAVISBEAVISBEAV
ISB|      ___\ \| | / /ISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEA
VIS|      /          \VISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEA
BEA|      |           \BEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVI
VI/       |      _    |SBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAV
SB|       |       \   |
EA|       |       _\ /|     THESE ARE THOSE GUYS FROM MY DREAM!!
VI|      __\     <_o)\o-    THOSE ALIEN GUYS!! THEY LIKE COME INTO
SB|     |             \     MY ROOM WITH THIS SHINING WHITE LIGHT
EAV\    ||             \    AND THEY'VE GOT LIKE NADS ON THEIR
ISBE|   |__          _  \   HEADS AND THEN THEY LIKE TIE ME TO A
AVIS|   |           (*___)  CHAIR AND GET LIKE MEDIEVAL ON MY ASS!!
BEAV|   |       _     |    /
ISBE|   |    //_______/     BEAVISBEAVISBEAVISBEAVISBEAVISBEAVBUNGHOLE
AVIS|  /       | UUUUU__    BEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVIS
BEAVI\|        \_nnnnnn_\-\ BEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVIS
SBEAVI|       ____________/ BEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVIS
BEAVIS|      /BEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBEAVISBE
EOD;
    }

}
