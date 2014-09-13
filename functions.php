<?php
function tna(&$list,$main) //track where the data from user taintable variable goes 
{
	foreach($list as $key=>$ar2)
	{
		$lno=$ar2[3];
		$ctrl=true;
		for($i=$ar2[2];$ctrl;$i--)
		{
			if ($main[$i]==='=')
			{
				echo 'equal found';
				$eqvarloc=$i-3;
				if ($main[$eqvarloc]=='T_VARIABLE')
				{
					array_push($list[$i],$main[$quvarloc][1]);
				}
			}
			else if ($main[$i]===';')
			{
				break;
			}
			else if ($main[$i][0]==)
			else 
			{
				continue;
			}
			
			
		}
	}
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