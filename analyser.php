<!DOCTYPE html>
<html lang="en">
<head>
<title>analyser results</title>
<meta charset="UTF-8">
<link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" charset="utf-8" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		  </button>
		  <a class="navbar-brand" href="/scap">SCAP</a>
		</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
	    <li><a href="ranking.php">Rank List</a></li>
        <li><a href="tips.php">Tips</a></li>
		<li><a href="myanalysis.php">My analysis </a></li>
		<li class="active"><a href="analyser.php">Analyze </a></li>
		</ul>
   <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<div class="navbar-right buffer">
			<div class="btn-group">
				<a href="login.php" class="btn btn-default">login</a>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="dash.php">Dashboard</a></li>
					<li class="divider"></li>
					<li><a href="logout.php">logout</a></li>
				</ul>
			</div>
		</div>
    </div>
 </div>
</nav>
<?php
$start_time = microtime(true);
$some=token_get_all(file_get_contents('wp-login.php'));
$ign = array
(
	'T_BAD_CHARACTER',
	'T_DOC_COMMENT',
	'T_COMMENT',
	'T_WHITESPACE',
	'T_OPEN_TAG',
	'T_CLOSE_TAG'
);

$incl= array(
'T_INCLUDE',
'T_INCLUDE_ONCE',
'T_REQUIRE',
'T_REQUIRE_ONCE' 
);
$exptbl= array (
'mysql_query',
'mysqli_query',
'echo',
'print',
'eval',
'popen',
'assert',
'include',
'include_once',
'require',
'require_once'
);
$list = array ();
$warnfunc = array();
filterunwanted($some,$ign);
echo count($some);
analyse($some,$list);
//print("<pre>".print_r($some,true)."</pre>");
print("<pre>".print_r($list,true)."</pre>");
//array_walk($list,'tna',$some);
function tna(&$list,$key,$main)
{
	echo "<br>";
	print_r($list);
}
function analyse(&$some,&$list)
{
$taintable=array 
(
	'$_POST',
	'$_GET',
	'$_SESSION'
);

	foreach($some as $skey =>&$val)
	{
		if (is_array($val))
		{
			foreach ($val as $key => &$boo)
			{
				if ($key===0)
				{
					if ($boo ==='T_FUNCTION')
					{
					$supkey=$some[$skey+1][1];
					$lno=$some[$skey+1][2];
					$udf=array ($supkey,'udf',$lno);
					array_push($list,$udf);
					}
					continue;
				}
				else if ($key === 1)
				{
					$search=array_keys($taintable,$val[$key]);
					if (!empty($search))
					{
						$sw=$search[0];
						switch ($sw)
						{
						case 0:
							listvar(1,$skey,$some,$list);
							break;
						case 1:
							listvar(2,$skey,$some,$list);
							break;
						case 2:
							listvar(3,$skey,$some,$list);
							break;
						}
					continue;
					}
				}
				
			}
		
		}
	}	
}
function strip_all_tags($string, $remove_breaks = true) // remove all html tags
{
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags($string);
	if ( $remove_breaks )
		$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	return trim( $string );
}
function listvar($type,$skey,$main,&$list)
{
	$ctrl=true;// for controlling the loop
	if ($type==1)
	{
		
		for ($i=$skey;$ctrl;++$i) // we'll check for ';' and end the loop at the end of php statement
		{
			if (is_array($main[$i]))
			{
				if ($main[$i][0]=='T_CONSTANT_ENCAPSED_STRING')
				{
					$varname=$main[$i][1];
					$filtered=strunquote($varname);
					if ($filtered==NULL) // remove any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
						
						$tmparr=array ($filtered,'post',$i);
						array_push($list,$tmparr);
						$ctrl=false;
						continue;
					}
				}
			}
			else 
			{
				if ($main[$i]==';')
				{
					$ctrl=false;
				}
			}
		}
	}
	else if ($type ==2)
	{
		for ($i=$skey;$ctrl;++$i) // we'll check for ';' and end the loop at the end of php statement
		{
			if (is_array($main[$i]))
			{
				if ($main[$i][0]=='T_CONSTANT_ENCAPSED_STRING')
				{
					$varname=$main[$i][1];
					$filtered=strunquote($varname);
					if ($filtered ==NULL) // remove any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
						$tmparr=array ($filtered,'get',$i);
						array_push($list,$tmparr);
						$ctrl=false;
						continue;
					}
				}
			}
			else 
			{
				if ($main[$i]==';')
				{
					$ctrl=false;
				}
			}
		}
	
	}
	else if ($type == 3)
	{
		for ($i=$skey;$ctrl;++$i) // we'll check for ';' and end the loop at the end of php statement
		{
			if (is_array($main[$i]))
			{
				if ($main[$i][0]=='T_CONSTANT_ENCAPSED_STRING')
				{
					$varname=$main[$i][1];
					$filtered=strunquote($varname);
					if ($filtered == null) // remove any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
					$tmparr=array ($filtered,'session',$i);
					array_push($list,$tmparr);
					$ctrl=false;
					continue;
					}
					
				}
			}
			else 
			{
				if ($main[$i]==';')
				{
					$ctrl=false;
				}
			}
		}
	}
	
	else 
	{
		return 0;
	}
}
function strunquote($str)
{
	if (strlen($str)>2)
	{
		$len=strlen($str);
		$len-=$len;
		return substr($str,1,$len-1);
	}
	else 
	{
		return NULL;
	}
}
function filterunwanted (&$some,$ign)
{
	foreach($some as $supkey=> &$val)
	{
		if (is_array($val))
		{
			foreach ($val as $key => &$boo)
			{
				if ($key==0)
				{
					$str=token_name((int)$boo);
					$search=array_search($str,$ign,true);
						if ($search)
						{
							unset ($some[$supkey]);
							break;
						}
						else 
						{
							$boo=$str;
						}
					
				}
				else if ($key==1) 
				{
					$boo=strip_all_tags($boo);
				}
			}
		}
		
	}
	$some=array_values($some);
}
 function multi_unique($array) 
{
        foreach ($array as $k=>$na)
            $new[$k] = serialize($na);
        $uniq = array_unique($new);
        foreach($uniq as $k=>$ser)
            $new1[$k] = unserialize($ser);
        return ($new1);
}
function jasu ($input)
{
	$input = array_map("unserialize", array_unique(array_map("serialize", $input)));
	return $input;
}
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
</div>-->
</body>
</html>