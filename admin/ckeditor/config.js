/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	 config.language = 'de';
	 config.height="400px"
	 config.width="900px"
	 config.toolbar = 'cms';
	 
   config.filebrowserBrowseUrl = 'kcfinder/browse.php?type=files';
   config.filebrowserImageBrowseUrl = 'kcfinder/browse.php?type=images';
   config.filebrowserFlashBrowseUrl = 'kcfinder/browse.php?type=flash';
   config.filebrowserUploadUrl = 'kcfinder/upload.php?type=files';
   config.filebrowserImageUploadUrl = 'kcfinder/upload.php?type=images';
   config.filebrowserFlashUploadUrl = 'kcfinder/upload.php?type=flash';
 
config.toolbar_cms =
[
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','SpellChecker', 'Scayt'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
    '/',
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['BidiLtr', 'BidiRtl'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks']
];

	 config.uiColor = '#6b94ac';
};
