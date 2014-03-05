<?php if(defined("_SECURITY")){
     $acl = new ACL();
    
     if($acl -> hasPermission("pages")){
        
        
         ?>
<h2>Seiten</h2>
<p>Hier können Sie die einzelnen Seiten Ihrer Homepage bearbeiten oder löschen.</p>
<p><a href="index.php?action=pages_new">Seite verfassen</a></p>

<script type="text/javascript">
function filter_by_language(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_language=" + element.options[index].value)
   }
}

function filter_by_status(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_status=" + element.options[index].value)
   }
}

$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=pages&filter_category=" + valueSelected)
   
   });

});

</script>

Nach Sprache filtern: 
<select name="filter_language" onchange="filter_by_language(this)">
<option value="">Bitte auswählen</option>
<?php
         if(!empty($_GET["filter_language"])){
             $_SESSION["filter_language"] = $_GET["filter_language"];
             }
        
        
         if(!isset($_SESSION["filter_category"])){
             $_SESSION["filter_category"] = 0;
             }
        
        
         if(isset($_GET["filter_category"])){
             $_SESSION["filter_category"] = intval($_GET["filter_category"]);
            
             }
        
        
        
        
        
         if(!empty($_GET["filter_status"])){
             $_SESSION["filter_status"] = $_GET["filter_status"];
             }
        
         $languages = getAllLanguages();
         for($j = 0; $j < count($languages); $j++){
             if($languages[$j] == $_SESSION["filter_language"]){
                 echo "<option value='" . $languages[$j] . "' selected>" . $languages[$j] . "</option>";
                 }else{
                 echo "<option value='" . $languages[$j] . "'>" . $languages[$j] . "</option>";
                 }
            
            
             }
         ?>
</select>
&nbsp;&nbsp;
Status: <select name="filter_status" onchange="filter_by_status(this)">
<option value="Standard" <?php
         if($_SESSION["filter_status"] == "standard"){
             echo " selected";
             }
         ?>>Standard</option>
<option value="trash" <?php
         if($_SESSION["filter_status"] == "trash"){
             echo " selected";
             }
         ?>>Papierkorb</option>
</select>
&nbsp; &nbsp;
Kategorie: 
<?php
        echo categories :: getHTMLSelect($_SESSION["filter_category"], true);
        ?>

<?php
         if($_SESSION["filter_status"] == "trash" and $acl -> hasPermission("pages")){
             ?>

&nbsp;&nbsp;
<a href="index.php?action=empty_trash" onclick="return confirm('Papierkorb leeren?');">Papierkorb leeren</a>
<?php }
         ?>

<br/>

<table border=1>
<tr style="font-weight:bold;">
<td style="width:40px;"></td>
<td><a href="?action=pages&order=systemname">Permalink</a></td>
<td><a href="?action=pages&order=menu">Menü</a></td>
<td><a href="?action=pages&order=position">Position</a></td>
<td><a href="?action=pages&order=parent">Übergeordnet</a></td>
<td><a href="?action=pages&order=active">Aktiviert</a></td>
<td><span data-tooltip="Die Seite auf der Webpräsenz öffnen">&nbsp;</span></td>
<td>&nbsp;</td>

<td><?php
         if($_SESSION["filter_status"] == "trash")
             echo "&nbsp;";
         else
             echo "&nbsp;";
        
         ?></td>
</tr>
<?php
         $order = basename($_GET["order"]);
         $filter_language = basename($_GET["filter_language"]);
         $filter_status = basename($_GET["filter_status"]);
        
         if(empty($filter_language)){
             if(!empty($_SESSION["filter_language"])){
                 $filter_language = $_SESSION["filter_language"];
                 }
            else{
                 $filter_language = "";
                 }
             }
        
        
         if($_SESSION["filter_status"] == "trash"){
             $filter_status = "`deleted_at` IS NOT NULL";
             }
        else{
             $filter_status = "`deleted_at` IS NULL";
             }
        
        
        
        
        
         if(empty($order)){
             $order = "menu";
             }
        
         if(!empty($filter_language)){
             $filter_sql = "WHERE language = '" . $filter_language . "' ";
             }else{
             $filter_sql = "WHERE 1=1 ";
             }
        
         if($_SESSION["filter_category"] != 0){
             $filter_sql .= "AND category=" . intval($_SESSION["filter_category"]) . " ";
             }
        
         $filter_sql .= "AND " . $filter_status . " ";
        
         $query = db_query("SELECT * FROM " . tbname("content") . " " . $filter_sql . "ORDER BY $order,position, systemname ASC") or die(db_error());
         if(db_num_rows($query) > 0){
             while($row = db_fetch_object($query)){
                 ?>
<?php
                 echo '<tr>';
                 echo "<td style=\"width:40px;\">--&gt;</td>";
                 echo "<td>" . $row -> systemname . "</td>";
                 echo "<td>" . $row -> menu . "</td>";
                
                 echo "<td>" . $row -> position . "</td>";
                 echo "<td>" . getPageSystemnameByID($row -> parent) . "</td>";
                
                 if($row -> active){
                     echo "<td>Ja</td>";
                     }
                else{
                     echo "<td>Nein</td>";
                     }
                
                
                
                 echo "<td><a href=\"../" . $row -> systemname . ".html\" target=\"_blank\"><img src=\"gfx/preview.gif\">Anzeigen</a></td>";
                 echo "<td>" . '<a href="index.php?action=pages_edit&page=' . $row -> id . '"><img src="gfx/edit.gif"> Bearbeiten</a></td>';
                 if($_SESSION["group"] >= 40){
                    
                     if($_SESSION["filter_status"] == "trash"){
                         echo "<td>" . '<a href="index.php?action=undelete_page&page=' . $row -> id . '";"> <img src="gfx/undelete.png"> Wiederherstellen</a></td>';
                         }
                    else
                        {
                         echo "<td>" . '<a href="index.php?action=pages_delete&page=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif">  Löschen</a></td>';
                        
                        
                         }
                     }else{
                     echo "<td><img src=\"gfx/delete.gif\"> Löschen</td>";
                     }
                 echo '</tr>';
                
                 }
             ?>
<?php
             }
         ?>
</table>


<br/>

<?php
         }else{
         noperms();
         }
    
     ?>

<?php }
?>
