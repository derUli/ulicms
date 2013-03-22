<?php 

if(defined(MODULE_ADMIN_REQUIRED_PERMISSION)){
  if($_SESSION["group"] < MODULE_ADMIN_REQUIRED_PERMISSION){
    die("Fuck you!");
  }

}


define("NEWSLETTER_TEMPLATE_TITLE", getconfig("newsletter_template_title"));
define("NEWSLETTER_TEMPLATE_CONTENT", getconfig("newsletter_template_content"));

$user = getUserById($_SESSION["login_id"]);
$email = $user["email"];

?>
<h3>Newsletter senden</h3>
<form method="post" action="<?php echo getModuleAdminSelfPath()?>">
<p><strong>Titel: </strong><input type="text" maxlength=78 size=78 name="newsletter_title" value="<?php echo htmlspecialchars(NEWSLETTER_TEMPLATE_TITLE)?>"></p>


<p>
<textarea id="newsletter_text" name="newsletter_text" cols=60 rows=16><?php 
echo htmlspecialchars(NEWSLETTER_TEMPLATE_CONTENT);
?></textarea></p>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'newsletter_text',
					{
						skin : 'kama'
					});                                         

</script>
<p>


<input name="send_to" type="radio" checked value="<?php echo getconfig("email")?>"> Testmail an <?php echo getconfig("email")?><br/>
<?php
if($email != getconfig("email")){
?>
<input name="send_to" type="radio" value="<?php 

echo $email;
?>"> Testmail an <?php echo $email;?><br/>
}
<?php } ?>

<input name="send_to" type="radio" value="ALL"> An alle Abonnenten
</p>
<p>
<input type="submit" name="submit" value="Newsletter vorbereiten">
</p>
</form>