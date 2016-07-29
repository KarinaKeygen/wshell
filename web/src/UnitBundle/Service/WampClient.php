<?php

namespace UnitBundle\Service;

use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;

use Psr\Log\NullLogger;
use Thruway\Logging\Logger;

class WampClient extends Client
{
  public $session;

  function __construct($realm, $routerUrl)
  {
    Logger::set(new NullLogger());
    parent::__construct($realm);
    $this->addTransportProvider(new PawlTransportProvider($routerUrl));
  }

  public function unit($rexec, $data, $code, $name, $hookup)
  {
      return [
        "index" => 1,
        "order" => 1,
        "require" => $rexec,
        'source' => $code,
        'name' => $name,
        'hookup' => $hookup,
        'args' => $data
      ];
  }

  // 16
  public function chain()
  {
      return [
          [
              "index" => 1,
              "rexec" => ["php"],
              "code" =>  "input*2;",
              "data" =>  ["input" => 1]
          ],[
              "index" => 2,
              "rexec" => ["php"],
              "code" =>  "input*2;",
              'prev' => 'input'
          ],[
              "index" => 3,
              "rexec" => ["php"],
              "code" =>  "input*2;",
              'prev' => 'input'
          ],[
              "index" => 4,
              "rexec" => ["php"],
              "code" =>  "input*2;",
              'prev' => 'input'
          ]
      ];
  }
}
