/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
   //config.language = 'de';
   config.ShiftEnterMode = 'p' ;
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

   config.uiColor = '#d1d8d0';
   config.removePlugins = "link,newpage,templates,preview,print,save,language";
   config.autoGrow_onStartup = true;   
   config.extraPlugins = 'autogrow,adv_link';
};
