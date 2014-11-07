<?php
$start_time = microtime(true);
$main=token_get_all(file_get_contents('test.php'));
$list=array();
require_once 'rules.php';
require_once 'functions.php';
filterunwanted($main,$ign);//removes unwanted like comments, open close tags etc 
//print("<pre>".print_r($main,true)."</pre>");
filtersuperglobals($main,$list,$taintable); // finds superglobal user input variables and user defined functions
tna($list,$main,$ctrlr); // track which superglobal value goes where
explorer($main,0,$temp,$list,"else");
//print("<pre>".print_r($list,true)."</pre>");
$end_time = microtime(true);
echo 'time :  '.($end_time - $start_time)." sec\n <br>";
//exec('php -l test.php');// syntax checking (lint)
//highlight_file('test.php'); highlighting file using php syntax highlighting
?>