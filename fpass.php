<?
$pagetitle = "Forgot Password";
include_once("cn_head.php");

if($go == "true") {
### Retrieve user info for email
if(empty($email)) { print E("You have not provided your email address"); }
$q[fpass] = mysql_query ("SELECT * FROM $t_user WHERE email = '$email' LIMIT 1", $link) or E("Could not search users:<br>" . mysql_error());
$inf = mysql_fetch_array($q[fpass], MYSQL_ASSOC) or E("There is no email that matches \"$email\" in the database");
// Email header info
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
$headers .= "X-Priority: 3\n";
$headers .= "X-MSMail-Priority: Low\n";
$headers .= "X-Mailer: php\n";
$headers .= "From: \"".$inf[user]."\" <".$email.">\n";
$subject = "CzarNews Password Mailer";
$msg = "$set[sitename]
Forgot Password Mailer

This email was sent to you becasue you or someone
else entered your email into the \"forgot password\"
page of the CzarNews script that you are a user of.
Your requested username and password are below:
------------------------------------------------------------------------

User: $inf[user]
Pass: $inf[pass]

------------------------------------------------------------------------
Login to your account at:
$set[scripturl]

";
mail("$inf[email]", "$subject", "$msg", $headers) or E("Could not email password information");
echo S("Password notification email has been sent");
exit;
}
$button_txt = "Send Password";
?>

<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td valign="top">
<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
<table  width="100%" border="0" cellspacing="1" cellpadding="2" align="center"><tr><td nowrap>
<b>Forgot Password</b>
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
Email:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="text" name="email" class="input" value="">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
&nbsp;
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="<?=$button_txt?>" class="input">
</td></tr>
</table>
		</td>
		<td valign="top"><br>
Please enter your email in the box provided, and an email will be sent to the email address you provide with your username and password.<br>
<a href="index.php">Back to the Main Page</a>
		</td>
	</tr></form>
</table>

<?
include_once("cn_foot.php");
?>