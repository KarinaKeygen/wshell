<?php

$author_name = substr($_SERVER['REQUEST_URI'], 1);
$author      = $em->getRepository('user')->findOneBy(array('name' => $author_name));
$posts       = $em->getRepository('post')->findBy(array('user_id' => $author->get_id()));
$sections    = $em->getRepository('section')->findBy(array('user_id' => $author->get_id()));

$template = $templater->loadTemplate($page . '.twig');
echo $template->render(array('author'   => $author,
                             'sections' => $sections,
                             'posts'    => $posts,
                             'login'    => $client->isUser()));