<?php

namespace UnitBundle\Document;

use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectID;
use Symfony\Component\Yaml\Yaml;

class Unit implements Persistable
{
    // fields
    public $_id;
    public $name;
    public $anno;
    public $source;
    public $access;
    public $type;
    public $guiMode;
    public $language;
    public $info;
    public $lastUpdate;
    public $createdAt;
    public $view;
    // fields with accessors
    protected $hookup;

    public function __construct($data)
    {
        $this->bsonUnserialize($data);
        $this->_id = new ObjectID();

        // time
        $msec = floor(microtime(true) * 1000);
        $this->lastUpdate = new UTCDateTime($msec);
        $this->createdAt = new UTCDateTime($msec);
    }

    public function bsonSerialize()
    {
      $data = [];
      foreach ($this as $key => $val) {
        $data[$key] = $val;
      }
      return $data;
    }

    public function bsonUnserialize(array $data)
    {
      foreach ($data as $key => $val) {
        if (property_exists($this, $key) && $val != null) {
          $this->$key = $val;
        }
      }
    }

    public function setHookup($hookup)
    {
      $this->hookup = serialize(Yaml::parse($hookup));
    }
    public function getHookup()
    {
      return Yaml::dump(unserialize($this->hookup), 4);
    }
    public function getHookupAsArray()
    {
      return unserialize($this->hookup);
    }
}
