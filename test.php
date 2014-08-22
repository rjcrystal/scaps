<?php
//add redirect to tab when some database action is completed
session_start();
function redirect()
{
	header("location: admin.php");
}
include 'conn.php';
	if (isset($_SESSION['uname']))
	{
		$unchk=$_SESSION['uname'];
		$incl=mysql_fetch_array(mysql_query("select name from admin_master"));
		if (in_array($unchk,$incl,true))
		{
			
		}
		else 
		{
			header("location: login.php");
		}
		
	}
	else 
	{
		header("location: login.php");
	}
$un=$_SESSION['uname'];
if (isset ($_POST['us']) and isset($_POST["js"]))
{
	$usaj=$_POST['us'];
	$jsaj=$_POST['js'];
	$qu=mysql_query("insert into junction_user_detail values ($jsaj,$usaj)");
	if(!$qu)
	{
		echo "<script>alert ('error') </script>";
	}
	unset ($_POST['js']);
	$_GET['checking'];
	$_GET['checking2'];
	unset ($_POST['us']);
	redirect();
}
if (isset($_POST['junc']) and isset($_POST['usr']))
{
	$djnc=$_POST['junc'];
	$jusr=$_POST['usr'];
	echo "$djnc";
	echo "$jusr";
	$que=mysql_query("delete from junction_user_detail where junction_id=$djnc and user_id=$jusr");
	unset ($_POST['junc']);
	unset ($_POST['usr']);
	redirect();	
}
if (isset($_POST['accuid']))
{
	$uid=$_POST['accuid'];
	$wha=$_POST['what'];
	if ($wha == 'block')
	{
		mysql_query("update user_login set block=1 where user_id='$uid'");
		unset($_POST['accuid']);
		redirect();
	}
	else
	{
		mysql_query("update user_login set block=0 where user_id='$uid'");
		unset($_POST['accuid']);
		redirect();
	}
}
if (isset($_POST['remid']))
{
	$remuid=$_POST['remid'];
	$chk=mysql_query("delete from user_login where user_id='$remuid'");
	if ($chk)
	{
		echo"<script> alert ('remove successful')</script>";
		unset($_POST['remid']);
		redirect();
	}
	else 
	{
	echo"<script> alert ('remove failed')</script>";
		unset($_POST['remid']);
		redirect();
	}
}
if (isset($_POST['reset']))
{
	echo "<script>alert('resetting')</script>";
	$uid=$_POST['reset'];
	$uin=mysql_result(mysql_query("select name from user_login where user_id=$uid"),0);
	$uinen=sha1($uin);
	mysql_query("update user_login set paswd='$uinen' where user_id=$uid");
	echo"<script> alert ('password reset successful')</script>";
	unset($_POST['reset']);
	echo "mysql_error()";
		echo "<script>alert('done')</script>";
	redirect();
}
if (isset($_POST['email']) and isset($_POST['paswd']) and isset($_POST['uname']))
{
	$em=$_POST['email'];
	$paswd=sha1($_POST['paswd']);
	$uname=$_POST['uname'];
	$chkdu=mysql_num_rows(mysql_query("select block from user_login where name='$uname' or email='$em'"));
	if ($chkdu >=1)
	{
		echo "<script>alert('user already exists')</script>";
			redirect();
	}
	else 
	{
		mysql_query("insert into user_login (paswd,name,email) values ('$paswd','$uname','$em')");
		if (mysql_affected_rows()== 1)
		{
			echo "<script>alert('$uname added')</script>";
					redirect();
		
		}
		else 
		{
			mysql_query("delete from user_login where name='$uname' or email='$em'");
			echo "<script>alert('occured user deleted')</script>";
				redirect();
		}
		
	}
	
}

function stats($stat)
{
	switch ($stat)
	{
		case 0:
		$usr=mysql_num_rows(mysql_query("select user_id from user_login"));
		echo "$usr";
		break;
		case 1:
		$junc=mysql_num_rows(mysql_query("select junction_id from junction_master"));
		echo "$junc";
		break;
		case 2:
		echo "<h2>Users assigned to junctions</h2>";
		$junm=mysql_num_rows(mysql_query("select junction_id from junction_user_detail"));
		echo "<table border=\"1\" cellpadding=\"10\"  class=\"table table-bordered\">
		<tr><th >User</th> <th>Assigned junction </th> <th> delete </th></tr>";
		if (!$junm)
		{
			echo "<tr><td colspan=\"3\">no user assigned to any junction</td></tr> </table>";
		}
		else 
		{
			for($i=0;$i<$junm;$i++)
			{
			$que=mysql_query("select junction_id,user_id from junction_user_detail");
			mysql_data_seek($que,$i);
			$res=mysql_fetch_assoc($que);
			$res2=mysql_fetch_assoc(mysql_query("select name from user_login where user_id=$res[user_id]"));
			$usr=$res2['name'];
			$jnc=mysql_query("select junction_id from junction_user_detail where user_id !=(select user_id from user_login where name='$usr') and junction_id not in(select junction_id from junction_user_detail where user_id=(select user_id from user_login where
			name='$usr'))");
			echo "<tr><td align=\"center\" valign=\"middle\"> $res2[name] </td>
			<td align=\"center\" valign=\"middle\"> $res[junction_id] </td> 
			";
			echo "<td><form action=\"#\" method=\"post\">
				<input type=\"hidden\" value=\"$res[user_id]\" name=\"usr\"></input>
				<input type=\"hidden\" value=\"$res[junction_id]\" name=\"junc\"></input>
				<input type=\"submit\" value=\"delete\" class=\"btn\"></input></form></td></tr>";
			}
			echo "</table>";
		}
		
		break;
		case 3:
		$tcars=mysql_result(mysql_query("select sum(cars) from sensor_master"),0);
		echo "$tcars";
		break;
		case 4:
		$ttw=mysql_result(mysql_query("select sum(twowheelers) from sensor_master"),0);
		echo "$ttw";
		break;
		case 5:
		$ttb=mysql_result(mysql_query("select sum(buses) from sensor_master"),0);
		echo "$ttb";
		break;
		case 6:
		echo "<h2> Assign junction to user</h2>";
		echo "<form action=\"#\" method=\"post\">
		<select name=\"us\">";
		$jque=mysql_query("select junction_id from junction_master");
		$uque=mysql_query("select name from user_login");
		$guid=mysql_query("select user_id from user_login");
		$gun=mysql_num_rows($uque);
		$gjn=mysql_num_rows($jque);
		for($g=0;$g<$gun;$g++)
		{
			mysql_data_seek($uque,$g);
			mysql_data_seek($guid,$g);
			$gnu=mysql_fetch_assoc($uque);
			$gnu2=mysql_fetch_assoc($guid);
			echo "<option value=\"$gnu2[user_id]\">$gnu[name]</option>";
		}
		echo "</select>";
		echo "<select name=\"js\">";
		for ($gj=0;$gj<$gjn;$gj++)
		{
			mysql_data_seek($jque,$gj);
			$gnj=mysql_fetch_assoc($jque);
			echo "<option value=\"$gnj[junction_id]\">$gnj[junction_id]</option>";
		}
		echo "</select> <input type=\"submit\" value=\"assign\" class=\"btn\" ></input></form>";
		break;
		case 7:
		$gn=mysql_query("select * from junction_detail");
		$gnn=mysql_num_rows($gn);
		for ($i=0;$i<$gnn;$i++)
		{
			mysql_data_seek($gn,$i);
			$nd=mysql_fetch_assoc($gn);
			echo "
			<table border=\"1\" cellpadding=\"5\" class=\"table table-bordered\">
			<tr> <td rowspan=\"5\"> <img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$nd[lat_pos],$nd[long_pos]&zoom=15&size=100x100&maptype=roadmap
			&markers=color:red%7Clabel:%7C$nd[lat_pos],$nd[long_pos]\"></img>
			</td></tr>
			<tr><td> Junction id:</td><td> $nd[junction_id]</td></tr>
			<tr> <td>Jocation </td><td>$nd[junction_location] </td></tr>
			<tr> <td>Area</td><td>$nd[junction_area] </td></tr>
			<tr> <td> Assigned user/s: </td>";
			$gju=mysql_query("select user_id from junction_user_detail where junction_id=$nd[junction_id]");
			$gjn=mysql_num_rows($gju);
			for ($j=0;$j<$gjn;$j++)
			{
				mysql_data_seek($gju,$j);
				$gud=mysql_fetch_assoc($gju);
				$ud=mysql_fetch_assoc(mysql_query("select name from user_login where user_id=$gud[user_id]"));
				 echo "<td>   $ud[name]   ";
			}
			if ($gjn==0)
			{
				echo "</tr></table>";
			}
			else 
			{
				echo "</td></tr></table>";
			}
		}
		break;
		case 8:
		$getus=mysql_query("select * from user_login");
		$un=mysql_num_rows($getus);
		if ($un == 0)
		{
			echo "No users added ";
		}
		else
		{
			echo "<table class=\"table table-bordered\" > 
			<tr><td>Id</td><td>Password</td><td>name</td><td>Email</td></tr>";
			for($h=0;$h<$un;$h++)
			{
				mysql_data_seek($getus,$h);
				$ustat=mysql_fetch_array($getus);
				echo "<tr> <td>$ustat[user_id] </td><td>$ustat[paswd] </td><td>$ustat[name] </td><td> $ustat[email]</td>";
				if ($ustat['block'] == 0)
				{
					echo "<form action=\"#\" method=\"post\"><td><input class=\"btn btn-warning\" type=\"submit\" name=\"block\" value=\"Block\" onclick=\"return confirm('Are you sure?');\">";
					echo "<input value=\"$ustat[user_id]\" type=\"hidden\" name=\"accuid\"/>
					<input value=\"block\" type=\"hidden\" name=\"what\"/></form>";
				}
				else 
				{
					echo "<form action=\"#\" method=\"post\"><td><input class=\"btn btn-success\" type=\"submit\" name=\"unblock\" value=\"Unlock\" onclick=\"return confirm('Are you sure?');\">";
					echo "<input value=\"$ustat[user_id]\" type=\"hidden\" name=\"accuid\"/>
					<input value=\"unblock\" type=\"hidden\" name=\"what\"/></form>";
				}
			
					echo "<form action=\"#\" method=\"post\"><input class=\"btn btn-danger\" type=\"submit\" name=\"remove\" value=\"Remove\" onclick=\"return confirm('Are you sure?');\"></input>";
					echo "<input value=\"$ustat[user_id]\" type=\"hidden\" name=\"remid\"/> </form>";
					
					echo "<form action=\"#\" method=\"post\">
					<input class=\"btn btn-info\" type=\"submit\" name=\"res\" value=\"Reset password\" onclick=\"return confirm('Are you sure?');\"></input>";
					echo "<input value=\"$ustat[user_id]\" type=\"hidden\" name=\"reset\"></input> </form>";
				}
				echo "</td></tr></table>";
				}
		break;
		case 9:
		$ttw=mysql_result(mysql_query("select sum(rickshaw) from sensor_master"),0);
		echo "$ttw";
		break ;
		case 10:
		$ttd=mysql_result(mysql_query("select sum(data_id) from sensor_master"),0);
		echo "$ttd";
		break ;
		case 11:
		echo "<form action=\"#\" method=\"post\" class=\"form-horizontal\">
					<div class=\"control-group\">
						<label class=\"control-label\" for=\"inputName\">Username</label>
						<div class=\"controls\">
							<input type=\"text\" name=\"uname\" placeholder=\"Username\"></input>
						</div>
					</div>
					<div class=\"control-group\">
						<label class=\"control-label\" for=\"inputEmail\">Email</label>
						<div class=\"controls\">
							<input type=\"email\" name=\"email\" placeholder=\"Email\"></input>
						</div>
					</div>
					<div class=\"control-group\">
						<label class=\"control-label\" for=\"inputPassword\">Password</label>
						<div class=\"controls\">
							<input type=\"password\" name=\"paswd\" id=\"inputPassword\" placeholder=\"Password\">
						</div>
					</div>
					<div class=\"control-group\" >
					<div class=\"controls\">
						<button type=\"submit\" class=\"btn btn-primary\">Save changes</button>
						<button type=\"reset\" class=\"btn\">Cancel</button>
						</div>
					</div>
			</form>";
		break;
		}
}
?>