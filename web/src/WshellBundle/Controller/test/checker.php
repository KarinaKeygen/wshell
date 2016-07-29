<?php

$data   = ['masterPass'          => '',
           'passwords:0'         => 'test',
           'passwords:1'         => 'test',
           'dataAuth:mail:login' => 'pilot114',
           'dataAuth:mail:pass'  => 'yfjtfyjtdy',
           'mode'                => 'save'
];
$hookup = <<<EOL
input:
    passwords:
        dimension: 1
        elem: '#^[\S]+$#'
        norm: '1234'
    dataAuth:
        dimension: 1
        map:
            login:
                elem: '#^[a-zA-Z0-9@._-]+$#'
                norm: login
            pass:
                elem: '#^[\S]+$#'
                norm: '1234'
    masterPass:
        elem: '#^[\S]+$#'
        norm: '12345678'
mode:
    save: [passwords, dataAuth, masterPass]
    load: [masterPass]
EOL;

$array = Symfony\Component\Yaml\Yaml::parse($hookup);

try {
    $checker = new \Wshell\Validator\InputChecker($data, $array);
    print_r($checker->getData());
} catch (Exception $e) {
    echo $e->getMessage();
}
