<?php

$author   = $em->getRepository('user')->findOneBy(array('name' => $author));
$location = $em->getRepository('section')->findOneBy(array('id' => $section_id));
$sections = $em->getRepository('section')->findBy(array('user_id' => $author->get_id()));
$posts    = $em->getRepository('post')->findBy(array('section_id' => $section_id));
$discrip  = 'dis';

$template = $templater->loadTemplate($page . '.twig');
echo $template->render(array('author'   => $author,
                             'sections' => $sections,
                             'posts'    => $posts,
                             'location' => $location,
                             'discrip'  => $discrip));