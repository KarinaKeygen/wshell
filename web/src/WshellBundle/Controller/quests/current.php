<?php

$quests = require('../pages/quests/manifest.php');

$task = $quests[$user->ql];

$next = $user->ql + 1;
if (!isset($quests[$next])) {
    $finish = TRUE;
} else {
    $finish = FALSE;
    if (isset($_POST['key'])) {
        if (md5($_POST['key']) === $task['hash']) {
            // level up!
            $user->ql++;
            $wshell->initRedbean();
            $userDb = R::findOne('user', ' id = ? ', array($user->id));
            $userDb->ql++;
            R::store($userDb);
        }
    }
}

$template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
echo $template->render(array(
    'user'    => $user,
    'task'    => $quests[$user->ql],
    'active3' => 'active',
    'finish'  => $finish));