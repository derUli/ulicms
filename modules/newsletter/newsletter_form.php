<?php 

if(defined(MODULE_ADMIN_REQUIRED_PERMISSION)){
  if($_SESSION["group"] < MODULE_ADMIN_REQUIRED_PERMISSION)
    die("Fuck you!");


}


if(isset($_POST["submit"])){
   $_SESSION["newsletter_data"]["newsletter_text"] =
   $_POST["newsletter_text"];
   
   $servername = $_SERVER["SERVER_NAME"];
   
   if($_SERVER['HTTPS'])
     $url = "https://";
   else
     $url = "http://";
     
   $url .= $servername;
   
   // Replace Relative URLs
   $_SESSION["newsletter_data"]["newsletter_text"] = 
   str_replace("<a href=\"/", "<a href=\"$url/", 
   $_SESSION["newsletter_data"]["newsletter_text"]);
   
   $_SESSION["newsletter_data"]["newsletter_text"] = 
   str_replace("<a href='/", "<a href='$url/", 
   $_SESSION["newsletter_data"]["newsletter_text"]);
   
   $_SESSION["newsletter_data"]["newsletter_text"] = 
   str_replace(" src=\"/", " src=\"$url/", 
   $_SESSION["newsletter_data"]["newsletter_text"]);
   
   $_SESSION["newsletter_data"]["newsletter_text"] = 
   str_replace(" href=\"?seite=", 
   " href=\"$url/?seite=",
   $_SESSION["newsletter_data"]["newsletter_text"]);
   
 
   
   $_SESSION["newsletter_data"]["newsletter_title"] =
   $_POST["newsletter_title"];
    
   $send_to = $_POST["send_to"];
   
   if($send_to == "ALL")
      $subscribers = getSubscribers();
   else
      $subscribers = array($send_to);
   $_SESSION["newsletter_data"]["newsletter_receivers"] = $subscribers;
   $_SESSION["newsletter_data"]["newsletter_remaining"] = count($subscribers);
   
}


$user = getUserById($_SESSION["login_id"]);
$email = $user["email"];

?>
<h3>Newsletter vorbereiten</h3>
<?php 
if(isset($_POST["submit"])){
?>
<script type="text/javascript">
url = window.location.href.toString()
window.location.replace(url);
</script>

<?php }?>
<form method="post" action="<?php echo getModuleAdminSelfPath()?>">
<p><strong>Titel: </strong><input type="text" maxlength=78 size=78 name="newsletter_title" value="<?php echo htmlspecialchars($_SESSION["newsletter_data"]["newsletter_title"])?>"></p>


<p>
<textarea id="newsletter_text" name="newsletter_text" cols=60 rows=16><?php 
echo htmlspecialchars($_SESSION["newsletter_data"]["newsletter_text"]);
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