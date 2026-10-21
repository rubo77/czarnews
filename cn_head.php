<?php 
##########################################
### 
### CzarNews v1.12
### Made by: Czaries  [czaries@czaries.net]
### http://www.czaries.net/scripts/
### for more scripts and updates.
###
##########################################

// Include configuration file
include_once("cn_config.php");

function userlink() {
GLOBAL $useri;
if(isset($useri)) {
print "Welcome {$useri['user']} | <a href=\"?op=logout\">Logout</a>";
} else {
print "&nbsp;";
}
}
?>
<html>

<head>
<title>CzarNews v1.12 :: <?=$pagetitle;?></title>
<meta name="Author" content="Vance Lucas">
<meta name="Copyright" content="Copyright <?=date("Y")?> Vance Lucas - www.czaries.net">
<link rel="stylesheet" href="sv_styles.css" type="text/css">
<script type="text/javascript">
function popBox(type,id) {
window.open('/cn_upload.php?type=' + type + '&id=' + fileid ,'',
'width=350,height=250,left=150,top=150,resizable=0,scrollbars=auto');
}
function openBox( thebox ) {
	if (document.all( 'box' + thebox ).style.display == 'none') {
		document.all( 'box' + thebox ).style.display = 'block';
	}
	else if (document.all( 'box' + thebox ).style.display == 'block') {
		document.all( 'box' + thebox ).style.display = 'none';
	}
}
</script>
</head>

<body bgcolor="#ffffff" text="#000000" link="#0000ff" vlink="#800080" alink="#ff0000">

<table width="660" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td bgcolor="#FFFFFF">
<a href="/"><img src="<?=$imgdir?>cn_logo_s.gif" width="140" height="100" alt="CzarNews" border="0"></a><br>
		</td>
		<td bgcolor="#000000">
<img src="<?=$imgdir?>spacer.gif" width="1" height="1"><br>
		</td>
		<td width="99%" valign="bottom" bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
<td><img src="<?=$imgdir?>cn_logo_l.gif" width="300" height="100" alt="CzarNews"><br></td>
<td valign="bottom" align="right"><?php  print userlink(); ?></td>
</tr></table>
		</td>
	</tr>
	<tr>
		<td colspan="3" bgcolor="#000000">
<img src="<?=$imgdir?>spacer.gif" width="1" height="1"><br>
		</td>
	</tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
<td valign="top" width="150" align="left">
<table border="0" width="135" align="left">
	<?php  if(isset($useri)) { ?><tr><td width="100%"><a href="index.php" class="menulink">News</a></td></tr><?php  } ?>
	<?php  if(isset($useri) && ($useri['cats'] == "on" || $useri['admin'] == "on")) { ?><tr><td width="100%"><a href="cn_cats.php" class="menulink">Categories</a></td></tr><?php  } ?>
	<?php  if(isset($useri) && ($useri['words'] == "on" || $useri['admin'] == "on")) { ?><tr><td width="100%"><a href="cn_words.php" class="menulink">Keywords/Filter</a></td></tr><?php  } ?>
		<?php  if(isset($useri) && ($useri['users'] == "on" || $useri['admin'] == "on")) { ?><tr><td width="100%"><a href="cn_users.php" class="menulink">Users</a></td></tr><?php  } ?>
	<?php  if(isset($useri) && ($useri['config'] == "on" || $useri['admin'] == "on")) { ?><tr><td width="100%"><a href="cn_webconfig.php" class="menulink">Configuration</a></td></tr><?php  } ?>
	<?php  if(isset($useri) && $useri['admin'] == "on") { ?><tr><td width="100%"><a href="cn_generate.php" class="menulink">Code Generator</a></td></tr><?php  } ?>
	<?php  if(isset($useri)) { ?><tr><td width="100%"><a href="cn_info.php" class="menulink">Info/About</a></td></tr><?php  } ?>
	<?php  if(isset($useri)) { ?><tr><td width="100%"><a href="?op=logout" class="menulink">Logout</a></td></tr><?php  } ?>
	<tr><td width="100%">&nbsp;</td></tr>
</table>
</td><td valign="top" width="470">
<font class="title"><?=$pagetitle;?></font><br><br>
