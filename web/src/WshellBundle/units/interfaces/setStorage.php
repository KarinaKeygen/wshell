<?php

class setStorage extends Storage {

//    Storage:
//    function __construct($mode, $unitName, $user)
//    
//    public $mode;
//    public $unitName;
//    public $user;
//    public $stor;
//    public $key;

    // add member
    public function sadd($key, array $members) {
        $this->stor->sadd($this->key . ':' . $key, $members);
    }

    // get data. return array
    public function sget($key, $count = False, $pop = False) {

        $name = $this->key . ':' . $key;

        if (!is_numeric($count)) {
            return $this->stor->smembers($name);
        } else {
            // ATTENTION! its load random values
            if ($pop) {
                // make in transaction!
                $members = array();
                for ($i = 1; $i <= $count; $i++) {
                    $members[] = $this->stor->spop($name);
                }
                return $members;
            } else {
                return $this->stor->srandmember($name, $count);
            }
        }
    }

    // sets operation
    public function sdiff(array $keys, $destination = False) {

        foreach ($keys as $key => $value) {
            $keys[$key] = $this->key . ':' . $value;
        }
        if ($destination) {
            return $this->stor->sdiffstore($this->key . ':' . $destination, $keys);
        } else {
            return $this->stor->sdiff($keys);
        }
    }

    public function sinter(array $keys, $destination = False) {

        foreach ($keys as $key => $value) {
            $keys[$key] = $this->key . ':' . $value;
        }
        if ($destination) {
            return $this->stor->sinterstore($this->key . ':' . $destination, $keys);
        } else {
            return $this->stor->sinter($keys);
        }
    }

    public function sunion(array $keys, $destination = False) {

        foreach ($keys as $key => $value) {
            $keys[$key] = $this->key . ':' . $value;
        }
        if ($destination) {
            return $this->stor->sunionstore($this->key . ':' . $destination, $keys);
        } else {
            return $this->stor->sunion($keys);
        }
    }

    //other
    // count members
    public function scard($key) {
        return $this->stor->scard($this->key . ':' . $key);
    }

    // exist member in set
    public function sismember($key, $member) {
        return $this->stor->sismember($this->key . ':' . $key, $member);
    }

    // move member from one set to other set
    public function smove($source, $destination, $member) {
        return $this->stor->smove($this->key . ':' . $source, $this->key . ':' . $destination, $member);
    }

    public function srem($key, array $members) {
        return $this->stor->srem($this->key . ':' . $key, $members);
    }
	
	// ???
	/*
	public function sscan() {

        return $this->stor->sscan();
    }
	*/

}