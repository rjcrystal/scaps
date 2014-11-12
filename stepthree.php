<?php
$main=token_get_all(file_get_contents('test.php'));
$list=array();// it will containt all functions and superglobals with other details
$plines= array ();//will contain all the lines that are printed to avoid any duplicates
require_once 'rules.php';
require_once 'functions.php';
filterunwanted($main,$ign);//removes unwanted like comments, open close tags etc 
filtersuperglobals($main,$list,$taintable); 
tna($list,$main,$ctrlr); 
print("<pre>".print_r($list,true)."</pre>");
?>