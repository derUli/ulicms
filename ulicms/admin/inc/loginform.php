<h2>CMS Login</h2>
<div id="login">
<h3>Bitte authentifizieren Sie sich:</h3>
<form action="index.php" method="post">
<input type="hidden" name="login" value="login">
<?php if(!empty($_REQUEST["go"])){
     ?>
<input type="hidden" name="go" value='<?php
     echo htmlspecialchars($_REQUEST["go"])?>'>
<?php }
?>
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
<td style="padding-top:10px; text-align:center;"><input style="width:100%;" type="submit" value="Login"></td>
</tr>
</table>
</form>

<?php
if(getconfig("visitors_can_register") === "on" or getconfig("visitors_can_register") === "1"){

     ?><a href="?register=register&<?php
     if(!empty($_REQUEST["go"])){
         echo "go=" . real_htmlspecialchars($_REQUEST["go"]);
         }
     ?>">Registrieren</a>
<?php
     }
?>
</div>
