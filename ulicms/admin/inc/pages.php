<?php if(defined("_SECURITY")){
     $acl = new ACL();
    
     if($acl -> hasPermission("pages")){
        
        
         ?>
<h2><?php echo TRANSLATION_PAGES;?></h2>
<p><?php echo TRANSLATION_PAGES_INFOTEXT;?></p>
<p><a href="index.php?action=pages_new"><?php echo TRANSLATION_CREATE_PAGE;?></a></p>

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

<?php echo TRANSLATION_FILTER_BY_LANGUAGE;?> 
<select name="filter_language" onchange="filter_by_language(this)">
<option value=""><?php echo TRANSLATION_PLEASE_SELECT;?></option>
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
                 echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                 }else{
                 echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                 }
            
            
             }
         ?>
</select>
&nbsp;&nbsp;
<?php echo TRANSLATION_STATUS;?> <select name="filter_status" onchange="filter_by_status(this)">
<option value="Standard" <?php
         if($_SESSION["filter_status"] == "standard"){
             echo " selected";
             }
         ?>><?php echo TRANSLATION_STANDARD;?></option>
<option value="trash" <?php
         if($_SESSION["filter_status"] == "trash"){
             echo " selected";
             }
         ?>><?php echo TRANSLATION_RECYCLE_BIN;?></option>
</select>
&nbsp; &nbsp;
<?php echo TRANSLATION_CATEGORY;?> 
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
<td><a href="?action=pages&order=systemname"><?php echo TRANSLATION_PERMALINK;?></a></td>
<td><a href="?action=pages&order=menu"><?php echo TRANSLATION_MENU;?></a></td>
<td><a href="?action=pages&order=position"><?php echo TRANSLATION_POSITION;?></a></td>
<td><a href="?action=pages&order=parent"><?php echo TRANSLATION_PARENT;?></a></td>
<td><a href="?action=pages&order=active"><?php echo TRANSLATION_ACTIVATED;?></a></td>
<td><a href="?action=pages&order=active"><?php echo TRANSLATION_VIEW;?></a></td>
<td><?php echo TRANSLATION_EDIT;?></td>
<td><?php echo TRANSLATION_DELETE;?></td>


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
                 echo "<td>" . htmlspecialchars($row -> title) . "</td>";
                 echo "<td>" . $row -> menu . "</td>";
                
                 echo "<td>" . $row -> position . "</td>";
                 echo "<td>" . getPageTitleByID($row -> parent) . "</td>";
                
                 if($row -> active){
                     echo "<td>".TRANSLATION_YES."</td>";
                     }
                else{
                     echo "<td>".TRANSLATION_NO."</td>";
                     }
                
                
                if(startsWith($row->redirection, "#")){
                echo "<td style='text-align:center'></td>";
                } else {
                 $domain = getDomainByLanguage($row->language);
                 if(!$domain){
                    $url = "../".$row -> systemname . ".html";
                    } else {
                  $url = "http://".$domain."/". $row -> systemname . ".html";
                 }
                 echo "<td style='text-align:center'><a href=\"".$url ."\" target=\"_blank\"><img src=\"gfx/preview.gif\" alt=\"".TRANSLATION_VIEW."\" title=\"".TRANSLATION_VIEW."\"></a></td>";
                
                 }
                 echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_edit&page=' . $row -> id . '"><img src="gfx/edit.gif" alt="'.TRANSLATION_EDIT.'" title="'.TRANSLATION_EDIT.'"></a></td>';
           
                    
                     if($_SESSION["filter_status"] == "trash"){
                         echo "<td style='text-align:center'>" . '<a href="index.php?action=undelete_page&page=' . $row -> id . '";"> <img src="gfx/undelete.png" alt="'.TRANSLATION_RECOVER.'" title="'.TRANSLATION_RECOVER.'"></a></td>';
                         }
                    else
                        {
                         echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_delete&page=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif" alt="'.TRANSLATION_DELETE.'" title="'.TRANSLATION_DELETE.'"></a></td>';
                        
                        
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
