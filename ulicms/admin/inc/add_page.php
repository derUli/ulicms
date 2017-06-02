	<?php
	if (defined ( "_SECURITY" )) {
		$acl = new ACL ();
		$groups = db_query ( "SELECT id, name from " . tbname ( "groups" ) );
		if ($acl->hasPermission ( "pages" ) and $acl->hasPermission ( "pages_create" )) {
			
			$allThemes = getThemesList ();
			$cols = Database::getColumnNames ( "content" );
			$sql = "SELECT id, name FROM " . tbname ( "videos" );
			$videos = Database::query ( $sql );
			
			$sql = "SELECT id, name FROM " . tbname ( "audio" );
			$audios = Database::query ( $sql );
			
			$pages_activate_own = $acl->hasPermission ( "pages_activate_own" );
			
			$types = get_available_post_types ();
			
			?>
<form id="pageform" name="newpageform" action="index.php?action=pages"
	method="post">

	<input type="hidden" name="add" value="add">
	<?php csrf_token_html ();?>

<div id="accordion-container">

		<h2 class="accordion-header"><?php translate("title_and_headline");?></h2>

		<div class="accordion-content">
			<strong><?php
			
			translate ( "permalink" );
			?>
	</strong><br /> <input type="text" name="system_title"
				id="system_title" required="required" value=""> <br /> <br /> <strong><?php
			
			translate ( "page_title" );
			?>
	</strong><br /> <input type="text" required="required"
				name="page_title" value=""
				onkeyup="systemname_vorschlagen(this.value)"> <br /> <br /> <strong><?php
			
			translate ( "alternate_title" );
			?>
	</strong><br /> <input type="text" name="alternate_title" value=""><br />
			<small><?php translate ( "ALTERNATE_TITLE_INFO" );?>
	</small> <br /> <br /> <strong><?php translate("show_headline");?></strong>
			<br /> <select name="show_headline">
				<option value="1" selected><?php translate("yes");?></option>
				<option value="0"><?php translate("no");?></option>
			</select>
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
			$languages = getAllLanguages ( true );
			if (! empty ( $_SESSION ["filter_language"] )) {
				$default_language = $_SESSION ["filter_language"];
			} else {
				$default_language = Settings::get ( "default_language" );
			}
			
			for($j = 0; $j < count ( $languages ); $j ++) {
				if ($languages [$j] === $default_language) {
					echo "<option value='" . $languages [$j] . "' selected>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
				} else {
					echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
				}
			}
			
			$pages = getAllPages ( $default_language, "title", false );
			?>
	</select><br /> <br /> <strong><?php translate("menu");?>
	</strong> <span style="cursor: help;"
				onclick="$('div#menu_help').slideToggle()">[?]</span><br /> <select
				name="menu" size=1>
		<?php
			foreach ( getAllMenus () as $menu ) {
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
			<br /> <br /> <strong><?php translate("parent");?></strong><br /> <select
				name="parent" size=1>
				<option selected="selected" value="NULL">
			[
			<?php translate("none");?>
			]
		</option>
		<?php
			
			foreach ( $pages as $key => $page ) {
				?>
		<option value="<?php
				
				echo $page ["id"];
				?>">
			<?php
				
				echo $page ["title"];
				?>
			(ID:
			<?php
				
				echo $page ["id"];
				?>
			)
		</option>
		<?php
			}
			?>
	</select> <br /> <br /> <strong><?php translate("activated");?>
	</strong><br /> <select name="activated" size=1
				<?php if(!$pages_activate_own) echo "disabled";?>>
				<option value="1">
		<?php translate("enabled");?>
		</option>
				<option value="0" <?php if(!$pages_activate_own) echo "selected";?>>
		<?php translate("disabled");?>
		</option>
			</select> <br /> <br />
			<div id="hidden-attrib">
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
	<?php echo categories :: getHTMLSelect();?>
	
		</div>
		<div id="tab-link" style="display: none;">
			<h2 class="accordion-header"><?php translate("external_redirect");?></h2>

			<div class="accordion-content">
				<strong><?php translate("EXTERNAL_REDIRECT");?>
		</strong><br /> <input type="text" name="redirection" value="">
			</div>
		</div>

		<h2 class="accordion-header"><?php translate("menu_image");?> &amp; <?php translate("design");?></h2>

		<div class="accordion-content">
			<strong><?php translate("menu_image");?>
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
				value="" style="cursor: pointer" /><br /> <a href="#"
				onclick="$('#menu_image').val('');return false;"><?php translate("clear");?>
		</a> <br /> <br /> <strong><?php translate("design");?></strong><br />
			<select name="theme" size=1>
				<option value="">
				[
				<?php translate("standard");?>
				]
			</option>
			<?php
			
			foreach ( $allThemes as $th ) {
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
		</select> <br /> <br /> <strong><?php translate("html_file");?>
		</strong> <br /> <input type="text" name="html_file" value="">
		</div>
		<h2 class="accordion-header"><?php translate("visibility");?></h2>

		<div class="accordion-content">
			<strong><?php translate("visible_for");?>
		</strong><br /> <select name="access[]" size=4 multiple>
				<option value="all" selected>
			<?php translate("everyone");?>
			</option>
				<option value="registered">
			<?php
			
			translate ( "registered_users" );
			?>
			</option>
				<option value="mobile"><?php translate("mobile_devices");?></option>
				<option value="desktop"><?php translate("desktop_computers");?></option>
			<?php
			while ( $row = db_fetch_object ( $groups ) ) {
				echo '<option value="' . $row->id . '">' . real_htmlspecialchars ( $row->name ) . '</option>';
			}
			?>

		</select>
		</div>
		<div id="tab-metadata">

			<h2 class="accordion-header"><?php translate("metadata");?></h2>

			<div class="accordion-content">
				<strong><?php translate("meta_description");?>
		</strong><br /> <input type="text" name="meta_description" value=''> <br />
				<br /> <strong><?php translate("meta_keywords");?>
		</strong><br /> <input type="text" name="meta_keywords" value=''>
				<div id="article-metadata">
					<br /> <strong><?php translate("author_name");?></strong><br /> <input
						type="text" name="article_author_name" value="" maxlength="80"> <br />
					<br /> <strong><?php translate("author_email");?></strong><br /> <input
						type="email" name="article_author_email" value="" maxlength="80">
					<br /> <br />
					<div id="comment-fields">
						<strong><?php translate("homepage");?></strong><br /> <input
							type="url" name="comment_homepage" value="" maxlength="255"> <br />
						<br />
					</div>

					<strong><?php translate("article_date");?></strong><br /> <input
						name="article_date" type="datetime-local"
						value="<?php echo date ( "Y-m-d\TH:i:s" );?>" step=any> <br /> <br />
					<strong><?php translate("excerpt");?></strong>
					<textarea name="excerpt" id="excerpt" rows="5" cols="80"></textarea>
				</div>
			</div>

		</div>
		<div id="custom_fields_container">
		<?php
			
			foreach ( $types as $type ) {
				$fields = getFieldsForCustomType ( $type );
				if (count ( $fields ) > 0) {
					?>
		<div class="custom-field-tab" data-type="<?php echo $type;?>">
				<h2 class="accordion-header"><?php translate($type);?></h2>

				<div class="accordion-content">
		<?php foreach($fields as $field){?>
		<p>
						<strong><?php translate($field);?></strong> <br /> <input
							type="text"
							name="cf_<?php echo Template::escape($type);?>_<?php echo Template::escape($field);?>"
							value="">
					</p>					
		<?php }?>
		</div>
			</div>
		<?php }?>
		
		<?php }?>
		</div>
		<h2 class="accordion-header"><?php translate("open_in");?></h2>

		<div class="accordion-content">
			<strong><?php
			
			translate ( "open_in" );
			?>
		</strong><br /> <select name="target" size=1>
				<option value="_self">
			<?php translate("target_self");?>
			</option>
				<option value="_blank">
			<?php translate("target_blank");?>
			</option>
			</select>
		</div>
		<div id="tab-og" style="display: none;">
			<h2 class="accordion-header"><?php translate("open_graph");?></h2>

			<div class="accordion-content">
				<p><?php translate("og_help");?></p>
				<div style="margin-left: 20px;">
					<strong><?php translate("title");?>
		</strong><br /> <input type="text" name="og_title" value=""> <br /> <br />

					<strong><?php translate("description");?>
		</strong><br /> <input type="text" name="og_description" value=""> <br />
					<br /> <strong><?php translate("type");?>
		</strong><br /> <input type="text" name="og_type" value=""> <br /> <br />
					<strong><?php translate("image");?></strong> <br />
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
					<input type="text" id="og_image" name="og_image"
						readonly="readonly" onclick="openMenuImageSelectWindow(this)"
						value="<?php echo htmlspecialchars($og_image);?>"
						style="cursor: pointer" /><br /> <a href="#"
						onclick="$('#og_image').val('');return false;"><?php translate("clear");?></a>
				</div>
			</div>
		</div>
		<div id="tab-cache-control" style="display: none;">
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

		<div id="tab-list" style="display: none">
			<h2 class="accordion-header"><?php translate("list_properties");?></h2>

			<div class="accordion-content">
				<strong><?php translate("type")?></strong> <br />

						<?php $types = get_available_post_types();?>
<select name="list_type">
					<option value="null" selected>[<?php
			translate ( "every" )?>]
		</option>
		<?php
			
			foreach ( $types as $type ) {
				echo '<option value="' . $type . '">' . get_translation ( $type ) . "</option>";
			}
			?>
	</select> <br /> <br /> <strong><?php translate("language");?>
	</strong> <br /> <select name="list_language">
					<option value="">[<?php translate("every");?>]</option>
	<?php
			$languages = getAllLanguages ();
			
			for($j = 0; $j < count ( $languages ); $j ++) {
				echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
			}
			
			?>
	</select> <br /> <br /> <strong><?php translate ( "category" );?>
	</strong><br />
	<?php echo categories :: getHTMLSelect(-1, true, "list_category")?>
	<br /> <br /> <strong><?php
			
			translate ( "menu" );
			?>
	</strong><br /> <select name="list_menu" size=1>
					<option value="">[<?php translate("every");?>]</option>
		<?php
			foreach ( getAllMenus () as $menu ) {
				?>
		<option value="<?php echo $menu;?>">
		<?php
				
				translate ( $menu );
				?></option>
			<?php
			}
			?>
			</select> <br /> <br /> <strong><?php translate("parent");?>
	</strong><br /> <select name="list_parent" size=1>
					<option selected="selected" value="NULL">
			[
			<?php
			
			translate ( "every" );
			?>
			]
		</option>
		<?php
			
			foreach ( $pages as $key => $page ) {
				?>
		<option value="<?php
				
				echo $page ["id"];
				?>">
			<?php
				
				echo $page ["title"];
				?>
			(ID:
			<?php
				
				echo $page ["id"];
				?>
			)
		</option>
		<?php
			}
			?>
	</select> <br /> <br /> <strong><?php
			translate ( "order_by" );
			?>
	</strong> <br /> <select name="list_order_by">
	<?php foreach($cols as $col){?>
	<option value="<?php echo $col;?>"
						<?php if($col == "title"){ echo 'selected';}?>><?php echo $col;?></option>
	<?php }?>
</select> <br /> <br /> <strong><?php
			translate ( "order_direction" );
			?>
	</strong> <select name="list_order_direction">
					<option value="asc"><?php translate("asc");?></option>
					<option value="desc"><?php translate("desc");?></option>
				</select> <br /> <br /> <strong><?php translate("limit");?></strong>
				<input type="number" min="0" name="limit" step="1" value="0"> <br />
				<br /> <strong><?php translate ( "use_pagination" );?></strong><br />
				<select name="list_use_pagination">
					<option value="1"><?php translate("yes")?></option>
					<option value="0" selected><?php translate("no")?></option>
				</select>


			</div>
		</div>

		<div id="tab-module" style="display: none;">
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
		<div id="tab-video" style="display: none;">
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
		<div id="tab-audio" style="display: none;">
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

		<div id="tab-image" style="display: none;">
			<h2 class="accordion-header"><?php translate("image");?></h2>

			<div class="accordion-content">
				<input type="text" id="image_url" name="image_url"
					readonly="readonly" onclick="openMenuImageSelectWindow(this)"
					value="" style="cursor: pointer" /><br /> <a href="#"
					onclick="$('#menu_image').val('');return false;"><?php
			
			translate ( "clear" );
			?>
		</a>
			</div>
		</div>
		<div id="tab-text-position" style="display: none">
			<h2 class="accordion-header"><?php translate("position_of_description");?></h2>
			<div class="accordion-content">
				<strong><?php translate("position_of_description");?>
					</strong> <br /> <select name="text_position">
					<option value="before"><?php translate("description_before_content")?></option>
					<option value="after"><?php translate("description_after_content")?></option>
				</select>

			</div>
		</div>
		<div id="article-image">
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
					value="" style="cursor: pointer" maxlength="255" /><br /> <a
					href="#" onclick="$('#article_image').val('');return false;"><?php translate("clear");?></a>
			</div>
		</div>
		<?php add_hook("before_custom_data_json");?>
		<h2 class="accordion-header"><?php translate("custom_data_json");?></h2>

		<div class="accordion-content">


			<textarea name="custom_data" style="width: 100%; height: 200px;"
				cols=80 rows=10>{}</textarea>

		</div>
	</div>
	<br /> <br />


	<?php
			
			add_hook ( "page_option" );
			?>

	<div id="content-editor">
		<textarea name="page_content" id="page_content" cols=60 rows=20></textarea>
		<?php
			$editor = get_html_editor ();
			?>

		<?php
			
			if ($editor === "ckeditor") {
				?>
		<script type="text/javascript">
var editor = CKEDITOR.replace( 'page_content',
					{
						skin : '<?php
				
				echo Settings::get ( "ckeditor_skin" );
				?>'
					});
var editor2 = CKEDITOR.replace( 'excerpt',
		{
			skin : '<?php
				
				echo Settings::get ( "ckeditor_skin" );
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
	<input type="hidden" name="add_page" value="add_page"> <input
		type="submit" value="<?php translate("save");?>">
	<?php
			if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
				?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>

	<?php
			}
			?>

	<script src="scripts/page.js" type="text/javascript">
</script>

</form>
<?php
		} else {
			noperms ();
		}
	}
