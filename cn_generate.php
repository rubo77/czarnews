<?
$pagetitle = "Code Generator";
include("cn_auth.php");
include("cn_head.php");

if($op == "gen") {
$code = "
&lt;?
\$tpath = \"$DOCUMENT_ROOT/\";
";
if($cats == "sel") { $code .= "\$c = \"$cat\";\n"; }
if($page == "headlines.php" && !empty($lim)) { $code .= "\$lim = \"$lim\";\n"; }
if($page == "headlines.php" && !empty($lim)) { $code .= "\$page = \"$pageurl\";\n"; }
$code .= "include(\$tpath . \"$page\");
?&gt;
";
?>
<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
PHP Code:<br>
<small>Copy and Paste the code below into your own PHP file</small><br>
<textarea cols="40" rows="6" name="output" class="input"><? print $code; ?></textarea>
</form>
<?
}
?>

<p>This page will generate code for you to use on your website to include news on your page.  Simply select the category for the news you want to display, or select all categories.</p>

<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
<table  width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<tr><td nowrap>
<b>Generate Code</b>
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>" nowrap>
Category:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="radio" name="cats" value="all" id="allcats" CHECKED> <label for="allcats">Display news in ALL categories</label><br>
<input type="radio" name="cats" value="sel" id="selcats" onClick="openBox(1);"> <label for="selcats">Display news for selected category</label><br>
<div id="box1" style="display: none">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? print catBox("cat"); ?>
</div>
</td></tr>
<tr><td nowrap>
<b>Include Page</b>
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>" nowrap>
Page:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="radio" name="page" value="news.php" id="news" CHECKED> <label for="news">News Page (news.php)</label><br>
<input type="radio" name="page" value="headlines.php" id="head" onClick="openBox(2);"> <label for="head">Headlines (headlines.php)</label><br>
<div id="box2" style="display: none">
<table border="0" cellpadding="0" cellspacing="0" summary=""><tr>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>URL to your news page:</td>
<td><input type="text" name="pageurl" value="/index.php"></td>
</tr><tr>
<td>&nbsp;</td>
<td># of news items to display:&nbsp;</td>
<td><input type="text" name="lim" value="5" size="3"></td>
</tr>
</table>
</div>
</td></tr>

<tr><td bgcolor="<? print $MenuBg2; ?>">
&nbsp;
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="hidden" name="op" value="gen">
<input type="submit" name="submit" value="Generate Code" class="input">
</td></tr>
</table><br>
</form>

<hr size="1" width="95%">
<span class="subtitle"></span>
<?
include("cn_foot.php");
?>