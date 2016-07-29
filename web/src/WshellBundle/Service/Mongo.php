<?php

namespace WshellBundle\Service;

use MongoDB\Client;

// http://mongodb.github.io/mongo-php-library/
class Mongo extends Client
{
  function __construct($uri)
  {
    parent::__construct($uri);
  }
}
