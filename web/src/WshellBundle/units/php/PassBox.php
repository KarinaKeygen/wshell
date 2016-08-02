<?php

use Wshell\Utils\Formatter as F;

class PassBox extends \Wshell\Unit {

    const ALG = MCRYPT_BLOWFISH;
    const MODE = MCRYPT_MODE_CBC;
    const ENTROPY = MCRYPT_DEV_URANDOM;

    private $dataAuth;
    private $passwords;
    private $masterPass;
    private $ivs;

    function __construct($user, $unitName, $storage, $hookup)
    {
        parent::__construct($user, $unitName, $storage, $hookup);
        $this->ivs = [];
    }

    function cript($passwords, $dataAuth, $masterPass)
    {
        $this->masterPass = $masterPass;
        $ivSize = mcrypt_get_iv_size(passBox::ALG, passBox::MODE);

        // login/pass context
        foreach ($dataAuth as $site => $dataSite) {
            $this->ivs[] = mcrypt_create_iv($ivSize, passBox::ENTROPY);
            $this->dataAuth[$site]["login"] = $this->symmetric($dataSite["login"], true);
            
            $this->ivs[] = mcrypt_create_iv($ivSize, passBox::ENTROPY);
            $this->dataAuth[$site]["pass"] = $this->symmetric($dataSite["pass"], true);
        }

        // common context
        foreach ($passwords as $password) {
            $this->ivs[] = mcrypt_create_iv($ivSize, passBox::ENTROPY);
            $this->passwords[] = $this->symmetric($password, true);
        }
    }

    function decript($masterPass)
    {
        $this->masterPass = $masterPass;

        foreach ($this->dataAuth as $site => $dataSite) {
            $login = $this->symmetric($dataSite["login"], false);
            $pass = $this->symmetric($dataSite["pass"], false);
            $secretWithContext[$site] = $this->linkGen($key, $login, $pass);
        }
        foreach ($this->passwords as $password) {
            $passwords[] = $this->symmetric($password, false);
        }
        return [$secretWithContext, $passwords];
    }

    function linkGen($syte, $login, $pass)
    {
    	// TODO: INSIDE STOR site:linkTemplate
    	$sites = ['mail' => "https://auth.mail.ru/cgi-bin/auth?Login=$login&Password=$pass&Domain=$domain"];
    	
    	foreach ($sites as $site => $authLink) {
	        if ($site == 'mail') {
	        	// crunch!! REMOVE
	            if (strpos($login, '@') !== False) {
	                list($login, $domain) = explode('@', $login);
	            } else {
	                $domain = 'mail.ru';
	            }
	            $authLink = "https://auth.mail.ru/cgi-bin/auth?Login=$login&Password=$pass&Domain=$domain";
	            return "<a style=\"cursor: pointer;\" onclick=\"return openEx('$authLink');\">$syte</a>";
        	}
    	}


        return NULL;
    }

    function symmetric($data, $encode)
    {
        if ($encode) {
            $raw = mcrypt_encrypt($this::ALG, md5($this->masterPass), $data, $this::MODE, end($this->ivs));
            $data = base64_encode($raw);
        } else {
            $raw = mcrypt_decrypt($this::ALG, md5($this->masterPass), base64_decode($data), $this::MODE, array_shift($this->ivs));
            // trim block-size appendix
            $data = rtrim($raw, "\0");
        }
        return $data;
    }

    public function uiOutput($data)
    {
        // $this->check($data, ARRAY);
        // $this->check($data, OBJECT);
		// $this->check($data, EXTRACT);
        $checked = $this->check($data);

        if ($checked['mode'] == 'save') {
            $passwords = $checked['passwords'];
            $dataAuth = $checked['dataAuth'];
            $masterPass = $checked['masterPass'];
            $this->cript($passwords, $dataAuth, $masterPass);
            if ($this->save( ['d' => $this->dataAuth, 'p' => $this->passwords, 'i' => $this->ivs] )) {
                F::alert('success', 'Данные успешно сохранены.');
            }
            echo '<h3>Зашифрованные данные <small>(вывод в base64)</small></h3>';
            F::printer('С контекстом:', $this->dataAuth);
            F::printer('Без контекста:', $this->passwords);
        }
        if ($checked['mode'] == 'load') {
            $masterPass = $checked['masterPass'];
            $loadData = $this->load(['d', 'p', 'i']);
            $this->dataAuth = $loadData['d'];
            $this->passwords = $loadData['p'];
            $this->ivs = $loadData['i'];
            list($links, $passwords) = $this->decript($masterPass);
            echo '<h3>Дешифрованные данные</h3>';
            F::printer('С контекстом:', $links);
            F::printer('Без контекста:', $passwords);
        }
    }

    public function output($data)
    {
        list($masterPass, $dataAuth, $passwords, $save) = $data;
        $this->cript($dataAuth, $passwords, $masterPass);
        return $this->passwords;
    }
}