<?php

namespace UserBundle\Document;

use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectID;
use Symfony\Component\Yaml\Yaml;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements Persistable, UserInterface, EquatableInterface
{
    // fields
    public $_id;
    public $username;
    public $password;
    public $salt;
    public $roles;

    public $providers;

    public $createdAt;
    public $lastLogin;

    public function __construct($data)
    {
        $this->bsonUnserialize($data);
        $this->_id = new ObjectID();

        $bytes = openssl_random_pseudo_bytes(8);
        $this->salt = bin2hex($bytes);

        // time
        $msec = floor(microtime(true) * 1000);
        $this->lastLogin = new UTCDateTime($msec);
        $this->createdAt = new UTCDateTime($msec);
    }

    public function __sleep()
    {
        $cleared = $this->bsonSerialize();
        foreach ($this as $key => $val) {
            $this->$key = $cleared[$key];
        }
        return array_keys($cleared);
    }

    public function bsonSerialize()
    {
      $data = [];
      foreach ($this as $key => $val) {
          $data[$key] = !is_object($val) ? $val :
          (method_exists($val, 'bsonSerialize') ? $val->bsonSerialize() : (string)$val);
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

    public function getRoles()
    {
        return is_object($this->roles) ? $this->roles->bsonSerialize() : $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }
        return true;
    }
}
