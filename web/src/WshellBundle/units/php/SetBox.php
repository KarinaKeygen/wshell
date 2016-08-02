<?php

use Wshell\Utils\Formatter as F;

class SetBox extends \Wshell\Unit
{

    public function reset($stor)
    {
        $stor->del([
            'Все заклинания', 'Вода', 'Воздух', 'Огонь', 'Земля',
            'базовые', 'продвинутые', 'игрок', 'враг'
        ]);
        $stor->sadd('книги', ['Все заклинания', 'Вода', 'Воздух', 'Огонь', 'Земля']);
        $stor->sadd('Все заклинания',
            ['Щит', 'Медлительность', 'Каменная кожа', 'Ускорение', 'Просмотр Воздуха', 'Благословение',
                'Лечение', 'Рассеивание', 'Защита от Воды', 'Жажда крови', 'Проклятие', 'Защита от Огня',
                'Волна смерти', 'Зыбучий песок', 'Разрушающий луч', 'Удача', 'Молния', 'Точность',
                'Ледяная Молния', 'Убрать препятствие', 'Слабость', 'Слепота', 'Стена огня']);
        $stor->sadd('Земля', ['Щит', 'Медлительность', 'Каменная кожа', 'Волна смерти', 'Зыбучий песок']);
        $stor->sadd('Воздух', ['Ускорение', 'Просмотр Воздуха', 'Разрушающий луч', 'Удача', 'Молния', 'Точность']);
        $stor->sadd('Вода', ['Благословение', 'Лечение', 'Рассеивание', 'Защита от Воды', 'Ледяная Молния', 'Убрать препятствие', 'Слабость']);
        $stor->sadd('Огонь', ['Жажда крови', 'Проклятие', 'Защита от Огня', 'Слепота', 'Стена огня']);
        $stor->sadd('базовые',
            ['Щит', 'Медлительность', 'Каменная кожа', 'Ускорение', 'Просмотр Воздуха', 'Благословение',
                'Лечение', 'Рассеивание', 'Защита от Воды', 'Жажда крови', 'Проклятие', 'Защита от Огня']);
        $stor->sadd('продвинутые',
            ['Волна смерти', 'Зыбучий песок', 'Разрушающий луч', 'Удача', 'Молния', 'Точность',
                'Ледяная Молния', 'Убрать препятствие', 'Слабость', 'Слепота', 'Стена огня']);
        $stor->sadd('игрок', ['Щит', 'Проклятие']);
        $stor->sadd('враг', ['Благословение', 'Рассеивание', 'Защита от Воды', 'Щит', 'Медлительность', 'Стена огня']);
    }

    public function uiOutput($data)
    {

        $checked = $this->check($data);

        $stor = new redisStorage('pub', 'setBox', $this->user);

        // reset
        if ($checked['reset']) {
            $this->reset($stor);
            F::alert('info', 'Все изменения отменены.');
        }

        // add/remove spell
        if ($checked['spell']) {
            if ($stor->sismember('Все заклинания', $checked['spell'])) {
                $stor->srem('Все заклинания', [$checked['spell']]);
                $stor->srem('игрок', [$checked['spell']]);
                $stor->srem('враг', [$checked['spell']]);
                F::alert('info', 'Заклинание "' . $checked['spell'] . '" было удалено.');
            } else {
                $stor->sadd('Все заклинания', [$checked['spell']]);
                F::alert('info', 'Появилось новое заклинание: "' . $checked['spell'] . '" !');
            }
        }

        // read book
        if ($checked['book']) {
            if ($stor->sismember('книги', $checked['book'])) {
                F::set('Заклинания в книге "' . $checked['book'] . '":', $stor->sget($checked['book']));
            } else {
                F::alert('warning', 'Книга "' . $checked['book'] . '" написана на неизвестном вам языке...');
            }
        }

        // count
        if ($checked['count']) {
            F::alert('info', 'Общее количество заклинаний: ' . $stor->scard('Все заклинания'));
        }

        // learn rand spell
        if ($checked['rand_spell']) {
            $new = $stor->sget('Все заклинания', 1)[0];
            if ($stor->sismember('игрок', $new)) {
                F::alert('danger', 'Вам не удалось выучить заклинание "' . $new . '". Вы его уже знаете.');
            } else {
                $stor->sadd('игрок', [$new]);
                F::alert('success', 'Вы выучили новое заклинание: "' . $new . '"!');
            }
        }

        // filch lucky
        if ($checked['filch_spell']) {
            if (rand(0, 1)) {
                $filch = $stor->sget('враг', 1, TRUE)[0];
                if ($filch) {
                    $stor->sadd('игрок', [$filch]);
                    F::alert('success', 'Вы украли заклинание: ' . $filch);
                } else {
                    F::alert('warning', 'Вы украли... А, нет, у противника нет заклинаний =)');
                }
            } else {
                $filch = $stor->sget('игрок', 1, TRUE)[0];
                if ($filch) {
                    $stor->sadd('враг', [$filch]);
                    F::alert('danger', 'У вас украли заклинание: ' . $filch);
                } else {
                    F::alert('warning', 'У вас украли... А, нет, у вас нечего красть =)');
                }
            }
        }

        // filch
        if ($checked['filch_spell2']) {
            $filch = $stor->smove('враг', 'игрок', $checked['filch_spell2']);
            if ($filch) {
                F::alert('success', 'Вы украли заклинание: ' . $checked['filch_spell2']);
            } else {
                F::alert('danger', 'У противника нет заклинания "' . $checked['filch_spell2'] . '".');
            }
        }

        // operation
        if ($checked['op']) {

            $one = $checked['one'];
            $mode = $checked['mode'];
            $two = $checked['two'];

            if ($mode == 'diff') {
                F::set("$one diff $two :", $stor->sdiff([$one, $two]));
            } elseif ($mode == 'inter') {
                F::set("$one inter $two :", $stor->sinter([$one, $two]));
            } else {
                F::set("$one union $two :", $stor->sunion([$one, $two]));
            }
        }

        F::set('Заклинания игрока:', $stor->sget('игрок'));
    }
}

