<?php 

if(defined(MODULE_ADMIN_REQUIRED_PERMISSION)){
  if($_SESSION["group"] < MODULE_ADMIN_REQUIRED_PERMISSION){
    die("Fuck you!");
  }

}

if(isset($_POST["submit"])){
  $unencoded = $_POST["template_content"];
  
  setconfig("newsletter_template_title", 
  mysql_real_escape_string($_POST["template_title"]));
  
  setconfig("newsletter_template_content", 
  mysql_real_escape_string($_POST["template_content"]));
  unset($_SESSION["newsletter_data"]);
  
  if(strlen(getconfig("newsletter_template_content")) < strlen($unencoded)){
     mysql_query("alter table ".tbname("settings")." change value value TEXT;");
     setconfig("newsletter_template_content", 
  mysql_real_escape_string($_POST["template_content"]));
       
  }
  
  
  echo "<script type=\"text/javascript\">
  url = window.location.href.toString()
  window.location.replace(url);
  </script>";
  
}

define("NEWSLETTER_TEMPLATE_TITLE", getconfig("newsletter_template_title"));
define("NEWSLETTER_TEMPLATE_CONTENT", getconfig("newsletter_template_content"));




?>

<h3>Vorlage</h3>
<form method="post" action="<?php echo getModuleAdminSelfPath()?>">
<p><strong>Titel: </strong><input type="text" maxlength=78 size=78 name="template_title" value="<?php echo htmlspecialchars(NEWSLETTER_TEMPLATE_TITLE)?>"></p>


<p>
<textarea id="template_content" name="template_content" cols=60 rows=16><?php 
echo htmlspecialchars(NEWSLETTER_TEMPLATE_CONTENT);
?></textarea></p>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'template_content',
					{
						skin : 'kama'
					});                                         

</script>

<p>
<input type="submit" name="submit" value="Vorlage speichern">
</p>
</form>