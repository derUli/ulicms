<?php if(defined("_SECURITY")){
     $acl = new ACL();
    
     if($acl -> hasPermission("pages")){
        
         ?>
<h2><?php echo TRANSLATION_PAGES;
         ?></h2>
<p><?php echo TRANSLATION_PAGES_INFOTEXT;
         ?></p>
<p><a href="index.php?action=pages_new"><?php echo TRANSLATION_CREATE_PAGE;
         ?></a></p>

<script type="text/javascript">
function filter_by_language(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_language=" + element.options[index].value)
   }
}


function filter_by_menu(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_menu=" + element.options[index].value)
   }
}

function filter_by_active(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_active=" + element.options[index].value)
   }
}

function filter_by_parent(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_parent=" + element.options[index].value)
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
<p>
<?php echo TRANSLATION_FILTER_BY_LANGUAGE;
         ?><select name="filter_language" onchange="filter_by_language(this)">
<option value=""><?php echo TRANSLATION_PLEASE_SELECT;
         ?></option>
<?php
         if(!empty($_GET["filter_language"]) and in_array($_GET["filter_language"], getAllLanguages())){
             $_SESSION["filter_language"] = $_GET["filter_language"];
             $_SESSION["filter_parent"] = null;
             }
        
        
         if(!isset($_SESSION["filter_category"])){
             $_SESSION["filter_category"] = 0;
             }
        
        
         if(isset($_GET["filter_active"])){
             if($_GET["filter_active"] === "null")
                 $_SESSION["filter_active"] = null;
             else
                 $_SESSION["filter_active"] = intval($_GET["filter_active"]);
             }
        
         if(isset($_GET["filter_menu"])){
             if($_GET["filter_menu"] == "null")
                 $_SESSION["filter_menu"] = null;
             else
                 $_SESSION["filter_menu"] = $_GET["filter_menu"];
             }
        
        
         if(isset($_GET["filter_parent"])){
             if($_GET["filter_parent"] == "null")
                 $_SESSION["filter_parent"] = null;
             else
                 $_SESSION["filter_parent"] = $_GET["filter_parent"];
             }
        
        
        
         if(!isset($_SESSION["filter_parent"])){
             $_SESSION["filter_parent"] = null;
             }
        
         if(!isset($_SESSION["filter_menu"])){
             $_SESSION["filter_menu"] = null;
             }
        
        
         if(!isset($_SESSION["filter_active"])){
             $_SESSION["filter_active"] = null;
             }
        
        
         if(isset($_GET["filter_category"])){
             $_SESSION["filter_category"] = intval($_GET["filter_category"]);
            
             }
        
        
        
        
        
         if(!empty($_GET["filter_status"]) and in_array($_GET["filter_status"], array("Standard", "standard", "trash"))){
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
        
         $menus = getAllMenus();
         array_unshift($menus, "null");
        
         $sql = "select a.id as id, a.title as title from " . tbname("content") . " a inner join " . tbname("content") . " b on a.id = b.parent ";
        
         if(in_array($_SESSION["filter_language"], getAllLanguages())){
             $sql .= "where b.language='" . $_SESSION["filter_language"] . "' ";
             }
        
         $sql .= " group by a.title ";
         $sql .= " order by a.title";
         $parents = db_query($sql);
         ?>
         
</select>
<?php echo TRANSLATION_STATUS;
         ?> <select name="filter_status" onchange="filter_by_status(this)">
<option value="Standard" <?php
         if($_SESSION["filter_status"] == "standard"){
             echo " selected";
             }
         ?>><?php echo TRANSLATION_STANDARD;
         ?></option>
<option value="trash" <?php
         if($_SESSION["filter_status"] == "trash"){
             echo " selected";
             }
         ?>><?php echo TRANSLATION_RECYCLE_BIN;
         ?></option>
</select>
<?php echo TRANSLATION_CATEGORY;
         ?> 
<?php
         echo categories :: getHTMLSelect($_SESSION["filter_category"], true);
         ?><?php echo TRANSLATION_MENU;
         ?> 
         <select name="filter_menu" onchange="filter_by_menu(this);">

<?php
         foreach($menus as $menu){
             if($menu == "null")
                 $name = "[" . TRANSLATION_EVERY . "]";
             else
                 $name = $menu;
            
             if($menu == $_SESSION["filter_menu"])
                 echo '<option value="' . $menu . '" selected>' . $name . "</option>";
             else
                 echo '<option value="' . $menu . '">' . $name . "</option>";
            
             }
        
         ?>
         </select>
         <?php echo TRANSLATION_PARENT;
         ?> 
         <select name="filter_parent" onchange="filter_by_parent(this);">
         <option value="null" <?php if("null" == $_SESSION["filter_parent"]) echo "selected";
         ?>>[<?php echo TRANSLATION_EVERY;
         ?>]</option>
            <option value="-" <?php if("-" == $_SESSION["filter_parent"]) echo "selected";
         ?>>[<?php echo TRANSLATION_NONE;
         ?>]</option>
         <?php
        
         while($parent = db_fetch_object($parents)){
             $parent_id = $parent -> id;
             $title = htmlspecialchars($parent -> title);
             if($parent_id == $_SESSION["filter_parent"])
                 echo '<option value="' . $parent_id . '" selected>' . $title . "</option>";
             else
                 echo '<option value="' . $parent_id . '">' . $title . "</option>";
            
             }
         ?>
</select>
<?php echo TRANSLATION_ENABLED;
         ?> 
         <select name="filter_active" onchange="filter_by_active(this);">
         <option value="null" <?php if(null == $_SESSION["filter_active"]) echo "selected";
         ?>>[<?php echo TRANSLATION_EVERY;
         ?>]</option>
         <option value="1" <?php if(1 === $_SESSION["filter_active"]) echo "selected";
         ?>><?php echo TRANSLATION_ENABLED;
         ?></option>
         <option value="0" <?php if(0 === $_SESSION["filter_active"]) echo "selected";
         ?>><?php echo TRANSLATION_DISABLED;
         ?></option>
         </select>
</p>

<?php
         if($_SESSION["filter_status"] == "trash" and $acl -> hasPermission("pages")){
             ?>

&nbsp;&nbsp;
<a href="index.php?action=empty_trash" onclick="return confirm('Papierkorb leeren?');">Papierkorb leeren</a>
<?php }
         ?>

<br/>

<table class="tablesorter">
<thead>
<tr style="font-weight:bold;">
<th><?php echo TRANSLATION_TITLE;
         ?></th>
<th><?php echo TRANSLATION_MENU;
         ?></th>
<th><?php echo TRANSLATION_POSITION;
         ?></th>
<th><?php echo TRANSLATION_PARENT;
         ?></th>
<th><?php echo TRANSLATION_ACTIVATED;
         ?></th>
<td><?php echo TRANSLATION_VIEW;
         ?></td>
<td><?php echo TRANSLATION_EDIT;
         ?></td>
<td><?php echo TRANSLATION_DELETE;
         ?></td>

</tr>
</thead>
<tbody>
<?php
         if(in_array($_GET["order"], array("title", "menu", "position", "parent", "active")))
             $order = $_GET["order"];
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
        
        
         if($_SESSION["filter_menu"] != null){
             $filter_sql .= "AND menu = '" . db_escape($_SESSION["filter_menu"]) . "' ";
             }
        
        
         if($_SESSION["filter_active"] !== null){
             $filter_sql .= "AND active = " . intval($_SESSION["filter_active"]) . " ";
             }
        
        
         if($_SESSION["filter_parent"] != null){
             if($_SESSION["filter_parent"] != "-")
                 $filter_sql .= "AND parent = '" . intval($_SESSION["filter_parent"]) . "' ";
             else
                 $filter_sql .= "AND parent IS NULL ";
             }
        
        
         $query = db_query("SELECT * FROM " . tbname("content") . " " . $filter_sql . "ORDER BY $order,position, systemname ASC") or die(db_error());
         if(db_num_rows($query) > 0){
             while($row = db_fetch_object($query)){
                 ?>
<?php
                 echo '<tr>';
                 echo "<td>" . htmlspecialchars($row -> title);
                 if(!empty($row -> redirection) and !is_null($row -> redirection))
                     echo htmlspecialchars(" --> ") . htmlspecialchars($row -> redirection);
                
                 echo "</td>";
                 echo "<td>" . $row -> menu . "</td>";
                
                 echo "<td>" . $row -> position . "</td>";
                 echo "<td>" . htmlspecialchars(getPageTitleByID($row -> parent)) . "</td>";
                
                 if($row -> active){
                     echo "<td>" . TRANSLATION_YES . "</td>";
                     }
                else{
                     echo "<td>" . TRANSLATION_NO . "</td>";
                     }
                
                
                 if(startsWith($row -> redirection, "#")){
                     echo "<td style='text-align:center'></td>";
                     }else{
                     $domain = getDomainByLanguage($row -> language);
                     if(!$domain){
                         $url = "../" . $row -> systemname . ".html";
                         }else{
                         $url = "http://" . $domain . "/" . $row -> systemname . ".html";
                         }
                     echo "<td style='text-align:center'><a href=\"" . $url . "\" target=\"_blank\"><img class=\"mobile-big-image\" src=\"gfx/preview.png\" alt=\"" . TRANSLATION_VIEW . "\" title=\"" . TRANSLATION_VIEW . "\"></a></td>";
                    
                     }
                 echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_edit&page=' . $row -> id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . TRANSLATION_EDIT . '" title="' . TRANSLATION_EDIT . '"></a></td>';
                
                
                 if($_SESSION["filter_status"] == "trash"){
                     echo "<td style='text-align:center'>" . '<a href="index.php?action=undelete_page&page=' . $row -> id . '";"> <img class="mobile-big-image" src="gfx/undelete.png" alt="' . TRANSLATION_RECOVER . '" title="' . TRANSLATION_RECOVER . '"></a></td>';
                     }
                else
                    {
                     echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_delete&page=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif" class="mobile-big-image" alt="' . TRANSLATION_DELETE . '" title="' . TRANSLATION_DELETE . '"></a></td>';
                    
                    
                     }
                
                 echo '</tr>';
                
                 }
             ?>
<?php
             }
         ?>
</tbody>
</table>


<br/>

<?php
         }else{
         noperms();
         }
    
     ?>

<?php }
?>
