<?php
if (!$user->isAdmin()) header('Location: /');

use Wshell\Utils\Check;

$docDir = "$wshell->rootPath/remain/docs";

if (Check::isXHRequest()) {

    $curPath = $docDir . str_replace('.', '', $_POST['path']);

    // DIR UP
    if (!isset($_POST['docName'])) {
        $explode        = explode('/doc', $curPath);
        $curPath        = implode('/doc', [$explode[0], dirname($explode[1])]);
        $msg['dirDocs'] = getContentDir($curPath);
        echo json_encode($msg);
    } else {
        $docName = $_POST['docName'];
        // DIR DOWN
        if (substr($docName, -1) == '/') {
            $msg['dirDocs'] = getContentDir($curPath . $docName);
            echo json_encode($msg);
            // GET FILE
        } else {
            $msg['text'] = file_get_contents($curPath . $docName);
            echo json_encode($msg);
        }
    }

} else {
    $pathDocs = getContentDir($docDir);
    $template = $wshell->twig()->loadTemplate($wshell->action . '.twig');
    echo $template->render([
        'user'     => $user,
        'path'     => '/',
        'pathDocs' => $pathDocs,
        'active4'  => 'active']);
}


function getContentDir($path)
{
    $pathDocs = array_slice(scandir($path), 2);
    foreach($pathDocs as $key => $value) {
        if (is_dir($path . '/' . $value)) {
            $pathDocs[$key] .= '/';
        }
    }
    return $pathDocs;
}