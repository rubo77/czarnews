<?
$pagetitle = "User Login";
include_once("cn_head.php");
?>

<? if(isset($msg)) { ?><font color=#ff0000><?=$msg?></font><? } ?>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td valign="top">
<form method="post" action="index.php">
<table width="95%" border="0" cellspacing="0" cellpadding="4" align="center"><tr><td nowrap>
<b>User Login</b>
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr><tr><td class="name">
Username:
</td><td>
<input class="input" type="text" name="usern" value="">
</td></tr><tr><td class="name">
Password:
</td><td>
<input class="input" type="password" name="passw" value="">
</td></tr><tr><td class="name">
&nbsp;
</td><td>
<input type="submit" name="submit" value="Login" class="input">
</td></tr>
</table>
		</td>
		<td valign="top"><br>
This is an admin area that is restricted to authorized users only.  Please login with your username and password.<br>
<a href="fpass.php">Forget your password?</a>
		</td>
	</tr></form>
</table>

<?
include_once("cn_foot.php");
?>