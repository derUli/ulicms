<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("banners")) {
    if (! isset($_SESSION["filter_category"])) {
        $_SESSION["filter_category"] = 0;
    }
    if (isset($_GET["filter_category"])) {
        $_SESSION["filter_category"] = intval($_GET["filter_category"]);
    }
    if ($_SESSION["filter_category"] == 0) {
        $banners = Banners::getAll();
    } else {
        $banners = Banners::getByCategory($_SESSION["filter_category"]);
    }
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("contents");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i>
		<?php translate("back")?></a>
</p>
<h2><?php translate("advertisements"); ?></h2>
<p>
<?php translate("advertisement_infotext");?>
	<?php
    if ($permissionChecker->hasPermission("banners_create")) {
        ?><br /> <br /> <a href="index.php?action=banner_new"
		class="btn btn-default"><i class="fa fa-plus"></i> <?php translate("add_advertisement");?>
	</a><br />
	<?php }?>
</p>
<p><?php translate("category");?>
<?php
    echo Categories::getHTMLSelect($_SESSION["filter_category"], true);
    ?>
</p>

<p><?php BackendHelper::formatDatasetCount(count ( $banners ));?></p>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr style="font-weight: bold;">
				<th><?php translate ( "advertisements" );?>
			</th>
				<th><?php translate("language");?>
			</th>
			<?php if ($permissionChecker->hasPermission ( "banners_edit" )) {?>
			<td><?php translate ( "edit" );?>
			</td>
				<td><?php translate ( "delete" );?>
			</td>
				<?php }?>
		</tr>
		</thead>
		<tbody>
	<?php
    if (count($banners) > 0) {
        foreach ($banners as $banner) {
            ?>
			<?php
            echo '<tr id="dataset-' . $banner->id . '">';
            if ($banner->getType() == "gif") {
                $link_url = Template::getEscape($banner->link_url);
                $image_url = Template::getEscape($banner->image_url);
                $name = Template::getEscape($banner->name);
                echo '<td><a href="' . $link_url . '" target="_blank"><img src="' . $image_url . '" title="' . $name . '" alt="' . $name . '" border=0></a></td>';
            } else {
                echo '<td>' . Template::getEscape($banner->html) . '</td>';
            }
            if (! $banner->language) {
                echo '<td>' . get_translation("every") . '</td>';
            } else {
                echo '<td>' . getLanguageNameByCode($banner->language) . "</td>";
            }
            if ($permissionChecker->hasPermission("banners_edit")) {
                echo "<td style='text-align:center;'>" . '<a href="index.php?action=banner_edit&banner=' . $banner->id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation("edit") . '" title="' . get_translation("edit") . '"></a></td>';
                echo "<td style='text-align:center;'>" . '<form action="index.php?sClass=BannerController&sMethod=delete&banner=' . $banner->id . '" method="post" class="delete-form">' . get_csrf_token_html() . '<input type="image" class="mobile-big-image" src="gfx/delete.gif" title="' . get_translation("delete") . '"></form></td>';
            }
            echo '</tr>';
        }
    }
    ?>
	</tbody>
	</table>
</div>
<?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_content", "js/banners.js"));
    combinedScriptHtml();
    ?>
<br />
<br />
<?php
} else {
    noPerms();
}

$translation = new JSTranslation(array(
    "ask_for_delete"
));
$translation->render();