<?php
if (!$user->isAdmin()) header('Location: /');


// run ajax tools
if (!empty($_POST)) {

    switch($_POST['command']) {
        case 'dbReboot':
            try {
                include $wshell->rootPath . '/remain/run/tools/dbReboot.php';
            } catch(Exception $exc) {
                die('<div class="alert alert-warning">Ошибка: ' . $exc->getMessage() . '</div>');
            }
            echo '<div class="alert alert-success">Структура БД восстановлена.</div>';
            break;
        case 'dbFill':
            try {
                include $wshell->rootPath . '/remain/run/tools/dbFill.php';
            } catch(Exception $exc) {
                die('<div class="alert alert-warning">Ошибка: ' . $exc->getMessage() . '</div>');
            }
            echo '<div class="alert alert-success">БД заполнена тестовыми данными.</div>';
            break;

        default:
            break;
    }

} else {

    $template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
    echo $template->render(array('user' => $user, 'active5' => 'active'));
}

