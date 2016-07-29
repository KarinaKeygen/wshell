<?php
if (!$user->isAdmin()) header('Location: /');

$wshell->initRedbean();
$users = R::findAll('user');


$template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
echo $template->render(array('user' => $user, 'users' => $users, 'active2' => 'active'));