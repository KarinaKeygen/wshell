$content = $this->renderView(
    'AcmeHelloBundle:Hello:index.html.twig',
    array('name' => $name)
);

return new Response($content);


// или
return $this->render(
    'AcmeHelloBundle:Hello:index.html.twig',
    array('name' => $name)
);



Логическое имя шаблона:
BundleName:ControllerName:TemplateName
