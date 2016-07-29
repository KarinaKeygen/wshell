<?php

echo <<< EOL
<form method="post">
<input name="test[0]" value="1">
<input name="test[1]" value="2">
<input name="test[1][qwe]" value="2">
<button type="submit">Submit</button>
</form>
EOL;

// flat list names
var_dump(recursiveKeys($_POST));
// struct array
var_dump($_POST);


function recursiveKeys($input)
{
    $output = array_keys($input);
    foreach($input as $sub) {
        if (is_array($sub)) {
            $output = array_merge($output, recursiveKeys($sub));
        }
    }
    return $output;
}
