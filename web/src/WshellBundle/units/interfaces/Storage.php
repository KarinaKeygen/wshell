<?php
// THIS UNIT REGLAMENTED PLACE&KEY FOR STORAGE DATA

/*
 * Mode -> key/storage:
 * env -> UnitName:UserId/1
 * pub -> UnitId/2
 * per -> UserId/3
 * ano -> generate/4
 * 
 * for visitors and not pub not ano - 5
 */

class Storage {

    public $mode;
    public $unitName;
    public $user;
    public $stor;
    public $key;

    function __construct($mode, $unitName, $user) {
        $this->mode = $mode;
        $this->unitName = $unitName;
        $this->user = $user;

        // select key and storage-place. 
        $role = ($user->role === 'visitor') ? 1 : 2;
        
        switch ($mode . $role) {
            case 'env1':
                $this->stor = $GLOBALS['wshell']->predis(5);
                $this->key = $unitName . ':' . $user->id;
                break;
            case 'env2':
                $this->stor = $GLOBALS['wshell']->predis(1);
                $this->key = $unitName . ':' . $user->id;
                break;
            case ('pub1'):
                $this->stor = $GLOBALS['wshell']->predis(2);
                $this->key = $unitName;
                break;
            case ('pub2'):
                $this->stor = $GLOBALS['wshell']->predis(2);
                $this->key = $unitName;
                break;
            case 'per1':
                $this->stor = $GLOBALS['wshell']->predis(5);
                $this->key = $user->id;
                break;
            case 'per2':
                $this->stor = $GLOBALS['wshell']->predis(3);
                $this->key = $user->id;
                break;
            default: // ano
                $this->stor = $GLOBALS['wshell']->predis(4);
		$this->key = uniqid(mt_rand());
                break;
        }
    }

    final public function save($value, $key) {
        
            $name = $this->key . ':' . $key;
            $this->stor->set($name, $value);
    }

    final public function load($key) {
        $name = $this->key . ':' . $key;
		
        if ($this->stor->exists($name)) {
            return $this->stor->get($name);
        } else {
            throw new Exception('Нет данных для загрузки.');
        }
    }
    
    public function execute($command, $args = []) {
        $this->stor->$command($args);
    }

}