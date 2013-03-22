<?php 
define("MODULE_ADMIN_HEADLINE", "Newsletter");

$required_permission = getconfig("newsletter_required_permission");

if($required_permission === false){
   $required_permission = 40;
}


define("NEWSLETTER_TEMPLATE_TITLE", getconfig("newsletter_template_title"));
define("NEWSLETTER_TEMPLATE_CONTENT", getconfig("newsletter_template_content"));

define("MODULE_ADMIN_REQUIRED_PERMISSION", $required_permission);

define("DATE_FORMAT", getconfig("date_format"));

include getModulePath("newsletter")."newsletter_install.php";
newsletter_check_install();


include getModulePath("newsletter")."newsletter_helper.php";


if(!isset($_SESSION["newsletter_data"])){
   $_SESSION["newsletter_data"] = array(
   "newsletter_receivers"    => array(),
   "newsletter_remaining" => 0,
   "newsletter_text" => NEWSLETTER_TEMPLATE_CONTENT,
   "newsletter_title" => NEWSLETTER_TEMPLATE_TITLE
   );
}




function newsletter_admin(){

?>
<a href="index.php?action=module_settings&module=newsletter&newsletter_action=prepare_newsletter">Newsletter vorbereiten</a> | 
<a href="index.php?action=module_settings&module=newsletter&newsletter_action=show_subscribers">Abonnenten anzeigen</a> | 
<a href="index.php?action=module_settings&module=newsletter&newsletter_action=edit_template">Vorlage bearbeiten</a> | 
<a href="index.php?action=module_settings&module=newsletter&newsletter_action=send_it">Versand durchführen<?php 
if($_SESSION["newsletter_data"]["newsletter_remaining"] > 0){
   echo " ".$_SESSION["newsletter_data"]["newsletter_remaining"].
        " in Warteschlange";
}
?></a>
<br/>
<?php
if($_GET["newsletter_action"] == "show_subscribers"){
   $query = mysql_query("SELECT * FROM ".tbname("newsletter_subscribers"). " ORDER by email");
   echo "<p>Dieser Newsletter wurde ".mysql_num_rows($query). "x abonniert.";
   if(mysql_num_rows($query) > 0){
     
     echo "<table border=1>";
     echo "<tr style=\"font-weight:bold;\"><td>E-Mail:</td>";
     echo "<td>Abonnent seit:</td>";
     echo "</tr>";
     
     while($row = mysql_fetch_assoc($query)){
        echo "<tr>".
        "<td>".
        $row["email"].
        "</td>".
        "<td>".
        date(DATE_FORMAT, $row["subscribe_date"]).
        "</td></tr>";
             
     }     
     
     echo "</table>";
   
   
   }
} 
else if($_GET["newsletter_action"] == "edit_template"){
    include getModulePath("newsletter")."newsletter_template.php";

}
else if($_GET["newsletter_action"] == "prepare_newsletter"){
   include getModulePath("newsletter")."newsletter_form.php";

}
else if($_GET["newsletter_action"] == "send_it"){
   include getModulePath("newsletter")."send_it.php";

}
else{
   echo "<br/>";
   echo "<p>(C) 2013 by Ulrich Schmidt<br/>";
   echo "Version ".NEWSLETTER_MODULE_VERSION."</p>";

}
}
 
?>
