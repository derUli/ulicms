<?php
define("MODULE_ADMIN_HEADLINE", "Einstellungen des Kontaktformulars");
define("MODULE_ADMIN_REQUIRED_PERMISSION", "kontaktformular_settings");


function kontaktformular_admin(){
    
     if(isset($_POST["submit"])){
         setconfig("contact_form_email",
             db_escape($_POST["contact_form_email"]));
        
         if(empty($_POST["kontaktformular_thankyou_page"]))
             deleteconfig("kontaktformular_thankyou_page");
         else
             setconfig("kontaktformular_thankyou_page",
                 db_escape($_POST["kontaktformular_thankyou_page"]));
        
         }
    
    
    
    $kontaktformular_thankyou_page = getconfig("kontaktformular_thankyou_page");
    $contact_form_email = getconfig("contact_form_email");
    
    $pages = getAllSystemNames();
    
    ?>

<form action="<?php echo getModuleAdminSelfPath()?>" method="post">
<p>Mails senden an<br/>
<input type="email" name="contact_form_email" value="<?php echo htmlspecialchars($contact_form_email, ENT_QUOTES, "UTF-8");
    ?>">
</p>
<p>Zielseite<br/>
<select name="kontaktformular_thankyou_page" size=1>
<option value=""<?php if(!$kontaktformular_thankyou_page) echo " selected=\"selected\""?>>[Standard]</option>
<?php
        for ($i = 0; $i < count($pages); $i++){
        $p = htmlspecialchars($pages[$i], ENT_QUOTES, "UTF-8");
        ?>
<option value="<?php echo $p;
        ?>"<?php if($kontaktformular_thankyou_page == $pages[$i]) echo " selected=\"selected\""?>><?php echo $p;
        ?></option>
<?php }
    ?>
</select>
</p>

<p><input type="submit" name="submit" value="Einstellungen speichern"/></p>
</form>
<?php
     }

?>
