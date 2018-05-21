<?php
include_once ULICMS_ROOT . "/classes/objects/content/VCS.php";
if (defined("_SECURITY")) {
    $acl = new ACL();
    if ($acl->hasPermission("pages") and $acl->hasPermission("pages_create")) {
        $page = intval($_GET["page"]);
        $query = db_query("SELECT * FROM " . tbname("content") . " WHERE id='$page'");
        
        $allThemes = getThemesList();
        $cols = Database::getColumnNames("content");
        
        $sql = "SELECT id, name FROM " . tbname("videos");
        $videos = Database::query($sql);
        
        $sql = "SELECT id, name FROM " . tbname("audio");
        $audios = Database::query($sql);
        
        $types = get_available_post_types();
        
        $groups = db_query("SELECT id, name from " . tbname("groups"));
        while ($row = db_fetch_object($query)) {
            $list_data = new List_Data($row->id);
            $pages_activate_own = $acl->hasPermission("pages_activate_own");
            ?>
		<?php
            echo ModuleHelper::buildMethodCallForm("PageController", "create", array(), "post", array(
                "id" => "editpageform",
                "name" => "editpageform"
            ));
            ?>
<input type="hidden" name="add" value="add">
<input type="hidden" name="edit_page" value="edit_page">
<input type="hidden" name="page_id" value="<?php echo $row -> id?>">
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("pages");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<div id="accordion-container">

	<h2 class="accordion-header"><?php translate("title_and_headline");?></h2>

	<div class="accordion-content">
		<strong><?php translate("permalink");?></strong><br /> <input
			type="text" required="required" name="system_title" id="system_title"
			value="">
		<div class="hide-on-snippet hide-on-non-regular">
			<br /> <strong><?php translate("page_title");?> </strong><br /> <input
				type="text" required="required" name="page_title" value=""
				onkeyup="systemname_vorschlagen(this.value)"> <br /> <strong><?php translate("alternate_title");?> </strong><br />
			<input type="text" name="alternate_title"
				value="<?php
            echo htmlspecialchars($row->alternate_title);
            
            ?>"><small><?php translate("ALTERNATE_TITLE_INFO");?> </small> <br />
			<br /> <strong><?php translate("show_headline");?></strong> <br /> <select
				name="show_headline">
				<option value="1"
					<?php if($row->show_headline == 1) echo "selected";?>><?php translate("yes");?></option>
				<option value="0"
					<?php if($row->show_headline == 0) echo "selected";?>><?php translate("no");?></option>
			</select>
		</div>
	</div>
	<h2 class="accordion-header"><?php translate("type");?></h2>

	<div class="accordion-content">
		<?php foreach($types as $type){?>
				<input type="radio" name="type" id="type_<?php echo $type;?>"
			value="<?php echo $type;?>"
			<?php if($type == $row->type) echo "checked";?>> <label
			for="type_<?php echo $type;?>"><?php translate($type);?></label> <br />
					<?php }?>

		</div>
	<h2 class="accordion-header"><?php translate("menu_entry");?></h2>

	<div class="accordion-content">
		<strong><?php translate("language");?></strong> <br /> <select
			name="language">
			<?php
            $languages = getAllLanguages(true);
            
            $page_language = $row->language;
            
            for ($j = 0; $j < count($languages); $j ++) {
                if ($languages[$j] === $page_language) {
                    echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                } else {
                    echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                }
            }
            
            $pages = getAllPages($page_language, "title", false);
            ?>
	</select> <br /> <br />
		<div class="typedep menu-stuff">
			<strong><?php translate("menu");?> </strong> <span
				style="cursor: help;" onclick="$('div#menu_help').slideToggle()">[?]</span><br />
			<select name="menu" size=1>
		<?php
            foreach (getAllMenus() as $menu) {
                ?>
		<option
					<?php
                
                if ($row->menu == $menu) {
                    echo 'selected="selected" ';
                }
                ?>
					value="<?php echo $menu?>">
			<?php
                
                translate($menu);
                ?>
		</option>
		<?php
            }
            ?>
	</select>
			<div id="menu_help" class="help" style="display: none">
	<?php
            
            echo nl2br(get_translation("help_menu"));
            ?>
	</div>
			<br /> <br /> <strong><?php translate("position");?> </strong> <span
				style="cursor: help;" onclick="$('div#position_help').slideToggle()">[?]</span><br />
			<input type="number" name="position" required min="0" step="1"
				value="<?php
            
            echo $row->position;
            ?>">

			<div id="position_help" class="help" style="display: none">
	<?php
            
            echo nl2br(get_translation("help_position"));
            ?>
	</div>

			<br />
			<div class="typedep" id="parent-div">
				<strong><?php translate("parent");?> </strong><br /> <select
					name="parent" size=1>
					<option value="NULL">
			[
			<?php translate("none");?>
			]
		</option>
		<?php
            
            foreach ($pages as $key => $page) {
                ?>
		<option value="<?php
                
                echo $page["id"];
                ?>"
						<?php
                
                if ($page["id"] == $row->parent) {
                    echo " selected='selected'";
                }
                ?>>
				<?php
                
                esc($page["title"]);
                ?>
			(ID:
			<?php
                
                echo $page["id"];
                ?>
			)
		</option>
		<?php
            }
            ?>
	</select> <br /> <br />
			</div>
		</div>
		<strong><?php translate("activated");?> </strong><br /> <select
			name="activated" size=1
			<?php if(!$pages_activate_own) echo "disabled";?>>
			<option value="1">
				<?php translate("enabled");?>
				</option>
			<option value="0" <?php if(!$pages_activate_own) echo "selected";?>>
				<?php translate("disabled");?>
				</option>
		</select> <br /> <br />

		<div class="typedep" id="hidden-attrib">
			<strong><?php translate("hidden");?>
	</strong><br /> <select name="hidden" size="1"><option value="1"
					<?php if($row->hidden == 1) echo "selected";?>>
		<?php translate("yes");?>
		</option>
				<option value="0" <?php if($row->hidden == 0) echo "selected";?>>
		<?php translate("no");?>
		</option>
			</select> <br /> <br />
		</div>
		<strong><?php translate("category");?> </strong><br />
	<?php
            echo Categories::getHTMLSelect($row->category);
            ?>
		</div>
	<div class="typedep" id="tab-link">
		<h2 class="accordion-header"><?php translate("link_url");?></h2>

		<div class="accordion-content">
			<strong><?php translate("link_url");?></strong><br /> <input
				type="text" name="redirection"
				value="<?php
            
            echo $row->redirection;
            ?>">
		</div>
	</div>
	<div class="typedep" id="tab-language-link" style="display: none;">
		<h2 class="accordion-header"><?php translate("language_link");?></h2>

		<div class="accordion-content">
			<strong><?php translate("language_link");?>
		</strong><br />
		<?php
            $languages = Language::getAllLanguages();
            ?>
<select name="link_to_language">
				<option value=""
					<?php if(is_null($row->link_to_language)){ echo "selected";}?>>[<?php translate("none");?>]</option>
<?php foreach($languages as $language){?>
<option value="<?php Template::escape($language->getID());?>"
					<?php if($language->getID() == $row->link_to_language) echo " selected";?>><?php Template::escape($language->getName());?></option>
<?php }?>
</select>
		</div>
	</div>
	<div class="typedep" id="tab-menu-image">
		<h2 class="accordion-header"><?php translate("menu_image");?> &amp; <?php translate("design");?></h2>

		<div class="accordion-content">
			<strong><?php translate("menu_image");?> </strong><br />

			<script type="text/javascript">
function openMenuImageSelectWindow(field) {
    window.KCFinder = {
        callBack: function(url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window.open('kcfinder/browse.php?type=images&dir=images&lang=<?php echo htmlspecialchars(getSystemLanguage());?>', 'menu_image',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
        'resizable=1, scrollbars=0, width=800, height=600'
    );
}
</script>
			<input type="text" id="menu_image" name="menu_image"
				readonly="readonly" onclick="openMenuImageSelectWindow(this)"
				value="<?php
            
            echo $row->menu_image;
            ?>"
				style="cursor: pointer" /> <a href="#"
				onclick="$('#menu_image').val('');return false;"><?php translate("clear");?> </a>
			<br /> <br /> <strong><?php translate("design");?></strong><br /> <select
				name="theme" size=1>
				<option value="">
				[
				<?php translate("standard");?>
				]
			</option>
			<?php
            
            foreach ($allThemes as $th) {
                ?>
			<option value="<?php
                
                echo $th;
                ?>"
					<?php
                
                if (! is_null($row->theme) and ! empty($row->theme) and $row->theme == $th) {
                    echo "selected";
                }
                ?>>
				<?php
                
                echo $th;
                ?>
			</option>
			<?php
            }
            ?>
		</select> <br /> <br /> <strong><?php translate("html_file");?></strong>
			<br /> <input type="text" name="html_file"
				value="<?php
            
            echo $row->html_file;
            ?>">
		</div>
	</div>
	<h2 class="accordion-header"><?php translate("visibility");?></h2>

	<div class="accordion-content">
		<strong><?php translate("visible_for");?> </strong><br />
			<?php
            
            $access = explode(",", $row->access);
            ?>
		<select name="access[]" size=4 multiple>
			<option value="all"
				<?php
            
            if (faster_in_array("all", $access)) {
                echo " selected";
            }
            ?>>
				<?php translate("everyone");?></option>
			<option value="registered"
				<?php if(faster_in_array("registered", $access)) {echo " selected";}?>>
				<?php translate("registered_users");?></option>


			<option value="mobile"
				<?php if(faster_in_array("mobile", $access)){ echo " selected";};?>><?php translate("mobile_devices");?></option>
			<option value="desktop"
				<?php if(faster_in_array("desktop", $access)){ echo " selected";}?>><?php translate("desktop_computers");?></option>
				<?php
            while ($row2 = db_fetch_object($groups)) {
                if (faster_in_array(strval($row2->id), $access)) {
                    echo '<option value="' . $row2->id . '" selected>' . real_htmlspecialchars($row2->name) . '</option>';
                } else {
                    echo '<option value="' . $row2->id . '">' . real_htmlspecialchars($row2->name) . '</option>';
                }
            }
            ?>
		</select>
	</div>

	<div class="typedep" id="tab-metadata" style="display: none">
		<h2 class="accordion-header"><?php translate("metadata");?></h2>

		<div class="accordion-content">
			<strong><?php translate("meta_description");?></strong><br /> <input
				type="text" name="meta_description"
				value="<?php
            echo htmlspecialchars($row->meta_description);
            ?>"
				maxlength="200"> <br /> <strong><?php translate("meta_keywords");?> </strong><br />
			<input type="text" name="meta_keywords"
				value="<?php
            echo htmlspecialchars($row->meta_keywords);
            ?>"
				maxlength="200">
			<div class="typedep" id="article-metadata">
				<br /> <strong><?php translate("author_name");?></strong><br /> <input
					type="text" name="article_author_name"
					value="<?php echo real_htmlspecialchars($row->article_author_name);?>"
					maxlength="80"> <br /> <br /> <strong><?php translate("author_email");?></strong><br />
				<input type="email" name="article_author_email"
					value="<?php echo real_htmlspecialchars($row->article_author_email);?>"
					maxlength="80"> <br /> <br />
				<div id="comment-fields">
					<strong><?php translate("homepage");?></strong><br /> <input
						type="url" name="comment_homepage"
						value="<?php echo real_htmlspecialchars($row->comment_homepage);?>"
						maxlength="255"> <br /> <br />
				</div>

				<strong><?php translate("article_date");?></strong><br /> <input
					name="article_date" type="datetime-local"
					value="<?php
            
            if (StringHelper::isNotNullOrEmpty($row->article_date)) {
                echo date("Y-m-d\TH:i:s", strtotime($row->article_date));
            }
            ?>"
					step="any"> <br /> <br /> <strong><?php translate("excerpt");?></strong>
				<textarea name="excerpt" rows="5" cols="80"><?php echo real_htmlspecialchars($row->excerpt);?></textarea>
			</div>
		</div>
	</div>
	<div class="typedep" id="custom_fields_container">
		<?php
            foreach (DefaultContentTypes::getAll() as $name => $type) {
                $fields = $type->customFields;
                if (count($fields) > 0) {
                    ?>
		<div class="custom-field-tab" data-type="<?php echo $name;?>">
			<h2 class="accordion-header"><?php translate($type->customFieldTabTitle ? $type->customFieldTabTitle : $name);?></h2>

			<div class="accordion-content">
		<?php foreach($fields as $field){?>
			<?php echo $field->render(CustomFields::get($field->name, $row->id));?>
		<?php }?>
		</div>
		</div>
		<?php }?>

		<?php }?>
		</div>

	<div class="typedep" id="tab-target">
		<h2 class="accordion-header"><?php translate("open_in");?></h2>

		<div class="accordion-content">
			<strong><?php translate("open_in");?></strong><br /> <select
				name="target" size=1>
				<option
					<?php
            
            if ($row->target == "_self") {
                echo 'selected="selected" ';
            }
            ?>
					value="_self">
				<?php translate("target_self");?></option>
				<option
					<?php
            
            if ($row->target == "_blank") {
                echo 'selected="selected" ';
            }
            ?>
					value="_blank">
				<?php translate("target_blank");?></option>
			</select>
		</div>
	</div>
	<div class="typedep" id="tab-cache-control" style="display: none;">
		<h2 class="accordion-header"><?php translate("cache_control");?></h2>

		<div class="accordion-content">
			<strong><?php translate("cache_control");?></strong> <br /> <select
				name="cache_control">
				<option value="auto" selected><?php translate("auto");?></option>
				<option value="force"><?php translate("force");?></option>
				<option value="no_cache"><?php translate("no_cache");?></option>
			</select>
		</div>
	</div>

	<div class="typedep" id="tab-og" style="display: none">
		<h2 class="accordion-header"><?php translate("open_graph");?></h2>

		<div class="accordion-content">

			<p><?php translate("og_help");?></p>
			<div style="margin-left: 20px;">
				<strong><?php translate("title");?>
		</strong><br /> <input type="text" name="og_title"
					value="<?php
            echo htmlspecialchars($row->og_title);
            ?>"> <br /> <strong><?php translate("description");?>
		</strong><br /> <input type="text" name="og_description"
					value="<?php
            echo htmlspecialchars($row->og_description);
            ?>"> <br /> <strong><?php translate("type");?>
		</strong><br /> <input type="text" name="og_type"
					value="<?php
            echo htmlspecialchars($row->og_type);
            ?>"> <br /> <strong><?php translate("image");?></strong> <br />
				<script type="text/javascript">
function openMenuImageSelectWindow(field) {
    window.KCFinder = {
        callBack: function(url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window.open('kcfinder/browse.php?type=images&dir=images&lang=<?php echo htmlspecialchars(getSystemLanguage());?>', 'og_image',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
        'resizable=1, scrollbars=0, width=800, height=600'
    );
}
</script>
				<input type="text" id="og_image" name="og_image" readonly="readonly"
					onclick="openMenuImageSelectWindow(this)"
					value="<?php
            echo htmlspecialchars($row->og_image);
            ?>"
					style="cursor: pointer" /> <a href="#"
					onclick="$('#og_image').val('');return false;"><?php translate("clear");?>
		</a>
		<?php
            if (! empty($row->og_image)) {
                $og_url = get_protocol_and_domain() . $row->og_image;
                ?>
<div style="margin-top: 15px;">
					<img class="small-preview-image"
						src="<?php
                
                echo htmlspecialchars($og_url);
                ?>" />
				</div>
<?php }?>
				</div>
		</div>
	</div>
	<div class="typedep" id="tab-list" class="list-show">
		<h2 class="accordion-header"><?php translate("list_properties");?></h2>

		<div class="accordion-content">
			<strong><?php translate("type")?></strong> <br />
			<?php $types = get_available_post_types();?>
				<select name="list_type">
				<option value="null"
					<?php
            
            if ("null" == $list_data->type) {
                echo "selected";
            }
            ?>>
			[<?php
            translate("every")?>]
		</option>
		<?php
            foreach ($types as $type) {
                if ($type == $list_data->type) {
                    echo '<option value="' . $type . '" selected>' . get_translation($type) . "</option>";
                } else {
                    echo '<option value="' . $type . '">' . get_translation($type) . "</option>";
                }
            }
            ?>
	</select> <br /> <br /> <strong><?php translate("language");?>
	</strong> <br /> <select name="list_language">
				<option value=""
					<?php if($list->language === "null") echo "selected";?>>[<?php translate("every");?>]</option>
	<?php
            $languages = getAllLanguages();
            
            for ($j = 0; $j < count($languages); $j ++) {
                if ($list_data->language === $languages[$j]) {
                    echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                } else {
                    echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                }
            }
            
            ?>
	</select> <br /> <br /> <strong><?php
            
            translate("category");
            ?>
	</strong><br />
	<?php
            
            $lcat = $list_data->category_id;
            if ($lcat === null) {
                $lcat = - 1;
            }
            ?>
	<?php echo Categories :: getHTMLSelect($lcat, true, "list_category")?>
	<br /> <br /> <strong><?php
            
            translate("menu");
            ?>
	</strong><br /> <select name="list_menu" size=1>
				<option value="">[<?php translate("every");?>]</option>
		<?php
            foreach (getAllMenus() as $menu) {
                ?>
				<option value="<?php echo $menu?>"
					<?php if($menu == $list_data->menu) echo "selected"?>>

		<?php
                
                translate($menu);
                ?></option>
			<?php
            }
            ?>
			</select> <br /> <br /> <strong><?php translate("parent")?>
	</strong><br /> <select name="list_parent" size=1>
				<option
					<?php
            
            if ($list_data->parent_id === null) {
                echo 'selected="selected"';
            }
            ?>
					value="NULL">
			[
			<?php
            
            translate("every");
            ?>
			]
		</option>
		<?php
            
            foreach ($pages as $key => $page) {
                ?>
		<option value="<?php
                
                echo $page["id"];
                ?>"
					<?php
                
                if ($list_data->parent_id === $page["id"]) {
                    echo 'selected="selected"';
                }
                ?>>
			<?php
                
                esc($page["title"]);
                ?>
			(ID:
			<?php
                
                echo $page["id"];
                ?>
			)
		</option>
		<?php
            }
            ?>
	</select> <br /> <br /> <strong><?php
            translate("order_by");
            ?>
	</strong> <br /> <select name="list_order_by">
	<?php foreach($cols as $col){?>
	<option value="<?php echo $col;?>"
					<?php if($col == $list_data->order_by) echo 'selected';?>><?php echo $col;?></option>
	<?php }?>
</select> <br /> <br /> <strong><?php
            translate("order_direction");
            ?>
	</strong> <select name="list_order_direction">
				<option value="asc"><?php translate("asc");?></option>
				<option value="desc"
					<?php if($list_data->order_direction=== "desc") echo ' selected';?>><?php translate("desc");?></option>
			</select> <br /> <br /> <strong><?php translate("limit");?></strong>
			<input type="number" name="limit" min="0" step="1"
				value="<?php echo intval($list_data->limit);?>"> <br /> <strong><?php translate ( "use_pagination" );?></strong><br />
			<select name="list_use_pagination">
				<option value="1"
					<?php if($list_data->use_pagination) echo "selected";?>><?php translate("yes")?></option>
				<option value="0"
					<?php if(!$list_data->use_pagination) echo "selected";?>><?php translate("no")?></option>
			</select>
		</div>
	</div>
	<div class="typedep" id="tab-module" style="display: none;">
		<h2 class="accordion-header"><?php translate("module");?></h2>

		<div class="accordion-content">
			<strong><?php translate("module");?></strong><br /> <select
				name="module">
				<option value="null"
					<?php if($module == null or empty($module)) echo " selected";?>>[<?php translate("none");?>]</option>
				<?php foreach(ModuleHelper::getAllEmbedModules() as $module){?>
				<option value="<?php echo $module;?>"
					<?php if($module == $row->module) echo " selected";?>><?php echo $module;?></option>
				<?php }?>
				</select>
		</div>

	</div>

	<div class="typedep" id="tab-video" style="display: none;">
		<h2 class="accordion-header"><?php translate("video");?></h2>

		<div class="accordion-content">
			<strong><?php translate("video");?></strong><br /> <select
				name="video">
				<option value=""
					<?php if($row->video == null or empty($row->video)) echo " selected";?>>[<?php translate("none");?>]</option>
				<?php while($row5 = Database::fetchObject($videos)){?>
				<option value="<?php echo $row5->id;?>"
					<?php if($row5->id == $row->video) echo " selected";?>><?php Template::escape($row5->name);?> (ID: <?php echo $row5->id;?>)</option>
				<?php }?>
				</select>
		</div>
	</div>

	<div class="typedep" id="tab-audio" style="display: none;">
		<h2 class="accordion-header"><?php translate("audio");?></h2>

		<div class="accordion-content">
			<strong><?php translate("audio");?></strong><br /> <select
				name="audio">
				<option value=""
					<?php if($row->audio == null or empty($row->audio)) echo " selected";?>>[<?php translate("none");?>]</option>
				<?php while($row5 = Database::fetchObject($audios)){?>
				<option value="<?php echo $row5->id;?>"
					<?php if($row5->id == $row->audio) echo " selected";?>><?php Template::escape($row5->name);?> (ID: <?php echo $row5->id;?>)</option>
				<?php }?>
				</select>
		</div>

	</div>
	<div class="typedep" id="tab-image" style="display: none;">
		<h2 class="accordion-header"><?php translate("image");?></h2>

		<div class="accordion-content">
			<input type="text" id="image_url" name="image_url"
				readonly="readonly" onclick="openMenuImageSelectWindow(this)"
				value="<?php Template::escape($row->image_url);?>"
				style="cursor: pointer" /> <a href="#"
				onclick="$('#menu_image').val('');return false;"><?php
            
            translate("clear");
            ?>
		</a>
		</div>
	</div>
	<div class="typedep" id="tab-text-position" style="display: none">
		<h2 class="accordion-header"><?php translate("position_of_description");?></h2>
		<div class="accordion-content">
			<strong><?php translate("position_of_description");?>
					</strong> <br /> <select name="text_position">
				<option value="before"
					<?php if($row->text_position == "before") echo "selected";?>><?php translate("description_before_content")?></option>
				<option value="after"
					<?php if($row->text_position == "after") echo "selected";?>><?php translate("description_after_content")?></option>
			</select>

		</div>
	</div>

	<div class="typedep" id="article-image">
		<h2 class="accordion-header"><?php translate("article_image");?></h2>

		<div class="accordion-content">
			<strong><?php translate("article_image");?>
		</strong><br />

			<script type="text/javascript">
function openArticleImageSelectWindow(field) {
    window.KCFinder = {
        callBack: function(url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window.open('kcfinder/browse.php?type=images&dir=images&lang=<?php echo htmlspecialchars(getSystemLanguage());?>', 'article_image',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
        'resizable=1, scrollbars=0, width=800, height=600'
    );
}
</script>
			<input type="text" id="article_image" name="article_image"
				readonly="readonly" onclick="openArticleImageSelectWindow(this)"
				value="<?php echo real_htmlspecialchars($row->article_image);?>"
				style="cursor: pointer" maxlength="255" /><br /> <a href="#"
				onclick="$('#article_image').val('');return false;"><?php translate("clear");?></a>
		</div>
	</div>

	<div class="typedep" id="custom_data_json"><?php add_hook("before_custom_data_json");?>
		<h2 class="accordion-header"><?php translate("custom_data_json");?></h2>
		<div class="accordion-content">

			<textarea name="custom_data" style="width: 100%; height: 200px;"
				cols=80 rows=10><?php
            
            echo htmlspecialchars($row->custom_data);
            ?></textarea>
		</div>

	</div>
</div>
<br />
<br />
<?php
            add_hook("page_option");
            ?>
<div class="typedep" id="content-editor">
	<textarea name="page_content" id="page_content" cols=60 rows=20><?php
            
            echo htmlspecialchars($row->content);
            ?></textarea>
		<?php
            $editor = get_html_editor();
            ?>

		<?php
            
            if ($editor === "ckeditor") {
                ?>
		<script type="text/javascript">
var editor = CKEDITOR.replace( 'page_content',
					{
						skin : '<?php
                
                echo Settings::get("ckeditor_skin");
                ?>'
					});


var editor2 = CKEDITOR.replace( 'excerpt',
		{
			skin : '<?php
                
                echo Settings::get("ckeditor_skin");
                ?>'
		});

editor.on("instanceReady", function()
{
	this.document.on("keyup", CKCHANGED);
	this.document.on("paste", CKCHANGED);
}
);


editor2.on("instanceReady", function()
		{
			this.document.on("keyup", CKCHANGED);
			this.document.on("paste", CKCHANGED);
		}

);

function CKCHANGED() {
	formchanged = 1;
}

var formchanged = 0;
var submitted = 0;

$(document).ready(function() {
$("#extra_options").hide();
	$('form').each(function(i,n){
		$('input', n).change(function(){formchanged = 1});
		$('textarea', n).change(function(){formchanged = 1});
		$('select', n).change(function(){formchanged = 1});
		$(n).submit(function(){submitted=1});
	});
});

window.onbeforeunload = confirmExit;
function confirmExit()
{
	if(formchanged == 1 && submitted == 0)
		return "Wenn Sie diese Seite verlassen gehen nicht gespeicherte Änderungen verloren.";
	else
		return;
}
</script>
<?php
            } else if ($editor == "codemirror") {
                ?>
		<script type="text/javascript">
var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("page_content"),

{lineNumbers: true,
        matchBrackets: true,
        mode : "text/html",

        indentUnit: 0,
        indentWithTabs: false,
        enterMode: "keep",
        tabMode: "shift"});


var myCodeMirror2 = CodeMirror.fromTextArea(document.getElementById("excerpt"),

		{lineNumbers: true,
		        matchBrackets: true,
		        mode : "text/html",

		        indentUnit: 0,
		        indentWithTabs: false,
		        enterMode: "keep",
		        tabMode: "shift"});
</script>
<?php }?>
		<noscript>
		<p style="color: red;">
			Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a
				href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a>
		</p>

	</noscript>
		<?php
            
            $rev = vcs::getRevisionsByContentID($row->id);
            if (count($rev) > 0) {
                ?>
		<p>
		[<a
			href="index.php?action=restore_version&content_id=<?php echo $row->id;?>"><?php translate("restore_older_version");?></a>]
	</p>
		<?php }?>	</div>
<div class="inPageMessage">
	<div id="message_page_edit" class="inPageMessage"></div>
	<img class="loading" src="gfx/loading.gif" alt="Wird gespeichert...">
</div>

<button type="submit" class="btn btn-success"><?php translate("save_changes");?></button>
<script src="scripts/page.js" type="text/javascript">
</script>
<input type="hidden" name="add_page" value="add_page">
</form>

<?php
            break;
        }
        ?>
		<?php
    } else {
        noperms();
    }
    ?>

<?php
}
?>
