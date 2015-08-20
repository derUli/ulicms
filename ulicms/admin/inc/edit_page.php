<?php
include_once ULICMS_ROOT . "/classes/vcs.php";
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "pages" )) {
		$page = db_escape ( $_GET ["page"] );
		$query = db_query ( "SELECT * FROM " . tbname ( "content" ) . " WHERE id='$page'" );
		
		$allThemes = getThemesList ();
		
		$groups = db_query ( "SELECT id, name from " . tbname ( "groups" ) );
		while ( $row = db_fetch_object ( $query ) ) {
			
			?>



</form>

<form id="pageform" action="index.php?action=pages" method="post">
<?php
			
			csrf_token_html ();
			?>
	<input type="hidden" name="edit_page" value="edit_page"> <input
		type="hidden" name="page_id" value="<?php echo $row -> id?>"> <strong><?php
			
			echo TRANSLATION_PERMALINK;
			?></strong><br /> <input type="text" required="true"
		name="page_"
		value="<?php
			
			echo $row->systemname;
			?>"> <br /> <br /> <strong><?php
			
			echo TRANSLATION_PAGE_TITLE;
			?> </strong><br /> <input type="text" required="true"
		name="page_title"
		value="<?php
			echo htmlspecialchars ( $row->title );
			?>"> <br /> <br /> <strong><?php
			
			echo TRANSLATION_ALTERNATE_TITLE;
			?> </strong><br /> <input type="text"
		name="alternate_title"
		value="<?php
			echo htmlspecialchars ( $row->alternate_title );
			
			?>"><br /> <small><?php
			
			echo TRANSLATION_ALTERNATE_TITLE_INFO;
			?> </small> <br /> <br /> <strong><?php
			
			echo TRANSLATION_LANGUAGE;
			?></strong> <br /> <select name="language">
			<?php
			$languages = getAllLanguages ();
			
			$page_language = $row->language;
			
			for($j = 0; $j < count ( $languages ); $j ++) {
				if ($languages [$j] === $page_language) {
					echo "<option value='" . $languages [$j] . "' selected>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
				} else {
					echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
				}
			}
			
			$pages = getAllPages ( $page_language, "title", false );
			?>
	</select> <br /> <br /> <strong><?php
			
			echo TRANSLATION_CATEGORY;
			?> </strong><br />
	<?php
			echo categories::getHTMLSelect ( $row->category );
			?>

	<br /> <br /> <strong><?php
			
			echo TRANSLATION_MENU;
			?> </strong> <span style="cursor: help;"
		onclick="$('div#menu_help').slideToggle()">[?]</span><br /> <select
		name="menu" size=1>
		<?php
			foreach ( getAllMenus () as $menu ) {
				?>
		<option
			<?php
				
				if ($row->menu == $menu) {
					echo 'selected="selected" ';
				}
				?>
			value="<?php echo $menu?>">
			<?php
				
				translate ( $menu );
				?>
		</option>
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
			?> </strong> <span style="cursor: help;"
		onclick="$('div#position_help').slideToggle()">[?]</span><br /> <input
		type="text" name="position" required="true"
		value="<?php
			
			echo $row->position;
			?>">

	<div id="position_help" class="help" style="display: none">
	<?php
			
			echo nl2br ( TRANSLATION_HELP_POSITION );
			?>
	</div>

	<br /> <br />
	<strong><?php
			
			echo TRANSLATION_PARENT;
			?> </strong><br /> <select name="parent" size=1>
		<option value="NULL">
			[
			<?php
			
			echo TRANSLATION_NONE;
			?>
			]
		</option>
		<?php
			
			foreach ( $pages as $key => $page ) {
				?>
		<option
			value="<?php
				
				echo $page ["id"];
				?>"
			<?php
				
				if ($page ["id"] == $row->parent) {
					echo " selected='selected'";
				}
				?>>
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
			?> </strong><br /> <select name="activated" size=1>
		<option value="1"
			<?php
			
			if ($row->active == 1) {
				echo "selected";
			}
			?>>
		<?php
			
			echo TRANSLATION_ENABLED;
			?>
		</option>
		<option value="0"
			<?php
			
			if ($row->active == 0) {
				echo "selected";
			}
			?>>
		<?php
			
			echo TRANSLATION_DISABLED;
			?>
		</option>
	</select> <br /> <br />



	<p>
		<a name="toggle_options" href="#toggle_options"
			onclick="$('#extra_options').slideToggle();"><?php
			
			echo TRANSLATION_ADDITIONAL_SETTINGS;
			?> </a>
	</p>
	<fieldset id="extra_options">


		<strong><?php
			
			echo TRANSLATION_EXTERNAL_REDIRECT;
			?></strong><br /> <input type="text" name="redirection"
			value="<?php
			
			echo $row->redirection;
			?>"> <br /> <br /> <strong><?php
			
			echo TRANSLATION_MENU_IMAGE;
			?> </strong><br />

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
			style="cursor: pointer" /><br /> <a href="#"
			onclick="$('#menu_image').val('');return false;"><?php
			
			echo TRANSLATION_CLEAR;
			?> </a> <br /> <br /> <strong><?php
			
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
			<option
				value="<?php
				
				echo $th;
				?>"
				<?php
				
				if (! is_null ( $row->theme ) and ! empty ( $row->theme ) and $row->theme == $th)
					echo "selected";
				?>>
				<?php
				
				echo $th;
				?>
			</option>
			<?php
			}
			?>
		</select> <br /> <br /> <strong><?php
			
			echo TRANSLATION_HTML_FILE;
			?></strong> <br /> <input type="text" name="html_file"
			value="<?php
			
			echo $row->html_file;
			?>"> <br /> <br /> <strong><?php
			
			echo TRANSLATION_VISIBLE_FOR;
			?> </strong><br />
			<?php
			
			$access = explode ( ",", $row->access );
			?>
		<select name="access[]" size=4 multiple>
			<option value="all"
				<?php if(in_array("all", $access)) echo " selected"?>>
				<?php
			
			echo TRANSLATION_EVERYONE;
			?></option>
			<option value="registered"
				<?php if(in_array("registered", $access)) echo " selected"?>>
				<?php
			
			echo TRANSLATION_REGISTERED_USERS;
			?></option>
			
			
			<option value="mobile" <?php if(in_array("mobile", $access)) echo " selected"?>><?php translate("mobile_devices");?></option>
			<option value="desktop" <?php if(in_array("desktop", $access)) echo " selected"?>><?php translate("desktop_computers");?></option>
				<?php
			while ( $row2 = db_fetch_object ( $groups ) ) {
				if (in_array ( strval ( $row2->id ), $access )) {
					echo '<option value="' . $row2->id . '" selected>' . real_htmlspecialchars ( $row2->name ) . '</option>';
				} else {
					echo '<option value="' . $row2->id . '">' . real_htmlspecialchars ( $row2->name ) . '</option>';
				}
			}
			?>
		</select> <br /> <br /> <strong><?php
			
			echo TRANSLATION_META_DESCRIPTION;
			?></strong><br /> <input type="text"
			name="meta_description"
			value="<?php
			echo htmlspecialchars ( $row->meta_description );
			?>"> <br /> <br /> <strong><?php
			
			echo TRANSLATION_META_KEYWORDS;
			?> </strong><br /> <input type="text" name="meta_keywords"
			value="<?php
			echo htmlspecialchars ( $row->meta_keywords );
			?>"> <br /> <br />
		<strong><?php
			
			echo TRANSLATION_OPEN_IN;
			?></strong><br /> <select name="target" size=1>
			<option
				<?php
			
			if ($row->target == "_self") {
				echo 'selected="selected" ';
			}
			?>
				value="_self">
				<?php
			
			echo TRANSLATION_TARGET_SELF;
			?></option>
			<option
				<?php
			
			if ($row->target == "_blank") {
				echo 'selected="selected" ';
			}
			?>
				value="_blank">
				<?php
			
			echo TRANSLATION_TARGET_BLANK;
			?></option>
		</select><br />
		<h3><?php translate("open_graph");?></h3>
		<div style="margin-left:20px;">
		<strong><?php translate("title");?>
		</strong><br /> <input type="text" name="og_title" value='<?php
			echo htmlspecialchars ( $row->og_title );
			?>'> <br />
		 <br />
		<strong><?php translate("type");?>
		</strong><br /> <input type="text" name="og_type" value='<?php
			echo htmlspecialchars ( $row->og_type );
			?>'> <br /> <br />
		<strong><?php translate("image");?>
		<br/>
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
			value="<?php
			echo htmlspecialchars ( $row->og_image );
			?>" style="cursor: pointer" /><br /> <a href="#"
			onclick="$('#og_image').val('');return false;"><?php
		
		echo TRANSLATION_CLEAR;
		?>
		</a> 
		<?php
if(!empty($row->og_image)){
$og_url = get_protocol_and_domain(). $row->og_image;
?>
<div style="margin-top:15px;"><img class="small-preview-image" src="<?php 

echo htmlspecialchars($og_url);?>"/></div>
<?php }?>
		</div>
		<h3><?php
		
		echo TRANSLATION_CUSTOM_DATA_JSON;
		?>
		</h3>
		<textarea name="custom_data"
			style="width: 100%; height: 200px;" cols=80 rows=10><?php
			
			echo htmlspecialchars ( $row->custom_data );
			?></textarea>

	</fieldset>


	<br /> <br />

	<?php
			
			add_hook ( "page_option" );
			?>


	<div>
		<textarea name="page_content" id="page_content" cols=60 rows=20><?php
			
echo htmlspecialchars ( $row->content );
			?></textarea>
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
				
				echo getconfig ( "ckeditor_skin" );
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
		<?php
			
$rev = vcs::getRevisionsByContentID ( $row->id );
			if (count ( $rev ) > 0) {
				?>
		<p>
			[<a
				href="index.php?action=restore_version&content_id=<?php echo $row->id;?>"><?php translate("restore_older_version");?></a>]
		</p>
		<?php }?>
		<div class="inPageMessage">
			<div id="message_page_edit" class="inPageMessage"></div>
			<img class="loading" src="gfx/loading.gif" alt="Wird gespeichert...">
		</div>

		<input type="submit"
			value="<?php
			
			echo TRANSLATION_SAVE_CHANGES;
			?>">
	</div>

	<?php
			if (getconfig ( "override_shortcuts" ) == "on" || getconfig ( "override_shortcuts" ) == "backend") {
				?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
			}
			?>
</form>

<script type="text/javascript">
$("#pageform").ajaxForm({beforeSubmit: function(e){
  $("#message_page_edit").html("");
  $("#message_page_edit").hide();
  $(".loading").show();
  }, beforeSerialize:function($Form, options){
        /* Before serialize */
        for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances[instance].updateElement();
        }
        return true; 
    },
  success:function(e){
  $(".loading").hide();  
  $("#message_page_edit").html("<span style=\"color:green;\">Die Seite wurde gespeichert</span>");
  $("#message_page_edit").show();
  }
  
}); 

</script>



<?php
			break;
		}
		?>
		<?php
	} else {
		noperms ();
         }
     ?>

<?php
    }
?>
