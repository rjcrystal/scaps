<?php

function tna(&$list,$main,$ctrlr,$funcstats) //track where the data from user taintable variable goes 
{
	foreach($list as $key=>$ar2)
	{
		/*
		if($ar2['type']==='udf') // understanding what current function will do
		{
			echo "$ar2[name]";
			$ctrl2=true;
			$flag=true;
			for ($i=$ar2['key'];$ctrl2;$i++)
			{
					if ($main[$i]==='(' and $flag)// finds all function arguments
					{
						$udfvarlist=array ();// will contain list of all the argument variables
						$buf= array ($ar2['name']=> findvarsudf($main,$i));
						array_push($udfvarlist,$buf);
						$flag=false;
					}
					if ($main[$i]==='{')
					{
						$funcstats=explorer($main,$i);
						break;
					}
			}
			
		}
		else
		{
			*/
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
function explorer($main,$i,$temp,$mode="udf")// explore function and its operations  
{
	include 'rules.php';
	if($mode==="udf")
	{
		for($j=$i;;$j++)
		{	
			//echo "in for loop";
			//echo "$j";
			//print("<pre>".print_r($main[$j],true)."</pre>");
			if($main[$j][0]==='T_STRING')
			{
				$fname=$main[$j][1];
				if(in_array($fname,$sqli))
				{
					echo "sqli functions found";
				}
				else if (in_array($fname,$xss))
				{
					echo "xss functions found";
				}
				else if (in_array($fname,$cmdexec))
				{
					echo "command execution functions found";
				}
				else if (in_array($fname,$clbkfunc))
				{
					echo "callback functions found";
				}
				else 
				{
					
				}
				
			}
			else if(in_array($main[$j][0],$lrfi))
			{
				echo "lfi rfi function found";
			}
			else if ($main[$j][1]==='}')
			{
				break;
			}
			else 
			{
				continue;
			}
		}
	}
	else
	{
		
		$end=count($main);
		for($j=0;$j<$end;$j++)
		{
			//print_r($main[$j]);
			//print("<pre>".print_r($main[$j],true)."</pre>");
			if($main[$j][0]==='T_STRING')
			{
				$fname=$main[$j][1];
				if(in_array($fname,$sqli))
				{
					
					if($main[$j+2][0]==='T_CONSTANT_ENCAPSED_STRING')
					{
						echo "query is".$main[$j+2][1]."<br>";
					}
					else if ($main[$j+2][0]==='T_VARIABLE')
					{
						echo "var is ".$main[$j+2][1]."<br>";
					}
					else if ($main[$j+3][0]==='T_ENCAPSED_AND_WHITESPACE') //for insert and delete queries
					{
						echo "dml query is ";
						for ($i=$j+3;$main[$i]!=='"';$i++)
						{
							if(is_array($main[$i]))
							{
								if ($main[$i][0]==='T_VARIABLE')
								{
									
								}
								echo $main[$i][1];
							}
							else 
							{
								echo $main[$i];
							}
						}
						echo "<br>";
					}
					else 
					{
						echo "$j <br>";
					}
				}
				else if (in_array($fname,$xss))
				{
					echo "xss functions found <br>";
				}
				else if (in_array($fname,$cmdexec))
				{
					echo "command execution functions found <br>";
				}
				else if (in_array($fname,$clbkfunc))
				{
					echo "callback functions found <br>";
				}
				else 
				{
					
				}
			}
			else if(in_array($main[$j][0],$lrfi))
			{
				echo "lfi rfi function found <br>";
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
					if ($boo === $taintable[3])
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
				}
			}
		
		}
	}	
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
function strunquote($str) //removes quotes used in superglobals
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