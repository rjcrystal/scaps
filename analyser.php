
<?php
$start_time = microtime(true);
$some=token_get_all(file_get_contents('wp-login.php'));
require 'rules.php';
require 'functions.php';
filterunwanted($some,$ign);
analyse($some,$list);
echo count($list);
//print("<pre>".print_r($some,true)."</pre>");
print("<pre>".print_r($list,true)."</pre>");
//array_walk($list,'tna',$some);
/* 
Array
(
    [0] => Array
        (
            [0] => <variable or function name>
            [1] => <type of variable or function>
            [2] => <key for main array >
            [3] => <line number>
        )

*/
$end_time = microtime(true);
echo 'time :  '.($end_time - $start_time)." sec\n <br>";
?>