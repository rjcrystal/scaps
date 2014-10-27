<?php
$start_time = microtime(true);
$start_time = microtime(true);
$main=token_get_all(file_get_contents('test.php'));
require 'rules.php';
require 'functions.php';
filterunwanted($main,$ign);//removes unwanted like comments, open close tags etc 
//print("<pre>".print_r($main,true)."</pre>");
filtersuperglobals($main,$list,$taintable); // finds superglobal user input variables and user defined functions
//tna($list,$main,$ctrlr,$funcstats); // track which superglobal value goes where and function defination contains what
explorer($main,0,"else");
//print("<pre>".print_r($list,true)."</pre>");
$end_time = microtime(true);
echo 'time :  '.($end_time - $start_time)." sec\n <br>";
//exec('php -l test.php'); syntax checking (lint)
//highlight_file('test.php'); highlighting file using php syntax highlighting
?>