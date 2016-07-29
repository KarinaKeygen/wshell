<?php

$m = new MongoClient();
$db = $m->selectDB("test");
$collection = new MongoCollection($db, 'people');

$peopleQuery = ['name' => 'ivan'];

$cursor = $collection->find($peopleQuery);
foreach ($cursor as $doc) {
    var_dump($doc);
}
