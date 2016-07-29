<?php

namespace AppBundle\Service;

use MongoDB\Client as MongoClient;

// http://mongodb.github.io/mongo-php-library/
class Mongo extends MongoClient
{
  function __construct($uri)
  {
    parent::__construct($uri);
  }
}
