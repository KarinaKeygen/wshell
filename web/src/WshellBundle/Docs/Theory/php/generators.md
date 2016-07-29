Генератор - это функция, которая использует ключевоне слово yield вместо return,
сохраняет свое состояние и возвращает итератор.

### Экономия памяти

Генераторы позволяют существенно экономить память при работе с большими массивами данных,
когда не требуется хранить в памяти промежуточные результаты:

<?php
function file_lines($filename) {
    $file = fopen($filename, 'r');
    while (($line = fgets($file)) !== false) {
        yield $line;
    }
    fclose($file);
}

// Test 1
$m = memory_get_peak_usage();
foreach (file_lines('lipsum.txt') as $l);
echo memory_get_peak_usage() - $m, "n"; //Выдает 7336

// Test 2
$m = memory_get_peak_usage();
foreach (file('lipsum.txt') as $l);
echo memory_get_peak_usage() - $m, "n"; // Выдает 148112

Другой пример: range(0, 1000000) потребует over 100 Мб памяти.

Реализация range на генераторе потребует не более 1 Кб.

    function xrange($start, $limit, $step = 1) {
        if ($start < $limit) {
            if ($step <= 0) {
                throw new LogicException('Step must be +ve');
            }

            for ($i = $start; $i <= $limit; $i += $step) {
                yield $i;
            }
        } else {
            if ($step >= 0) {
                throw new LogicException('Step must be -ve');
            }

            for ($i = $start; $i >= $limit; $i += $step) {
                yield $i;
            }
        }
    }

### Передача данных в генератор

Yield может работать в обратную сторону:

<?php
function nums() {
    for ($i = 0; $i < 5; ++$i) {
        // get a value from the caller
        $cmd = (yield $i);
        if ($cmd == 'stop') {
            return; // exit the generator
        }
    }
}

$gen = nums();

foreach ($gen as $v) {
    // we are satisfied
    if ($v == 3) {
        $gen->send('stop');
    }
    echo "{$v}n";
}

### Утечки памяти

Если во внешнем цикле поставить break возможна утечка памяти.
Для её устранения рекомендуется использовать finally:

    function getLines($file) {
        $f = fopen($file, 'r');
        try {
            while ($line = fgets($f)) {
                yield $line;
            }
        } finally {
            fclose($f);
        }
    }

    foreach (getLines("file.txt") as $n => $line) {
        if ($n > 5) break;
        echo $line;
    }
