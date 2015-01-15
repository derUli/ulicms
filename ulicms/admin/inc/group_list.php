<?php
if(!defined("ULICMS_ROOT"))
     die("Dummer Hacker!");

if(isset($_REQUEST["standard"])){
     $standard = intval($_REQUEST["standard"]);
     setconfig("default_acl_group", $standard);
     }

$acl = new ACL();
$groups = $acl -> getAllGroups();

$default_acl_group = intval(getconfig("default_acl_group"));


if(isset($_REQUEST["sort"]) and in_array($_REQUEST["sort"], array("id", "name"))){
     $_SESSION["grp_sort"] = $_REQUEST["sort"];
     }

if($_SESSION["grp_sort"] == "id"){
     if($_SESSION["sortDirection"] == "asc")
         ksort($groups);
     else if($_SESSION["sortDirection"] == "asc")
         krsort($groups);
     }
else if($_SESSION["grp_sort"] == "name"){
     if($_SESSION["sortDirection"] == "asc")
         asort($groups);
     else
         arsort($groups);
     }else{
     ksort($groups);
     }

?>
<p><a href="?action=groups&add=add"><?php echo TRANSLATION_CREATE_GROUP;
?></a></p>
<?php if(count($groups) > 0){
     ?>
<table>
<tr>
<td style="min-width:100px;"><a href="?action=groups&sort=id&sort_direction=change"><strong><?php echo TRANSLATION_ID;
     ?></strong></a></td>
<td style="min-width:200px;"><a href="?action=groups&sort=name&sort_direction=change"><strong><?php echo TRANSLATION_NAME;
     ?></strong></a></td>
<td><strong><?php echo TRANSLATION_STANDARD;
     ?></strong></td>
<td></td>
<td></td>
</tr>

<?php foreach($groups as $id => $name){
         ?>
<tr>
<td><?php echo $id;
         ?></td>
<td><?php echo $name;
         ?></td>
<td>
<?php if($default_acl_group === $id){
             ?>
<span style="color:green; font-weight:bold;"><?php echo TRANSLATION_YES;
             ?></a>
<?php }else{
             ?>
<a href="?action=groups&standard=<?php echo $id;
             ?>"<span style="color:red; font-weight:bold;" onclick='return confirm("<?php echo str_ireplace("%name%", $name, TRANSLATION_MAKE_GROUP_DEFAULT);
             ?>")'><?php echo TRANSLATION_NO;
             ?></a>
<?php }
         ?>
</td>
<td><a href="?action=groups&edit=<?php echo $id;
         ?>"><img src="gfx/edit.gif" alt="<?php echo TRANSLATION_EDIT;
         ?>" title="<?php echo TRANSLATION_EDIT;
         ?>"></a></td>
<td><a href="?action=groups&delete=<?php echo $id;
         ?>" onclick="return confirm('<?php echo TRANSLATION_ASK_FOR_DELETE;
         ?>');"><img src="gfx/delete.gif" alt="<?php echo TRANSLATION_DELETE;
         ?>" title="<?php echo TRANSLATION_DELETE;
         ?>"></a></td>
</tr>



<?php }
     ?>

</table>
<?php }
?>