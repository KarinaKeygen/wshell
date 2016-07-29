<?php

if ($user->isVisitor()) {
    header('Location: /');
}


$author   = $user->id;
$sections = [1, 2, 3];
$posts    = [4, 5, 6];

$template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
echo $template->render(array('author'   => $author,
                             'sections' => $sections,
                             'posts'    => $posts,
                             'login'    => !$user->isVisitor()));