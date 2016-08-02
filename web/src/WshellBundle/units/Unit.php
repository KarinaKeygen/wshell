<?php

class Unit {

    protected $user;
    protected $hookup;

    function __construct($user, $unitName) {
        $this->user = $user;
        $unitHookup = 'hookup' . $unitName;
        $this->hookup = unserialize($this->user->$unitHookup);
        // autoload service units
        spl_autoload_register(array($this, 'autoload'), true);
    }

    //----------------------------build-in methods-------------------------------//
    protected final function autoload($className) {
        $classFile = __DIR__ . "/interfaces/$className.php";
        if (is_file($classFile)) {
            include($classFile);
            return True;
        }
        return False;
    }

    // check non-struct. dP = data, hP = meta info from hookup
    protected final function paramCheck(&$result, $dP, $hP) {
        if (isset($hP['demension'])) {
            // WARNING: demension accept as boolean (exist/non exist)
            foreach ($dP as $key => $val) {
                if (is_array($val)) {
                    $result[$key] = [];
                    paramCheck($result[$key], $val, $hP);
                } else {
                    if (preg_match($hP['elem'], $val)) {
                        $result[$key] = $val;
                    } else {
                        $result[$key] = $hP['norm'];
                    }
                }
            }
            return TRUE;
        } else {
            if (preg_match($hP['elem'], $dP)) {
                $result = $dP;
            } else {
                $result = $hP['norm'];
            }
            return TRUE;
        }
    }

    protected final function check($data) {
        
        
        // convert to hookup-like data (hData). Check names
        foreach($data as $name => $value){
            if( preg_match('#[^a-zA-Z0-9:]+#', $name) ){
                    throw new Exception('incorrect symbols in form name');
            } else {
                    $eval = '$hData[\''. str_replace(':', '\'][\'', $name) .'\'] = $value;'."\n";
                    eval($eval);
            }
        }
        // cut params non used in mode
        if(isset($this->hookup['mode'])) {
            if(isset($this->hookup['mode'][$hData['mode']])) {
                $checked = ['mode' => $hData['mode']];
                $paramsMode = $this->hookup['mode'][$hData['mode']];
                foreach ($this->hookup['input'] as $key => $value) {
                    if(!in_array($key, $paramsMode)){
                        unset($this->hookup['input'][$key]);
                    }
                }    
            } else {
                throw new Exception('incorrect mode: '.$hData['mode']);
            }
        }
        
        // check values
        foreach ($this->hookup['input'] as $key => $value) {
            // okay, struct
            if(isset($value['map'])) {

                // array of struct. demension as BOOLEAN!
                if(isset($value['demension'])){
                    
                    foreach ($hData[$key] as $tes => $item) {

                        array_walk_recursive($item, function(&$v, $k, $map) {
                            
                            $structNodeHookup = self::findRecursive($map, $k);
                            $result = NULL;
                            $this->paramCheck($result, $v, $structNodeHookup);
                            $v = $result;
                            
                        }, $value['map']);
                        
                        $hData[$key][$tes] = $item;
                        
                    }
                    
                }else{
                    
                    array_walk_recursive($item, function(&$v, $k, $map) {
                            
                            $structNodeHookup = self::findRecursive($map, $k);
                            $result = NULL;
                            $this->paramCheck($result, $v, $structNodeHookup);
                            $v = $result;
                            
                        }, $value['map']);
                        
                    $hData[$key] = $item;
                }
                $checked[$key] = $hData[$key];
                
            // non-struct
            }else{
                $result = NULL;
                $this->paramCheck($result, $hData[$key], $value);
                $checked[$key] = $result;
            }
        }
        return $checked;
    }
    
    static final function findRecursive(&$array, $needle) {
        
        if(is_array($array)){
            foreach ($array as $key => &$value) {
                if($key == $needle){
                    return $array[$key];
                }
                if (is_array($value)) {
                    self::findRecursive($value, $needle);
                }
            }
        }
    }
    
    protected final function save($data, $mode = 'env') {

        $box = new Storage($mode, get_class($this), $this->user);
        
        foreach ($data as $key => $value) {
            $box->save(serialize($value), $key);
        }
        return True;
    }

    protected final function load($keys, $mode = 'env') {
        
        $box = new Storage($mode, get_class($this), $this->user);
        
        foreach ($keys as $key) {
            $data[$key] = unserialize($box->load($key));
        }
        return $data;
    }

    //----------------------------client methods-------------------------------//
    
    // GUI mode
    protected function vOutput($data) {
        
    }

    // in chain
    protected function output($data) {
        
    }
    
    // embed
    protected function vEmbed($data) {
        
    }

}