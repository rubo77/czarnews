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
CzarNews v<?php  print $set['version']; ?> copyright &copy; <?=date("Y");?> Vance Lucas<br>
Please visit <a href="http://www.czaries.net/scripts/" target="_blank" class="creditlink">Czaries.net</a> for more scripts & updates
		</td>
	</tr>
</table>

</body>

</html>