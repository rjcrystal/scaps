<?php
$start_time = microtime(true);
$main=token_get_all(file_get_contents('test.php'));
require 'rules.php';
require 'functions.php';
filterunwanted($main,$ign);
analyse($main,$list,$taintable);
echo '<pre>
Array
(
    [0] => Array
        (
            [0] => variable or function name
            [1] => type of variable or function
            [2] => key for main array
            [3] => line number
	    [4] => line start key
        )

</pre>';
echo count($list);
//print("<pre>".print_r($main,true)."</pre>");
tna($list,$main);
print("<pre>".print_r($list,true)."</pre>");
$end_time = microtime(true);
echo 'time :  '.($end_time - $start_time)." sec\n <br>";
?>