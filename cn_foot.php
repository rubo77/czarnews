<?php 
if(file_exists("install.php")) {
print "<font color=#ff0000><b>Warning: \"install.php\" is a security risk, please delete the file from your server.</b></font><br>";
}
?>
<br>
</td></tr>
</table>

		</td>
	</tr>
	<tr>
		<td align="right">
CzarNews v<?php  print $GLOBALS['set']['version'] ?? ""; ?> &copy; 2003&#8211;2004 Vance Lucas &middot; PHP-8.x-Port &copy; <?=date("Y");?> eclabs.de
		</td>
	</tr>
</table>

</body>

</html>