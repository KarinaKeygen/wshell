<?php

$wshell->initRedbean();

//$units = R::batch('unit',array( 1, R::count('unit') ));
$units = R::findAll('unit');

$template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
echo $template->render([
    'units'   => $units,
    'user'    => $user,
    'active2' => 'active'
]);