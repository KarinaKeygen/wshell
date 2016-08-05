<?php

namespace UnitBundle\Tool;

// TODO: dimension and map
class UIMapper
{
    private $input;
    private $modes;
    private $signatures = [
        'email'    => '#^.+\@.+\..+$#',
        'color'    => '#^\#[0-9]{6}$#',
        'time'     => '#^[0-9]{2}\/[0-9]{2}\/\-?[0-9]{4} [0-9]{2}:[0-9]{2} (AM|PM)$#',
        'big_text' => '#^(.*)$#',
        'number'   => '#^-?[0-9]+(\.[0-9]+)?$#',
    ];
    private $templates = [
        // args
        'email'    => '<div class="form-group %MODE%"><div class="input-group"><div class="input-group-addon">@</div><input type="text" class="form-control" placeholder="%NAME%" name="%NAME%" value="%VALUE%"></div></div>',
        'color'    => '<div class="form-group %MODE%"><div class="input-group"><span class="input-group-addon"><span>%NAME%</span></span><input type="color" class="form-control" value="%VALUE%" name="%NAME%"/></div></div>',
        'time'     => '<div class="form-group %MODE%"><div class="input-group date datetimepicker"><input type="text" class="form-control" placeholder="%NAME%" name="%NAME%" value="%VALUE%"/><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></div>',
        'big_text' => '<div class="form-group %MODE%"><textarea class="form-control" placeholder="%NAME%" name="%NAME%" rows="8">%VALUE%</textarea></div>',
        'number'   => '<div class="form-group %MODE%"><div class="row"><div class="col-xs-3"><input type="text" class="form-control" placeholder="%NAME%" name="%NAME%" value="%VALUE%"></div></div></div>',

        'text'     => '<div class="form-group %MODE%"><input type="text" class="form-control" placeholder="%NAME%" name="%NAME%" value="%VALUE%"></div>',
        'boolean'  => '<div class="form-group %MODE%"><input type="checkbox" %VALUE% name="%NAME%" data-toggle="toggle" data-onstyle="default" data-on="%NAME% ON" data-off="%NAME% OFF"></div>',
        'select'   => '',

        // special
        'mode'     => '<label class="btn btn-default mode_toogle %ACTIVE%"><input type="radio" name="%NAME%" %CHECKED%/>%VALUE%</label>'
    ];

    function __construct($hookup)
    {
        $this->input = $hookup['input'];
        if (isset($hookup['mode'])) {
            $this->modes = $hookup['mode'];
        }

        // replace hookup info to view
        foreach ($this->input as $name => $value) {
            if (is_null($value)) {
                $this->input[$name] = $this->replace($this->templates['text'], ['NAME' => $name, 'VALUE' => '']);
            } else {
                if ($value === true || $value === false) {
                    $val = ($value) ? 'checked':'';
                    $this->input[$name] = $this->replace($this->templates['boolean'], ['NAME' => $name, 'VALUE' => $val]);
                } else {
                    if (isset($value['elem'])) {
                        $finded = false;
                        foreach ($this->signatures as $signature => $regex) {
                            if ($value['elem'] == $regex) {
                                $this->input[$name] = $this->replace($this->templates[$signature], ['NAME' => $name]);
                                $finded = true;
                            }
                        }
                        if (!$finded) {
                            $this->input[$name] = $this->replace($this->templates['text'], ['NAME' => $name]);
                        }

                        $val = isset($value['norm']) ? $value['norm'] : '';
                        $this->input[$name] = $this->replace($this->input[$name], ['VALUE' => $val]);
                    } else {
                        // it is map!
                        // TODO
                    }
                }
            }

            // set mode classes
            $defaultMode = true;
            $classes = '';
            foreach ($this->modes as $mode => $modeArgs) {
                foreach ($modeArgs as $arg) {
                    if ($arg == $name) {
                        $classes .= ' mode_' . $mode;
                    }
                }
                if ($defaultMode) {
                    $defaultMode = false;
                    if (!in_array($name, $modeArgs)) {
                        // $classes .= ' hidden';
                    }
                }
            }
            $this->input[$name] = $this->replace($this->input[$name], ['MODE' => 'input' . $classes]);
        }
    }

    private function replace($template, $replacement)
    {
        foreach ($replacement as $name => $value) {
            $template = str_replace("%$name%", $value, $template);
        }
        return $template;
    }

    private function getModeView()
    {
        $view = '<div class="btn-group" data-toggle="buttons">';
        $i = 0;
        foreach ($this->modes as $name => $args) {
            $view .= $this->replace($this->templates['mode'], [
                'ACTIVE' => ($i == 0) ? 'active':'',
                'NAME' => $name,
                'CHECKED' => ($i == 0) ? 'checked':'',
                'VALUE' => $name
            ]);
            $i++;
        }
        return $view . '</div> <span></span>';
    }

    public function getView()
    {
        $view = '<form><div class="form-group">';
        if ($this->modes) {
            $view .= $this->getModeView();
        }
        $view .= '<button type="submit" class="btn btn-default">Run</button></div>';
        foreach ($this->input as $key => $value) {
            $view .= $value;
        }
        return $view . '</form>';
    }
}
