/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
   config.language = 'de';
   config.width = "800px";
   config.height = "400px";
	 
   if(window.location.href.indexOf("admin/") != -1)
   {
     config.filebrowserBrowseUrl = 'kcfinder/browse.php?type=files';
     config.filebrowserImageBrowseUrl = 'kcfinder/browse.php?type=images';
     config.filebrowserFlashBrowseUrl = 'kcfinder/browse.php?type=flash';
     config.filebrowserUploadUrl = 'kcfinder/upload.php?type=files';
     config.filebrowserImageUploadUrl = 'kcfinder/upload.php?type=images';
     config.filebrowserFlashUploadUrl = 'kcfinder/upload.php?type=flash';
   } else 
   {
     config.filebrowserBrowseUrl = 'admin/kcfinder/browse.php?type=files';
     config.filebrowserImageBrowseUrl = 'admin/kcfinder/browse.php?type=images';
     config.filebrowserFlashBrowseUrl = 'admin/kcfinder/browse.php?type=flash';
     config.filebrowserUploadUrl = 'admin/kcfinder/upload.php?type=files';
     config.filebrowserImageUploadUrl = 'admin/kcfinder/upload.php?type=images';
     config.filebrowserFlashUploadUrl = 'admin/kcfinder/upload.php?type=flash';
   }
   config.entities_latin = false;
   config.uiColor = '#6b94ac';
   config.removePlugins = "newpage,templates,preview,print,save";
};
