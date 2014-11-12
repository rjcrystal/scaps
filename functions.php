<?php

function tna(&$list,$main,$ctrlr) //track where the data from user taintable variable goes 
{
	foreach($list as $key=>$ar2)
	{
		$ctrl=true;
		for($i=$ar2['key'];$ctrl;$i--)
		{
			$where=$main[$i];
			if ($where==='=')
			{
				$eqvarloc=$i-1;
				if ($main[$eqvarloc][0]=='T_VARIABLE')//checks to which variable the value from superglobal is given
				{
					array_push($list[$key],$main[$eqvarloc][1]);
				}
			}
			else if ($main[$i]===';')
			{
				break;
			}
			else 
			{
				continue;
			}
		}
	}
}

function sechk($data,$type,$te,$k="0@0")
{
	include 'rules.php';
	global $main,$list;
	if($type==='direct')
	{
		highlight($k,$te);
	}
	if ($type==='var')//for variable used in a query
	{
		//print_r($list);
		foreach($list as $key=>$val)
		{
			if(array_key_exists(0,$val))
			{
				if($data===$val[0] or in_array($data,$taintable))
				{
					highlight($k,$te);
				}
				else 
				{
					
				}
			}
		}
		if(in_array($data,$taintable))
		{
			highlight($k,$te);//echo "SQL injection found direct superglobal use <br>";
		}
		
	}
	else if ($type==='string')//for queries stored as string in a variable
	{
		foreach($list as $chk)
		{
			if(array_key_exists('query',$chk))
			{
				if($chk['qvar']===$data)
				{
					sechk($data,$list,$main,'var','sql',$k);
				}
			}
		}
	}
}
function highlight()
{
	$data= func_get_args();
	$str="";
	global $plines;
	if(count($data)===2)
	{
		$key=$data[0];
		$type=$data[1];
		global $main;
		$line=$main[$key][2];
		//echo "key $key "; 
		if (!in_array($key,$plines))//check if the line is already printed or not
		{
			for($i=$key;$main[$i]!==';';$i++)
			{
				if(is_array($main[$i]))
				{
					$str.=$main[$i][1];
				}
				else 
				{
					$str.=$main[$i];
				}
			}
			echo "
				<code>$type <hr>$str; line : $line </code>";
			array_push($plines,$key);
		}
		else 
		{
			return ;
		}
	}
	else 
	{
		return;
	}
}
function explorer($main,$i,&$temp,$list,$mode="udf")// explore function and its operations  
{
	include 'exploitconfig.php';
	include 'rules.php';
		$end=count($main);
		for($j=0;$j<$end;$j++)
		{
			if($main[$j][0]==='T_STRING')
			{
				$fname=$main[$j][1];
				if(in_array($fname,$sqli))//sql injection check
				{
					if ($main[$j+2][0]==='T_VARIABLE')//if the query is stored in a variable
					{
						sechk($main[$j+2][1],'string','sql injection',$j);
					}
					else if ($main[$j+3][0]==='T_ENCAPSED_AND_WHITESPACE') //for queries with variables 
					{
						for ($i=$j+3;$main[$i]!=='"';$i++)
						{
							if(is_array($main[$i]))
							{
								if ($main[$i][0]==='T_VARIABLE')
								{
									sechk($main[$i][1],'var','sql injection',$j);
								}
								
							}
						}
					}
				}
				else if (in_array($fname,$cmdexec)) //command execution check 
				{
					if($fname==='shell_exec' or $fname==='`')
					{
						 $ty="shell command";
					}
					else 
					{
						$ty="external program";
					}
					if($main[$j+3][0]==='T_ENCAPSED_AND_WHITESPACE')
					{
						for($i=$j;$main[$i]!==';';$i++)
						{
							//print_r($main[$i]);
							
							if($main[$i][0]==='T_VARIABLE')
							{
								if(in_array($main[$i][1],$taintable))
								{
									sechk($main[$i][1],'direct',"$ty execution",$j);
								}
								else 
								{
									sechk($main[$i][1],'var',"$ty execution",$j);	
								}
							}
						}
						
					}
					else if($main[$j+3][0]==='T_VARIABLE')
					{
						
					}
				}
				else 
				{
					
				}
			}
			else if (in_array($main[$j][0],$xss))//xss check 
			{
				for($i=$j;$main[$i]!==';';$i++)
				{
					
						if ($main[$i][0]==='T_VARIABLE')
						{
							sechk($main[$i][1],'var','Cross site Scripting',$j);
						}
				}
			}
			else if(in_array($main[$j][0],$lrfi))
			{
				if($main[$j+2][0]==='T_ENCAPSED_AND_WHITESPACE')
				{
					for($i=$j;$main[$i]!==';';$i++)
					{
						if ($main[$i][0]==='T_VARIABLE')
						{
							sechk($main[$i][1],'var','Lfi',$j);
						}
					}
				}
				else if ($main[$j+2][0]==='T_VARIABLE')
				{
					sechk($main[$j+2][1],'var','Lfi',$j);
				}
				
				
			}
		}
	
}

function findvarsudf($main,$i)
{
$var= array();
	for($j=$i;;$j++)
	{	
		if($main[$j]===')')//detect end of a function argument list
		{
			if ($j===$i+1)
			{
				return 'no variable';
			}
			else
			{
				break;
			}
		}
		if (is_array($main[$j]))
		{
			if($main[$j][0]==='T_VARIABLE')
			{
				array_push($var,$main[$j][1]);	
			}
		}
	}
	return $var;
}

function striptags($string, $remove_breaks = true) // remove all html tags from php code
{
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags($string);
	if ( $remove_breaks )
		$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	return trim( $string );
}
function filterunwanted(&$some,$ign) //removes unwanted stuff from token array and resolves token number to name 
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
						if ($search)//remove elements defined in $ign array
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
					$boo=striptags($boo);
				}
			}
		}
	}
	$some=array_values($some);
}
function filtersuperglobals($some,&$list,$taintable) //filter out super globals and user defined functions form code
{
	foreach($some as $skey =>&$val)
	{
		if (is_array($val))
		{
			foreach ($val as $key => &$boo)
			{
				if ($key===0)
				{
					if ($boo === $taintable[2])
					{
						$supkey=$some[$skey+1][1];
						$lno=$some[$skey+1][2];
						$udf=array ("name"=>$supkey,'type'=>'udf','key'=>$skey,'line'=>$lno);
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
						listvar($sw,$skey,$some,$list,$taintable);						
						continue;
					}
					else if ($some[$skey][0]==='T_VARIABLE' and $some[$skey+1]==='=' and $some[$skey+3][0]==='T_ENCAPSED_AND_WHITESPACE')
					{
						if (startsWith($some[$skey+3][1]))
						{
							$querystr =array('key'=>$skey,'variable'=>$some[$skey][1],'query'=>$some[$skey+3][1],'qvar'=>$some[$skey+4][1]);
							array_push($list,$querystr);
						}
					}
					
				}
			}
		
		}
	}	
}
function startsWith($needle)//checks if the string is a mysql query or not
{
	$haystack=array('select','create','insert','delete','update');
	$ans=explode(" ",$needle);
	return in_array($ans[0],$haystack);
}
function listvar($type,$skey,$main,&$list,$taintable)//finds the name of superglobals
{
	$ctrl=true;// for controlling the loop
	for ($i=$skey;$ctrl;++$i) // we'll check for ';' and end the loop at the end of php statement
		{
			if (is_array($main[$i]))
			{
				if ($main[$i][0]=='T_CONSTANT_ENCAPSED_STRING')
				{
					$varname=$main[$i][1];
					$lno=$main[$i][2];
					$filtered=strunquote($varname);
					if ($filtered==NULL) // ignore any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
						$tmparr=array ("name"=>$filtered,"type"=>$taintable[$type],"key"=>$i,"line"=>$lno);
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
function strunquote($str) //removes quotes used in superglobal names
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
?>