<?php

namespace AdminBundle\Controller;

use UnitBundle\Document\Unit;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class UnitController extends Controller
{
    public function indexAction(Request $request, $unitId)
    {
      if ($unitId) {
        // edit unit
        $unit = $this->get('mongo')->wshell->units->findOne([ '_id' => new ObjectID($unitId) ]);
      } else {
        // new unit
        $unit = [
          'name'=>null, 'anno'=>null, 'info'=>null, 'source'=>null, 'hookup'=> null,
          'type'=>2, 'guiMode'=>'request', 'access'=>'public', 'language'=>'php',
          '_id'=>null, 'lastUpdate'=> new \DateTime(), 'view' => '',
        ];
      }
      $units = $this->get('mongo')->wshell->units->find();

        return $this->render('AdminBundle:Unit:index.html.twig', [
          'active3' => true,
          'user' => ['name' => 'test'],
          'units' => $units,
          'unit' => $unit,
        ]);
    }

    public function postAction(Request $request)
    {
      $unitData = $request->request->all();
      $col = $this->get('mongo')->wshell->units;

      if ($unitData['_id']) {
        // edit
        $unit = $col->findOne([ '_id' => new ObjectID($unitData['_id']) ]);
        if (!$unit) {
          throw $this->createNotFoundException("NOT FOUND UNIT WITH ID " . $unitData['_id']);
        }

        // simple fields
        $unit->bsonUnserialize($unitData);
        // special fields
        $unit->_id = new ObjectID($unitData['_id']);
        $unit->setHookup($unitData['hookup']);
        $msec = floor(microtime(true) * 1000);
        $unit->lastUpdate = new UTCDateTime($msec);

        $result = $col->replaceOne([ '_id' => $unit->_id ], $unit);
        if ($result->getMatchedCount() == 1) {
          return $this->redirect($this->generateUrl('units', ['unitId' => $unitData['_id'] ]));
        }

      } else {
        // new
        try {
          $unitData['hookup'] = serialize(Yaml::parse($unitData['hookup']));
        } catch (ParseException $e) {
          var_dump($e->getMessage());
          die();
        }
        $unit = new Unit($unitData);
        $result = $col->insertOne($unit);
        if ($result->getInsertedCount() == 1) {
          return $this->redirect($this->generateUrl('units'));
        }
        var_dump($e->getMessage());
        die();
      }
    }
}
