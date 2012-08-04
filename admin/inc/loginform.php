<h2>CMS Login</h2>
<div id="login">
<h3>Bitte authentifizieren Sie sich:</h3>
<form action="index.php" method="post">
<input type="hidden" name="login" value="login">
<table style="border:0px;">
<tr>
<td width="100px"><strong><strong>Benutzername:</strong></td>
<td><input type="text" name="user" value="" style="width:200px;"></td>
</tr>
<tr>
<td width="100px"><strong><strong>Passwort:</strong></td>
<td><input type="password" name="password" value="" style="width:200px;"></td>
</tr>
<tr>
<td></td>
<td><input type="submit" value="Login"></td>
</tr>
</table>
</form>
<br><br>

<?php 
if(getconfig("visitors_can_register")=="on"){
?><a href="?register=register">Registrieren</a>
<?php
}
?>
<br>
</div>
