<?php


/*
function GetListFiles($folder, $all_files, $levels) {
    $fp = opendir($folder);
    while ($cv_file = readdir($fp)) {
        if (is_file($folder . "/" . $cv_file)) {
            $all_files[$cv_file] = 'info_file';
        } elseif ($cv_file != "." && $cv_file != ".." && is_dir($folder . "/" . $cv_file)) {
            if ($levels > 0)
                $all_files[$cv_file] = GetListFiles($folder . "/" . $cv_file, $all_files[$cv_file], $levels - 1);
            else
                $all_files[$cv_file] = 'DIR_LAST_LEVEL';
        }
    }
    closedir($fp);
    return $all_files;
}

function honeyArray($output, $all_files) {
    $output.= '<ul>';
    foreach ($all_files as $name => $value) {
        if (is_array($value))
            $output.= '<li>' . $name . '' . honeyArray('', $value) . '</li>';
        else
        if ($value == 'DIR_LAST_LEVEL')
            $output.= '<li class="last_dir">' . $name . '</li>';
        else
            $output.= "<li>$name</li>";
    }
    $output.= '</ul>';
    return $output;
}

function honeyWikiArray($output, $all_files, $pre_item) {
    foreach ($all_files as $name => $value) {
        if (is_array($value)) {
            $output.= $pre_item . $name . "<br>";
            $output.= honeyWikiArray('', $value, $pre_item . '*');
        } else
        if ($value == 'DIR_LAST_LEVEL')
            $output.= $pre_item . $name . '/<br>';
        else
            $output.= $pre_item . $name . "<br>";
    }
    $output.= '</ul>';
    return $output;
}*/