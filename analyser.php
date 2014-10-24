<?php
$start_time = microtime(true);
$main=token_get_all(file_get_contents('test.php'));
require 'rules.php';
require 'functions.php';
filterunwanted($main,$ign);//removes unwanted like comments, open close tags etc 
//filtersuperglobals($main,$list,$taintable); // finds superglobal user input variables and user defined functions
print("<pre>".print_r($main,true)."</pre>");
//tna($list,$main,$ctrlr,$funcstats);
//print("<pre>".print_r($list,true)."</pre>");
$end_time = microtime(true);
echo 'time :  '.($end_time - $start_time)." sec\n <br>";
?>