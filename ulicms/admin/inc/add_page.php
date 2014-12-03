<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("pages")){
     
        
         ?>
<form id="pageform" name="newpageform" action="index.php?action=pages" method="post">

<strong><?php echo TRANSLATION_PERMALINK;?></strong><br/>
<input type="text" style="width:300px;" name="system_title" value="">
<br/><br/>

<strong><?php echo TRANSLATION_PAGE_TITLE;?></strong><br/>
<input type="text" style="width:300px;" name="page_title" value="" onkeyup="systemname_vorschlagen(this.value)">
<br/><br/>

<strong><?php echo TRANSLATION_ALTERNATE_TITLE;?></strong><br/>
<input type="text" style="width:300px;" name="alternate_title" value=""><br/>
<small><?php echo TRANSLATION_ALTERNATE_TITLE_INFO;?></small>
<br/><br/>

<strong><?php echo TRANSLATION_LANGUAGE;?></strong>
<br/>
<select name="language">
<?php
         $languages = getAllLanguages();
         if(!empty($_SESSION["filter_language"])){
             $default_language = $_SESSION["filter_language"];
            
             }
        else{
             $default_language = getconfig("default_language");
             }
        
        
         for($j = 0; $j < count($languages); $j++){
             if($languages[$j] === $default_language){
                 echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
                 }else{
                 echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
                 }
            
            
             }
             
             
     $pages = getAllPages($default_language, "title", false);
         ?>
</select>
<br/><br/>

<strong><?php echo TRANSLATION_CATEGORY;?></strong><br/>
<?php echo categories :: getHTMLSelect()?>
<br/><br/>

<strong><?php echo TRANSLATION_MENU;?></strong><br/>
<select name="menu" size=1>
<?php
         foreach(getAllMenus() as $menu){
             ?>
<option value="<?php echo $menu?>"><?php echo $menu;
             ?></option>
<?php
            
             }
         ?>
</select><br/> <br/>

<strong><?php echo TRANSLATION_POSITION;?></strong><br/>
<input type="text" name="position" value="0">
              
<br/><br/>

<strong><?php echo TRANSLATION_PARENT;?></strong><br/>
<select name="parent" size=1>
<option selected="selected" value="NULL">-</option>
<?php foreach($pages as $key=>$page){
             ?>
	<option value="<?php echo $page["id"]; ?>">
	<?php echo $page["title"];?> (ID: <?php echo $page["id"]; ?>)
	</option>
<?php
             }
         ?>
</select>


<br/> <br/>

<script type="text/javascript">
function systemname_vorschlagen(txt){
var systemname=txt.toLowerCase();
systemname=systemname.replace(/ü/g,"ue");
systemname=systemname.replace(/ö/g,"oe");
systemname=systemname.replace(/ä/g,"ae");
systemname=systemname.replace(/Ã/g,"ss");
systemname=systemname.replace(/\040/g,"_");
systemname=systemname.replace(/\?/g,"");
systemname=systemname.replace(/\!/g,"");
systemname=systemname.replace(/\"/g,"");
systemname=systemname.replace(/\'/g,"");
systemname=systemname.replace(/\+/g,"");
systemname=systemname.replace(/\&/g,"");
systemname=systemname.replace(/\#/g,"");
document.newpageform.system_title.value=systemname
}
</script>

<strong><?php echo TRANSLATION_ACTIVATED;?></strong><br/>
<select name="activated" size=1>
<option value="1"><?php echo TRANSLATION_ENABLED;?></option>
<option value="0"><?php echo TRANSLATION_DISABLED;?></option>
</select>

<br/><br/>



                     <p><a name="toggle_options" href="#toggle_options" onclick="$('#extra_options').slideToggle();">Zusätzliche Optionen &gt;&gt;</a></p>
<fieldset id="extra_options">
<strong><?php echo TRANSLATION_EXTERNAL_REDIRECT;?></strong><br/>
<input type="text" style="width:300px;" name="redirection" value="">
<br/><br/>
<strong><?php echo TRANSLATION_MENU_IMAGE;?></strong><br/>

<script type="text/javascript">
function openMenuImageSelectWindow(field) {
    window.KCFinder = {
        callBack: function(url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window.open('kcfinder/browse.php?type=images&dir=images&lang=de', 'menu_image',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
        'resizable=1, scrollbars=0, width=800, height=600'
    );
}
</script>
<input type="text" id="menu_image" name="menu_image" readonly="readonly" onclick="openMenuImageSelectWindow(this)"
    value="" style="width:300px;cursor:pointer" /> <a href="#" onclick="$('#menu_image').val('');return false;"><?php echo TRANSLATION_CLEAR;?></a>
    
<br/><br/>
<strong><?php echo TRANSLATION_HTML_FILE;?></strong>
<br/>
<input type="text" style="width:300px;" name="html_file" value="">
<br/><br/>
<strong><?php echo TRANSLATION_VISIBLE_FOR;?></strong><br/>
<select name="access[]" size=4 multiple>
<option value="all" selected><?php echo TRANSLATION_EVERYONE;?></option>
<option value="registered"><?php echo TRANSLATION_REGISTERED_USERS;?></option>
<option value="admin"><?php echo TRANSLATION_ADMINS;?></option>
</select>


<br/><br/>

<strong><?php echo TRANSLATION_META_DESCRIPTION;?></strong><br/>
<input type="text" style="width:300px;" name="meta_description" value=''>
<br/><br/>

<strong><?php echo TRANSLATION_META_KEYWORDS;?></strong><br/>
<input type="text" style="width:300px;" name="meta_keywords" value=''>
 
<br/><br/>

<strong><?php echo TRANSLATION_COMMENTS;?></strong><br/>
<select name="comments_enabled" size=1>
<option value="1"><?php echo TRANSLATION_ENABLED;?></option>
<option value="0" selected="selected"><?php echo TRANSLATION_DISABLED;?></option>
</select>

<br/><br/>

<strong><?php echo TRANSLATION_OPEN_IN;?></strong><br/>
<select name="target" size=1>
<option value="_self"><?php echo TRANSLATION_TARGET_SELF;?></option>
<option value="_blank"><?php echo TRANSLATION_TARGET_BLANK;?></option>
</select>


<br/><br/>

<strong><?php echo TRANSLATION_CUSTOM_DATA_JSON;?></strong><br/>
<textarea name="custom_data" style="width:100%; height:200px;" cols=80 rows=10>{}</textarea>


</fieldset>

<br/><br/>


<?php add_hook("page_option");
         ?>

<div>
<textarea name="page_content" id="page_content" cols=60 rows=20></textarea>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'page_content',
					{
						skin : '<?php echo getconfig("ckeditor_skin");?>'
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

<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>
</noscript>
<div class="inPageMessage">
</div>
<input type="hidden" name="add_page" value="add_page">

<input type="submit" value="<?php echo TRANSLATION_SAVE;?>">
</div>
<?php
         if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
             ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
         ?>

</form>


<?php
         }
    else{
         noperms();
         }
     ?>

<?php }
?>
