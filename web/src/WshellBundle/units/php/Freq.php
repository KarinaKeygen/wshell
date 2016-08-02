<?php

use Wshell\Utils\Formatter as F;

class Freq extends \Wshell\Unit
{
    private $wordDelimiters = " \t\n\r\0\x0B";

    public function uiOutput($data)
    {
        $checked = $this->check($data);
        $dic = [];
        $escaped = true;

        if ($checked['mode'] == 'words') {

            // get word array
            $words = [];
            $word = strtok($checked['text'], $this->wordDelimiters);
            while ($word !== false) {
                $words[] = $word;
                $word = strtok($this->wordDelimiters);
            }
            $words = array_map(function($word) use ($checked) {
                return mb_strtolower($word, 'utf8');
            }, $words);

            // clear
            if(isset($checked['clear'])) {
                $words  = array_map(function($word) use ($checked) {
                    return trim($word, $checked['clearPattern']);
                }, $words);
            }

            // filtering
            $countedWords = array_count_values($words);
            arsort($countedWords);
            foreach($countedWords as $word => $count) {
                if ($count >= $checked['minCountWord'] &&
                    mb_strlen($word, 'utf8') >= $checked['minLengthWord']) {
                    $dic[$word] = $count;
                }
            }
        }elseif ($checked['mode'] == 'letters') {

            $l = mb_strlen($checked['text'], 'UTF-8');
            for($i = 0; $i < $l; $i++) {
                $char = mb_substr($checked['text'], $i, 1, 'UTF-8');
                if(!array_key_exists($char, $dic))
                    $dic[$char] = 0;
                $dic[$char]++;
            }
            arsort($dic);

            if($checked['percent']){
                foreach($dic as $word => $count) {
                    $percent = ($count / mb_strlen($checked['text'], 'utf8')) * 100;
                    $dic[$word] = [$count, $percent];
                }
            }
            $escaped = false;
        }
        F::printer('Result: ', $dic, $escaped);
    }
}
