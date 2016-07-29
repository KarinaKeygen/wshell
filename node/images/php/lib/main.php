<?php

require 'vendor/autoload.php';
$d = new PhpToolbox\Utils\Debug();

list($curScriptName, $metaData, $unitDataFile) = $argv;
$unitData = file_get_contents('cache/' . $unitDataFile);
$metaData = json_decode(base64_decode($metaData), true);
$unitData = json_decode(base64_decode($unitData), true);
$hookup = json_decode($metaData['Hookup'], true);

require 'cache/' . $metaData['Name'];

// empty storage handler
$storage = new \PhpToolbox\Storages\Dummy(null);
$unitRun = new $metaData['Name']($storage, $hookup);
$output = $unitRun->uiOutput($unitData);

$debug = $d->result();
echo json_encode([$output, $debug]);
