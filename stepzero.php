<?php
$main=token_get_all(file_get_contents('test.php'));
print("<pre>".print_r($main,true)."</pre>");
?>