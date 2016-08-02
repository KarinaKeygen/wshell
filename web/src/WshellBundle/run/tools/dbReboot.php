<?php

$wshell->initRedbean();


if(R::inspect()) {
    throw new Exception('БД не пуста');
}

$user = R::dispense('user');
$user->pass = 'a55b41c913613c11850f5fca08e66387';
$user->name = 'pilot114';
$user->mail = 'pilot114@bk.ru';
$user->role = 'admin';
$user->ql = 2;
$user->active = TRUE;
$user->lastVisit = date('Y-m-d H:i:s');
$user->lastIpv4 = sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
R::store($user);

$unit = R::dispense('unit');
$unit->lastUpdate = date("Y-m-d H:i:s");
$unit->name = 'passBox';
$unit->anno = 'its test anno';
$unit->type = 3;
$unit->hookup = 'a:1:{s:5:"input";a:3:{s:9:"passwords";a:3:{s:4:"type";s:4:"heap";s:4:"elem";s:9:"#^[\S]+$#";s:4:"norm";s:4:"1234";}s:8:"dataAuth";a:2:{s:4:"type";s:10:"heapStruct";s:3:"map";a:2:{s:5:"login";a:3:{s:4:"type";s:6:"scalar";s:4:"elem";s:20:"#^[a-zA-Z0-9@._-]+$#";s:4:"norm";s:5:"login";}s:4:"pass";a:3:{s:4:"type";s:6:"scalar";s:4:"elem";s:9:"#^[\S]+$#";s:4:"norm";s:4:"1234";}}}s:10:"masterPass";a:3:{s:4:"type";s:6:"scalar";s:4:"elem";s:9:"#^[\S]+$#";s:4:"norm";s:8:"12345678";}}}';
$unit->guiMode = 1;
$unit->access = 'public';
$unit->info = 'test info';
        
R::store($unit);
