<?php

$author   = $em->find('user', $_SESSION['id']);
$sections = $em->getRepository('section')->findBy(array('user_id' => $author->get_id()));
$post     = $em->find('post', $param);

$template = $templater->loadTemplate($page . '.twig');
echo $template->render(array('sections'     => $sections,
                             'post'         => $post,
                             'request_post' => $param,
                             'author'       => $author,
                             'login'        => $client->isUser()));