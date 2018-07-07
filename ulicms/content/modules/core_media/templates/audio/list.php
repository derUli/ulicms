<?php
$acl = new ACL();
$audio_folder = ULICMS_DATA_STORAGE_ROOT . "/content/audio";
if (! is_dir($audio_folder)) {
    mkdir($audio_folder);
}

if (! isset($_SESSION["filter_category"])) {
    $_SESSION["filter_category"] = 0;
}

if (isset($_GET["filter_category"])) {
    $_SESSION["filter_category"] = intval($_GET["filter_category"]);
}

$sql = "SELECT id, name, mp3_file, ogg_file FROM " . tbname("audio") . " ";
if ($_SESSION["filter_category"] > 0) {
    $sql .= " where category_id = " . $_SESSION["filter_category"] . " ";
}
$sql .= " ORDER by id";

$all_audio = db_query($sql);

if ($acl->hasPermission("audio")) {
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("media");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1>
<?php
    
    translate("audio");
    ?>
</h1>
<?php translate("category");?>
<?php echo Categories::getHTMLSelect ( $_SESSION ["filter_category"], true );?>
<br />
<br />
<?php if($acl->hasPermission("audio_create")){?>
<p>
	<a href="index.php?action=add_audio" class="btn btn-default"><?php
        
        translate("upload_audio");
        ?></a>
</p>
<?php }?>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th><?php
    
    translate("id");
    ?>
			</th>
				<th><?php
    
    translate("name");
    ?>
			</th>
				<th class="hide-on-mobile"><?php
    
    translate("OGG_FILE");
    ?>
			</th>
				<th class="hide-on-mobile"><?php
    
    translate("MP3_FILE");
    ?>
			</th>

<?php if($acl->hasPermission("audio_edit")){?>
			<td></td>
				<td></td>
			<?php }?>
		</tr>

		</thead>
		<tbody>
	<?php
    while ($row = db_fetch_object($all_audio)) {
        ?>
		<tr id="dataset-<?php echo $row->id;?>">
				<td><?php
        
        echo $row->id;
        ?>
			</td>
				<td><?php
        
        echo htmlspecialchars($row->name);
        ?>
			</td>
				<td class="hide-on-mobile"><?php
        
        echo htmlspecialchars(basename($row->ogg_file));
        ?>
			</td>
				<td class="hide-on-mobile"><?php
        
        echo htmlspecialchars(basename($row->mp3_file));
        ?>
			</td>

	<?php if($acl->hasPermission("audio_edit")){?>
			<td><a
					href="index.php?action=edit_audio&id=<?php
            
            echo $row->id;
            ?>"><img src="gfx/edit.png" class="mobile-big-image"
						alt="<?php
            
            translate("edit");
            ?>"
						title="<?php
            
            translate("edit");
            ?>"> </a></td>
				<td><form
						action="?sClass=AudioController&sMethod=delete&delete=<?php echo $row->id;?>"
						method="post"
						onsubmit="return confirm('<?php translate ( "ASK_FOR_DELETE" );?>')"
						class="delete-form"><?php csrf_token_html();?><input type="image"
							src="gfx/delete.png" class="mobile-big-image"
							alt="<?php translate ( "delete" );?>"
							title="<?php translate ( "delete" );?>">
					</form></td>
				<?php }?>
		</tr>
		<?php }	?>
	</tbody>
	</table>
</div>
<?php
    enqueueScriptFile(ModuleHelper::buildModuleRessourcePath("core_media", "js/audio.js"));
    combinedScriptHtml();
    ?>
<?php
} else {
    noPerms();
}
