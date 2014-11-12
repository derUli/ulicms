<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("banners")){
        
         if(!isset($_SESSION["filter_category"]))
             $_SESSION["filter_category"] = 0;
        
        
        
         if(isset($_GET["filter_category"]))
             $_SESSION["filter_category"] = intval($_GET["filter_category"]);
        
        
        $sql = "SELECT * FROM " . tbname("banner") . " ";
        if($_SESSION["filter_category"] == 0)
             $sql .= "WHERE 1=1 ";
        else
             $sql .= "WHERE category=" . $_SESSION["filter_category"] . " ";
        
        $sql .= "ORDER BY id";
        $query = db_query($sql);
        
        ?>
<script type="text/javascript">
$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=banner&filter_category=" + valueSelected)
   
   });

});
</script>

<h2><?php echo TRANSLATION_ADVERTISEMENTS;?></h2>
<p>
<?php echo TRANSLATION_ADVERTISEMENT_INFOTEXT;?>
<br/><br/>
<a href="index.php?action=banner_new"><?php echo TRANSLATION_ADD_ADVERTISEMENT;?></a><br/>
</p>
<p><?php echo TRANSLATION_CATEGORY;?> 
<?php
        echo categories :: getHTMLSelect($_SESSION["filter_category"], true);
        ?>
</p>
<table border=1>

<tr style="font-weight:bold;">
<td style="width:40px;">--></td>
<td><?php echo TRANSLATION_ADVERTISEMENTS;?></td>
<td><?php echo TRANSLATION_EDIT;?></td>
<td><?php echo TRANSLATION_DELETE;?></td>
</tr>
<?php
        
         if(db_num_rows($query) > 0){
             while($row = db_fetch_object($query)){
                 ?>
<?php
                 echo '<tr>';
                 echo "<td style=\"width:40px;\">--></td>";
                 if($row->type == "gif"){
                 echo '<td><a href="' . $row -> link_url . '" target="_blank"><img src="' . $row -> image_url . '" title="' . $row -> name . '" alt="' . $row -> name . '" border=0></a></td>';
                 } else {
                 echo '<td>'.$row->html.'</td>';
                 }
                 echo "<td style='text-align:center;'>" . '<a href="index.php?action=banner_edit&banner=' . $row -> id . '"><img src="gfx/edit.gif" alt="'.TRANSLATION_EDIT.'" title="'.TRANSLATION_EDIT.'"></a></td>';
                 echo "<td style='text-align:center;'>" . '<a href="index.php?action=banner_delete&banner=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif" title="'.TRANSLATION_DELETE.'"></a></td>';
                 echo '</tr>';
                
                 }
            
             }
         ?>
</table>

<br/><br/>

<?php
        
         }
    else{
         noperms();
         }
    
     ?>




<?php }
?>
