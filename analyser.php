
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
<!--<div class="container-fluid">
<div class="row">
<div class="col-md-12">
<h1><p>Static code Analyser for php</p></h1>
<div class="col-md-4">
<form role="form" enctype="multipart/form-data"  action="<?php $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="InputFile">Upload file(s) for analysis</label>
    <input type="file" id="InputFile">
	<label for="InputFile">Max size 2 MiB</label>
  </div>
  <button type="submit" class="btn btn-info">Submit</button>
</form>
</div>
<div class="col-md-6 col-md-offset-0">
<label> Or Copy your php code here </label>
<form role="form" action="<?php $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
	<textarea class="form-control" rows="7" ></textarea>
  </div>
  <button type="submit" class="btn btn-info pull-right">Submit</button>
</form>
</div>
</div>
</div>
</div>
</body>
</html>-->