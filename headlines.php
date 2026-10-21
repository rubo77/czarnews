<?php 
### headlines.php [v1.12] 

// $tpath is set by the page that embeds this file; default to this
// directory so headlines.php also works when requested directly. It must
// never come from the request (remote file inclusion).
if(!isset($tpath)) { $tpath = dirname(__FILE__) . "/"; }
require($tpath . "cn_config.php");
if(!isset($link)) {
exit("Please make sure the \"\$tpath\" veriable is the root path to where 'headlines.php' is on your server.");
}
// Page URL to link the news items 
if(!isset($page)) { $page = "/index.php"; }
// Set limit for number of items displayed 
if(!isset($lim)) { $lim = "5"; }
// Number of characters to cut news titles at 
if(!isset($charnum)) { $charnum = "18"; }
// optional category filter
if(!isset($c)) { $c = ""; }

// Get news items from database, and order them from newest to oldest 
if($c != "") { $t_news .= " WHERE cat = '$c'"; }
$q['info'] = mysql_query("SELECT * FROM $t_news ORDER BY date DESC LIMIT 0, $lim", $link);

while($h = mysql_fetch_array($q['info'], MYSQL_ASSOC)) {
// Edit the HTML code below for the output of your headlines 
// Edit the HTML code between the dashed lines
// ------------------------------------------------------------------
?>
&nbsp;&#187; <a href="<?php  print $page; ?>?a=<?php  print $h['id']; ?>" title="<?=$h['subject']?>"><?php  print cutstr($h['subject'],"$charnum"); ?></a><br>
<?php 
// ------------------------------------------------------------------
}
?>