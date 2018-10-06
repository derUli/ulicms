<?php
$permissionChecker = new ACL();
$groups = db_query("SELECT id, name from " . tbname("groups"));
if ($permissionChecker->hasPermission("pages") and $permissionChecker->hasPermission("pages_create")) {
    
    $allThemes = getThemesList();
    $cols = Database::getColumnNames("content");
    $sql = "SELECT id, name FROM " . tbname("videos");
    $videos = Database::query($sql);
    $sql = "SELECT id, name FROM " . tbname("audio");
    $audios = Database::query($sql);
    
    $pages_activate_own = $permissionChecker->hasPermission("pages_activate_own");
    
    $types = get_available_post_types();
    
    ?>
<div class="loadspinner">
	<img src="gfx/loading.gif" alt="Loading...">
</div>
<?php
    echo ModuleHelper::buildMethodCallForm("PageController", "create", array(), "post", array(
        "name" => "newpageform",
        "id" => "pageform",
        "style" => "display:none",
        "class" => "pageform"
    ));
    ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("pages");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<input type="hidden" name="add" value="add">

<div id="accordion-container">

	<h2 class="accordion-header"><?php translate("title_and_headline");?></h2>

	<div class="accordion-content">
		<strong><?php
    
    translate("permalink");
    ?>*
	</strong><br /> <input type="text" name="systemname" id="systemname"
			required="required" value=""> <br /> <strong><?php
    
    translate("page_title");
    ?>*
	</strong><br /> <input type="text" required="required"
			name="page_title" value="" onkeyup="suggestSystemname(this.value)">
		<div class="typedep hide-on-snippet hide-on-non-regular">
			<br /> <strong><?php translate ( "alternate_title" );?>
	</strong><br /> <input type="text" name="alternate_title" value=""> <small><?php translate ( "ALTERNATE_TITLE_INFO" );?>
	</small> <br /> <br /> <strong><?php translate("show_headline");?></strong>
			<br /> <select name="show_headline">
				<option value="1" selected><?php translate("yes");?></option>
				<option value="0"><?php translate("no");?></option>
			</select>
		</div>
	</div>
	<h2 class="accordion-header"><?php translate("type");?></h2>

	<div class="accordion-content">

<?php foreach($types as $type){?>
			<input type="radio" name="type" id="type_<?php echo $type;?>"
			value="<?php echo $type;?>"
			<?php if($type == "page") echo "checked";?>> <label
			for="type_<?php echo $type;?>"><?php translate($type);?></label> <br />
			<?php }?>

		</div>

	<h2 class="accordion-header"><?php translate("menu_entry");?></h2>

	<div class="accordion-content">
		<strong><?php translate("language");?>
	</strong> <br /> <select name="language">
	<?php
    $languages = getAllLanguages(true);
    if (! empty($_SESSION["filter_language"])) {
        $default_language = $_SESSION["filter_language"];
    } else {
        $default_language = Settings::get("default_language");
    }
    
    for ($j = 0; $j < count($languages); $j ++) {
        if ($languages[$j] === $default_language) {
            echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
        } else {
            echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
        }
    }
    
    $pages = getAllPages($default_language, "title", false);
    ?>
	</select><br /> <br />
		<div class="typedep menu-stuff">
			<strong><?php translate("menu");?>
	</strong> <span style="cursor: help;"
				onclick="$('div#menu_help').slideToggle()">[?]</span><br /> <select
				name="menu" size=1>
		<?php
    foreach (getAllMenus() as $menu) {
        ?>
		<option value="<?php echo $menu?>"
					<?php if($menu == "top") echo "selected";?>>
		<?php translate ( $menu );?></option>
		<?php
    }
    ?>
	</select>
			<div id="menu_help" class="help" style="display: none">
	<?php echo nl2br ( get_translation("help_menu") );?>
	</div>
			<br /> <br /> <strong><?php translate("position");?>
	</strong> <span style="cursor: help;"
				onclick="$('div#position_help').slideToggle()">[?]</span><br /> <input
				type="number" required="required" name="position" value="0" min="0"
				step="1">
			<div id="position_help" class="help" style="display: none">
	<?php echo nl2br ( get_translation ( "help_position" ) );?>
	</div>
			<br />
			<div id="parent-div">
				<strong><?php translate("parent");?></strong><br /> <select
					name="parent" size=1>
					<option selected="selected" value="NULL">
			[
			<?php translate("none");?>
			]
		</option>
		<?php
    
    foreach ($pages as $key => $page) {
        ?>
		<option value="<?php
        
        echo $page["id"];
        ?>">
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

		<div class="typedep" id="tab-target">

			<strong><?php
    
    translate("open_in");
    ?>
		</strong><br /> <select name="target" size=1>
				<option value="_self">
			<?php translate("target_self");?>
			</option>
				<option value="_blank">
			<?php translate("target_blank");?>
			</option>
			</select><br/><br/>
		</div>

		<strong><?php translate("activated");?>
	</strong><br /> <select name="activated" size=1
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
	</strong><br /> <select name="hidden" size="1"><option value="1">
		<?php translate("yes");?>
		</option>
				<option value="0" selected>
		<?php translate("no");?>
		</option>
			</select> <br /> <br />

		</div>
		<strong><?php translate("category");?>
	</strong><br />
	<?php echo Categories :: getHTMLSelect();?>
	<br /> <br /> <strong><?php translate("menu_image");?>
		</strong><br />

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
			value="" style="cursor: pointer" /> <a href="#"
			onclick="$('#menu_image').val('');return false;"><?php translate("clear");?>
		</a>
	</div>
	<div class="typedep" id="tab-link" style="display: none;">
		<h2 class="accordion-header"><?php translate("link_url");?></h2>

		<div class="accordion-content">
			<strong><?php translate("link_url");?>
		</strong><br /> <input type="text" name="redirection" value="">
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
				<option value="">[<?php translate("none");?>]</option>
<?php foreach($languages as $language){?>
<option value="<?php Template::escape($language->getID());?>"><?php Template::escape($language->getName());?></option>
<?php }?>
</select>
		</div>

	</div>
	<div class="typedep" id="tab-metadata">

		<h2 class="accordion-header"><?php translate("metadata");?></h2>

		<div class="accordion-content">
			<strong><?php translate("meta_description");?>
		</strong><br /> <input type="text" name="meta_description" value=''
				maxlength="200"> <br /> <strong><?php translate("meta_keywords");?>
		</strong><br /> <input type="text" name="meta_keywords" value=''
				maxlength="200">
			<div class="typedep" id="article-metadata">
				<br /> <strong><?php translate("author_name");?></strong><br /> <input
					type="text" name="article_author_name" value="" maxlength="80"> <br />
				<strong><?php translate("author_email");?></strong><br /> <input
					type="email" name="article_author_email" value="" maxlength="80"> <br />
				<div class="typedep" id="comment-fields">
					<strong><?php translate("homepage");?></strong><br /> <input
						type="url" name="comment_homepage" value="" maxlength="255"> <br />
					<br />
				</div>

				<strong><?php translate("article_date");?></strong><br /> <input
					name="article_date" type="datetime-local"
					value="<?php echo date ( "Y-m-d\TH:i:s" );?>" step="any"> <br /> <strong><?php translate("excerpt");?></strong>
				<textarea name="excerpt" id="excerpt" rows="5" cols="80"></textarea>
			</div>
			<div class="typedep" id="tab-og" style="display: none;">
				<h3><?php translate("open_graph");?></h3>

				<p><?php translate("og_help");?></p>
				<strong><?php translate("title");?>
		</strong><br /> <input type="text" name="og_title" value=""> <br /> <strong><?php translate("description");?>
		</strong><br /> <input type="text" name="og_description" value=""> <br />
				<strong><?php translate("type");?>
		</strong><br /> <input type="text" name="og_type" value=""> <br /> <strong><?php translate("image");?></strong>
				<br />
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
					value="<?php echo htmlspecialchars($og_image);?>"
					style="cursor: pointer" /> <a href="#"
					onclick="$('#og_image').val('');return false;"><?php translate("clear");?></a>

			</div>
		</div>
	</div>
	<div id="custom_fields_container">
		<?php
    foreach (DefaultContentTypes::getAll() as $name => $type) {
        $fields = $type->customFields;
        if (count($fields) > 0) {
            ?>
		<div class="custom-field-tab" data-type="<?php echo $name;?>">
			<h2 class="accordion-header"><?php translate($type->customFieldTabTitle ? $type->customFieldTabTitle : $name);?></h2>

			<div class="accordion-content">
		<?php
            
            foreach ($fields as $field) {
                $field->name = "{$name}_{$field->name}";
                ?>
		<?php echo $field->render(null);?>				
		<?php }?>
		</div>
		</div>
		<?php }?>
		
		<?php }?>
		</div>
	<div class="typedep" id="tab-list" style="display: none">
		<h2 class="accordion-header"><?php translate("list_properties");?></h2>

		<div class="accordion-content">
			<strong><?php translate("type")?></strong> <br />

						<?php $types = get_available_post_types();?>
<select name="list_type">
				<option value="null" selected>[<?php
    translate("every")?>]
		</option>
		<?php
    
    foreach ($types as $type) {
        echo '<option value="' . $type . '">' . get_translation($type) . "</option>";
    }
    ?>
	</select> <br /> <br /> <strong><?php translate("language");?>
	</strong> <br /> <select name="list_language">
				<option value="">[<?php translate("every");?>]</option>
	<?php
    $languages = getAllLanguages();
    
    for ($j = 0; $j < count($languages); $j ++) {
        echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
    }
    
    ?>
	</select> <br /> <br /> <strong><?php translate ( "category" );?>
	</strong><br />
	<?php echo Categories :: getHTMLSelect(-1, true, "list_category")?>
	<br /> <br /> <strong><?php
    
    translate("menu");
    ?>
	</strong><br /> <select name="list_menu" size=1>
				<option value="">[<?php translate("every");?>]</option>
		<?php
    foreach (getAllMenus() as $menu) {
        ?>
		<option value="<?php echo $menu;?>">
		<?php
        
        translate($menu);
        ?></option>
			<?php
    }
    ?>
			</select> <br /> <br /> <strong><?php translate("parent");?>
	</strong><br /> <select name="list_parent" size=1>
				<option selected="selected" value="NULL">
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
        ?>">
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
					<?php if($col == "title"){ echo 'selected';}?>><?php echo $col;?></option>
	<?php }?>
</select> <br /> <br /> <strong><?php
    translate("order_direction");
    ?>
	</strong> <select name="list_order_direction">
				<option value="asc"><?php translate("asc");?></option>
				<option value="desc"><?php translate("desc");?></option>
			</select> <br /> <br /> <strong><?php translate("limit");?></strong>
			<input type="number" min="0" name="limit" step="1" value="0"> <br />
			<strong><?php translate ( "use_pagination" );?></strong><br /> <select
				name="list_use_pagination">
				<option value="1"><?php translate("yes")?></option>
				<option value="0" selected><?php translate("no")?></option>
			</select>
		</div>
	</div>
	<div class="typedep" id="tab-module" style="display: none;">
		<h2 class="accordion-header"><?php translate("module");?></h2>

		<div class="accordion-content">
			<strong><?php translate("module");?></strong><br /> <select
				name="module">
				<option value="null">[<?php translate("none");?>]</option>
				<?php foreach(ModuleHelper::getAllEmbedModules() as $module){?>
				<option value="<?php echo $module;?>"><?php echo $module;?></option>
				<?php }?>
				</select>
		</div>

	</div>
	<div class="typedep" id="tab-video" style="display: none;">
		<h2 class="accordion-header"><?php translate("video");?></h2>

		<div class="accordion-content">
			<strong><?php translate("video");?></strong><br /> <select
				name="video">
				<option value="">[<?php translate("none");?>]</option>
				<?php while($row = Database::fetchObject($videos)){?>
				<option value="<?php echo $row->id;?>"><?php Template::escape($row->name);?> (ID: <?php echo $row->id;?>)</option>
				<?php }?>
				</select>
		</div>

	</div>
	<div class="typedep" id="tab-audio" style="display: none;">
		<h2 class="accordion-header"><?php translate("audio");?></h2>

		<div class="accordion-content">
			<strong><?php translate("audio");?></strong><br /> <select
				name="audio">
				<option value="">[<?php translate("none");?>]</option>
				<?php while($row = Database::fetchObject($audios)){?>
				<option value="<?php echo $row->id;?>"><?php Template::escape($row->name);?> (ID: <?php echo $row->id;?>)</option>
				<?php }?>
				</select>
		</div>

	</div>

	<div class="typedep" id="tab-image" style="display: none;">
		<h2 class="accordion-header"><?php translate("image");?></h2>

		<div class="accordion-content">
			<input type="text" id="image_url" name="image_url"
				readonly="readonly" onclick="openMenuImageSelectWindow(this)"
				value="" style="cursor: pointer" /> <a href="#"
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
				<option value="before"><?php translate("description_before_content")?></option>
				<option value="after"><?php translate("description_after_content")?></option>
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
				value="" style="cursor: pointer" maxlength="255" /> <a href="#"
				onclick="$('#article_image').val('');return false;"><?php translate("clear");?></a>
		</div>
	</div>
	<h2 class="accordion-header"><?php translate("other");?></h2>

	<div class="accordion-content">

		<div class="typedep" id="tab-cache-control" style="display: none;">

			<strong><?php translate("cache_control");?></strong> <br /> <select
				name="cache_control">
				<option value="auto" selected><?php translate("auto");?></option>
				<option value="force"><?php translate("force");?></option>
				<option value="no_cache"><?php translate("no_cache");?></option>
			</select> <br /> <br />
		</div>
		<div class="typedep" id="tab-menu-image">
			<strong><?php translate("design");?></strong><br /> <select
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
        ?>">
			<?php
        
        echo $th;
        ?></option>
			<?php
    }
    ?>
		</select>
		</div>
		<br /> <strong><?php translate("visible_for");?>
		</strong><br /> <select name="access[]" size=4 multiple>
			<option value="all" selected>
			<?php translate("everyone");?>
			</option>
			<option value="registered">
			<?php
    
    translate("registered_users");
    ?>
			</option>
			<option value="mobile"><?php translate("mobile_devices");?></option>
			<option value="desktop"><?php translate("desktop_computers");?></option>
			<?php
    while ($row = db_fetch_object($groups)) {
        echo '<option value="' . $row->id . '">' . real_htmlspecialchars($row->name) . '</option>';
    }
    ?>

		</select> <br /> <br />

		<div class="typedep" id="custom_data_json">
			
		<?php do_event("before_custom_data_json");?>
			<strong><?php translate("custom_data_json");?>
				<textarea name="custom_data" style="width: 100%; height: 200px;"
					cols=80 rows=10><?php esc(CustomData::getDefaultJSON());?></textarea>
		
		</div>
	</div>
</div>
<br />
<br />


<?php
    
    do_event("page_option");
    ?>

<div class="typedep" id="content-editor">
	<textarea name="page_content" id="page_content" cols=60 rows=20></textarea>
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
		return PageTranslation.ConfirmExitWithoutSave;
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
<?php
    }
    ?>
		<noscript>
		<p style="color: red;">
			Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a
				href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a>
		</p>
	</noscript>
</div>
<div class="inPageMessage"></div>
<input type="hidden" name="add_page" value="add_page">
<button type="submit" class="btn btn-primary"><?php translate("save");?></button>
<?php
	$translation = new JSTranslation(array(), "PageTranslation");
	$translation->addKey("confirm_exit_without_save");
	$translation->render();

    enqueueScriptFile("scripts/page.js");
    combinedScriptHtml();
    ?>
</form>
<?php
} else {
    noPerms();
}
 