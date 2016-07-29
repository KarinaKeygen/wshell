<?php
if (!$user->isAdmin()) header('Location: /');

$template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
echo $template->render([
    'active1' => 'active',
    'user'    => $user,
]);