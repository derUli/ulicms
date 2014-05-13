<?php 
function frontend_login_render(){
  if(!logged_in()){
  $html = "";
  if(isset($_POST["login"]) and !validate_login($_POST["user"], $_POST["password"])){

 $html .= "<p class=\"ulicms_error\">Die Zugangsdaten sind fehlerhaft. Bitte probieren Sie es erneut.</p>";
 
}
  $html .= '
<form action="'.buildSEOUrl().'" method="post">
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
<td style="padding-top:10px; text-align:center;"><input style="width:100%;" type="submit" value="Login"></td>
</tr>
</table>
</form>';

  $html = apply_filter($html, "frontend_login_form");
}
else{
  $html = "Hallo ".$_SESSION["ulicms_login"]."!";
  $html = apply_filter($html, "frontend_login_welcome");
}

  return $html;
}
?>