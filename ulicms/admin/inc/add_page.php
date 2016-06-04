<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	$groups = db_query ( "SELECT id, name from " . tbname ( "groups" ) );
	if ($acl->hasPermission ( "pages" )) {
		
		$allThemes = getThemesList ();
		$cols = Database::getColumnNames ( "content" );
		?>
<form id="pageform" name="newpageform" action="index.php?action=pages"
	method="post">
	<input type="hidden" name="add" value="add">
	<?php
		
		csrf_token_html ();
		?>
		
<div id="accordion-container">

		<h2 class="accordion-header"><?php translate("title_and_headline");?></h2>

		<div class="accordion-content">
			<strong><?php
		
		translate ( "permalink" );
		?>
	</strong><br /> <input type="text" name="system_title"
				id="system_title" required="true" value=""> <br /> <br /> <strong><?php
		
		translate ( "page_title" );
		?>
	</strong><br /> <input type="text" required="true" name="page_title"
				value="" onkeyup="systemname_vorschlagen(this.value)"> <br /> <br />
			<strong><?php
		
		translate ( "alternate_title" );
		?>
	</strong><br /> <input type="text" name="alternate_title" value=""><br />
			<small><?php
		
		echo TRANSLATION_ALTERNATE_TITLE_INFO;
		?>
	</small>
		</div>
		<h2 class="accordion-header"><?php translate("type");?></h2>

		<div class="accordion-content">
			<p>

				<input type="radio" name="type" id="type_page" value="page" checked>
				<label for="type_page"><?php translate("page");?></label> <br /> <input
					type="radio" name="type" value="list" id="type_list"> <label
					for="type_list"><?php translate("list");?></label> <br /> <input
					type="radio" name="type" value="link" id="type_link"> <label
					for="type_link"><?php translate("link");?></label> <br /> <input
					type="radio" name="type" value="module" id="type_module"> <label
					for="type_module"><?php translate("module");?></label>
			</p>
		</div>
		<h2 class="accordion-header"><?php translate("menu_entry");?></h2>

		<div class="accordion-content">
			<strong><?php
		
		echo TRANSLATION_LANGUAGE;
		?>
	</strong> <br /> <select name="language">
	<?php
		$languages = getAllLanguages ();
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
	</select> <br /> <br /> <strong><?php
		
		echo TRANSLATION_CATEGORY;
		?>
	</strong><br />
	<?php echo categories :: getHTMLSelect()?>
	<br /> <br /> <strong><?php
		
		echo TRANSLATION_MENU;
		?>
	</strong> <span style="cursor: help;"
				onclick="$('div#menu_help').slideToggle()">[?]</span><br /> <select
				name="menu" size=1>
		<?php
		foreach ( getAllMenus () as $menu ) {
			?>
		<option value="<?php echo $menu?>">
		<?php
			
			translate ( $menu );
			?></option>
		<?php
		}
		?>
	</select>
			<div id="menu_help" class="help" style="display: none">
	<?php
		
		echo nl2br ( TRANSLATION_HELP_MENU );
		?>
	</div>
			<br /> <br /> <strong><?php
		
		echo TRANSLATION_POSITION;
		?>
	</strong> <span style="cursor: help;"
				onclick="$('div#position_help').slideToggle()">[?]</span><br /> <input
				type="text" required="true" name="position" value="0">
			<div id="position_help" class="help" style="display: none">
	<?php
		
		echo nl2br ( TRANSLATION_HELP_POSITION );
		?>
	</div>
			<br /> <br /> <strong><?php
		
		echo TRANSLATION_PARENT;
		?>
	</strong><br /> <select name="parent" size=1>
				<option selected="selected" value="NULL">
			[
			<?php
		
		echo TRANSLATION_NONE;
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
		
		echo TRANSLATION_ACTIVATED;
		?>
	</strong><br /> <select name="activated" size=1>
				<option value="1">
		<?php
		
		echo TRANSLATION_ENABLED;
		?>
		</option>
				<option value="0">
		<?php
		
		echo TRANSLATION_DISABLED;
		?>
		</option>
			</select>
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
			<strong><?php
		
		echo TRANSLATION_MENU_IMAGE;
		?>
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
				onclick="$('#menu_image').val('');return false;"><?php
		
		echo TRANSLATION_CLEAR;
		?>
		</a> <br /> <br /> <strong><?php
		
		echo TRANSLATION_DESIGN;
		?></strong><br /> <select name="theme" size=1>
				<option value="">
				[
				<?php
		
		echo TRANSLATION_STANDARD;
		?>
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
		</select> <br /> <br /> <strong><?php
		
		echo TRANSLATION_HTML_FILE;
		?>
		</strong> <br /> <input type="text" name="html_file" value="">
		</div>
		<h2 class="accordion-header"><?php translate("visibility");?></h2>

		<div class="accordion-content">
			<strong><?php
		
		echo TRANSLATION_VISIBLE_FOR;
		?>
		</strong><br /> <select name="access[]" size=4 multiple>
				<option value="all" selected>
			<?php
		
		echo TRANSLATION_EVERYONE;
		?>
			</option>
				<option value="registered">
			<?php
		
		echo TRANSLATION_REGISTERED_USERS;
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
				<strong><?php
		
		echo TRANSLATION_META_DESCRIPTION;
		?>
		</strong><br /> <input type="text" name="meta_description" value=''> <br />
				<br /> <strong><?php
		
		echo TRANSLATION_META_KEYWORDS;
		?>
		</strong><br /> <input type="text" name="meta_keywords" value=''>
			</div>
		</div>
		<h2 class="accordion-header"><?php translate("open_in");?></h2>

		<div class="accordion-content">
			<strong><?php
		
		echo TRANSLATION_OPEN_IN;
		?>
		</strong><br /> <select name="target" size=1>
				<option value="_self">
			<?php
		
		echo TRANSLATION_TARGET_SELF;
		?>
			</option>
				<option value="_blank">
			<?php
		
		echo TRANSLATION_TARGET_BLANK;
		?>
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
						onclick="$('#og_image').val('');return false;"><?php
		
		echo TRANSLATION_CLEAR;
		?>
		</a>

				</div>
			</div>
		</div>
		<div id="tab-list" style="display: none">
			<h2 class="accordion-header"><?php translate("list_properties");?></h2>

			<div class="accordion-content">
				<strong><?php
		
		echo TRANSLATION_LANGUAGE;
		?>
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
		<option value="<?php echo $menu?>">
		<?php
			
			translate ( $menu );
			?></option>
			<?php
		}
		?>
			</select> <br /> <br /> <strong><?php
		
		echo TRANSLATION_PARENT;
		?>
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
						<?php if($col == "title") echo 'selected';?>><?php echo $col;?></option>
	<?php }?>
</select> <br /> <br /> <strong><?php
		translate ( "order_direction" );
		?>
	</strong> <select name="list_order_direction">
					<option value="asc"><?php translate("asc");?></option>
					<option value="desc"><?php translate("desc");?></option>
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



editor.on("instanceReady", function()
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
		type="submit" value="<?php
		
		echo TRANSLATION_SAVE;
		?>">
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
	?>

<?php
}
?>
