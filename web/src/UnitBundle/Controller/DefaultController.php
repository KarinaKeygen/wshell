<?php

namespace UnitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use Thruway\ClientSession;
use Parsedown;
// temp
use PhpToolbox\Storages\Mongo;
use PhpToolbox\Utils\Formatter as F;
use UnitBundle\Tool\UIMapper;

class DefaultController extends Controller
{
    public function uiTestAction()
    {
        return $this->render('UnitBundle:Default:uitest.html.twig', [
        ]);
    }

    public function chainAction()
    {
      $units = $this->get('mongo')->wshell->units->find()->toArray();

      return $this->render('UnitBundle:Default:chain.html.twig', [
        'units' => $units
      ]);
    }

    public function singleAction($name)
    {
        $unit = $this->get('mongo')->wshell->units->findOne(['name' => $name]);

        $parsedown  = new Parsedown();
        $unit->info = $parsedown->text($unit->info);
        // shutdown
        $shutdown = ($unit->type == 0) ? true : false;

        // TODO: move to create unit
        $hookup = $unit->getHookupAsArray();
        $uim = new UIMapper($hookup);
        $unit->view = $uim->getView();
        // FINISH TODO

        return $this->render('UnitBundle:Default:single.html.twig', [
          'shutdown' => $shutdown,
          'unit'     => $unit
        ]);
    }

    public function runAction(Request $request, $name)
    {
      // TODO: add chain proccessing

      $unit = $this->get('mongo')->wshell->units->findOne(['name' => $name]);

      $client = $this->get('wamp.client');
      $client->on('open', function (ClientSession $session) use ($client, $unit) {
        // wshell.execute - для выполнения юнитов
        // wshell.system  - для передачи служебной информации (инфы о нодах, коннектах к бд)
          $sessionId = $client->session->getSessionId();
          $hookup = json_encode($unit->getHookupAsArray());

          // algorithm for non-type prossesing files
          foreach ($_FILES as $key => $f) {
              $content = file_get_contents($f['tmp_name']);

              // binary
              if (strpos($f['type'], 'octet-stream') !== false ) {

                  var_dump('binary');
                  die();

              // text. detect encode. "probes" uses for order-non-sencitive mb_detect_order
              } else {
                  mb_detect_order("UTF-8,CP1251,CP1252");
                  $probeBlock = strlen($content)/10;
                  $probes = [];
                  for ($i=0; $i < 10; $i++) {
                      $probe = mb_detect_encoding(substr($content, $i*$probeBlock, $probeBlock));
                      $probes[] = is_bool($probe) ? "undefined" : $probe;
                  }
                  $probesResults = array_count_values($probes);
                  $detect = array_keys($probesResults, max($probesResults))[0];

                  $content = mb_convert_encoding($content, "UTF-8", $detect);
                  // MAYBE TODO: check correct convert encode by letter statistic
              }
              $_POST[$key] = $content;
          }

          $message = [
            'units' => [
              $client->unit(['php'], $_POST, $unit->source, $unit->name, $hookup),
            ],
            'clientId' => (string)$sessionId
          ];

          $session->publish('wshell.execute.main', [json_encode($message)], [], ["acknowledge" => true])->then(
              function () {
                  $status = "Message publishing\n";
              },
              function ($error) {
                  $status = "Publish Error {$error}\n";
              }
          );

          $session->subscribe('wshell.execute.result', function ($args) {
            echo $args[1];
            die();
          });
      });
      $client->start();
    }
}
