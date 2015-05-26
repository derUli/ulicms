<?php
include_once ULICMS_ROOT."/classes/finediff.php";
include_once ULICMS_ROOT."/classes/vcs.php";
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ("pages")){
     $content_id = intval($_GET["content_id"]);
     $revisions = VCS::getRevisionsByContentID($content_id);
  ?>
<h1><?php translate("versions");?></h1>
<table class="tablesorter"     >
<thead>
<tr>
<th><?php translate("id");?></th>
<th><?php translate("content");?></th>
<th><?php translate("user");?></th>
<th><?php translate("date");?></th>
<th><?php translate("restore");?></th>
</tr>
</thead>
<tbody>
<?php foreach($revisions as $revision){
?>
<tr>
<td><?php echo intval($revision->id);?></td>
<td><?php translate("view_diff");?></td>
<td><?php getUserById($revision->autor_id);?></td>
<td><?php echo $revision->date;?></td>
<td><?php translate("restore");?></td>
</tr>
<?php }?>
</tbody>

</table>
     <?php
    } else {
      noperms();    
    }
    
    }
?>