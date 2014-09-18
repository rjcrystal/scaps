<?php
function tna(&$list,$main,$ctrlr,$funcstats) //track where the data from user taintable variable goes 
{
	foreach($list as $key=>$ar2)
	{	
		if($ar2[1]==='udf') // understanding what current function will do
		{
			//echo "$ar2[0]";
			$ctrl2=true;
			$flag=true;
			for ($i=$ar2[2];$ctrl2;$i++)
			{
				if ($main[$i]==='(' and $flag)// finds all function arguments
				{
					$udfvarlist=array ();// will contain list of all the argument variables 
					$buf= array ($ar2[0]=> findvarsudf($main,$i));
					array_push($udfvarlist,$buf);
					print("<pre>".print_r($buf,true)."</pre>");
					$flag=false;
				}
				if (is_array($main[$i]))// finds all the stuff used in function defination
				{
					$where=$main[$i];
					//print("<pre>".print_r($where,true)."</pre>");
				}
				else if ($main[$i]==='}')
				{
					break;
				}
			}
			
		}
		else
		{
			$ctrl=true;
			for($i=$ar2[2];$ctrl;$i--)
			{
				$where=$main[$i];
			//	print("<pre>".print_r($where,true)."</pre>");
				if ($where==='=')
				{
					$eqvarloc=$i-1;
					if ($main[$eqvarloc][0]=='T_VARIABLE')//checks to which variable the value from superglobal is given
					{
						/*echo "var found";
						print_r($main[$eqvarloc]);
						echo "<br>";
						*/
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
}
function findvarsudf($main,$i)
{
$ar= array();
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
				array_push($ar,$main[$j][1]);	
			}
		}
	}
	return $ar;
}
function analyse(&$some,&$list,$taintable) //filter out super globals and user defined functions form code
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
					$udf=array ($supkey,'udf',$skey,$lno);
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
function strip_all_tags($string, $remove_breaks = true) // remove all html tags from php code
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
					$lno=$main[$i][2];
					$filtered=strunquote($varname);
					if ($filtered==NULL) // ignore any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
						
						$tmparr=array ($filtered,'post',$i,$lno);
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
					$lno=$main[$i][2];
					if ($filtered ==NULL) // ignore any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
						$tmparr=array ($filtered,'get',$i,$lno);
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
					$lno=$main[$i][2];
					if ($filtered == null) // ignore any empty constant encapsed strings 
					{
						continue; 
					}
					else 
					{
					$tmparr=array ($filtered,'session',$i,$lno);
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
function filterunwanted (&$some,$ign) //removes unwanted stuff from token array and resolves token number to name 
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
?>