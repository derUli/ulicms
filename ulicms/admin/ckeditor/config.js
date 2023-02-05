/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights
 *          reserved. For licensing, see LICENSE.md or
 *          http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    // config.language = 'de';
    // Bootstrap soll eingebunden werden
    config.contentsCss = [CKEDITOR.basePath + 'contents.css',
        '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'];
    config.ShiftEnterMode = 'p';

    // allow all html tags
    config.allowedContent = true;

    config.height = '300px';
    config.image_prefillDimensions = false;

    // responsive filemanager
    if (window.location.href.indexOf("admin/") !== -1) {
        config.filebrowserBrowseUrl = 'fm/dialog.php?type=2&editor=ckeditor&fldr=files';
        config.filebrowserImageBrowseUrl = 'fm/dialog.php?type=1&editor=ckeditor&fldr=images';
        config.filebrowserFlashBrowseUrl = 'fm/dialog.php?type=2&editor=ckeditor&fldr=flash';
    } else {
        config.filebrowserBrowseUrl = 'admin/fm/dialog.php?type=2&editor=ckeditor&fldr=files';
        config.filebrowserImageBrowseUrl = 'admin/fm/dialog.php?type=1&editor=ckeditor&fldr=images';
        config.filebrowserFlashBrowseUrl = 'admin/fm/dialog.php?type=2&editor=ckeditor&fldr=flash';
    }
    config.entities_latin = false;
    config.uiColor = '#d1d8d0';

    config.removePlugins = "newpage,templates,preview,print,save,language,autoembed";
    // disable contextmenu on touchy things
    // to make it possible to select text in editor
    if (isTouchDevice()) {
        console.log("CKEditor: This is a touchscreen device. Disable Context Menu");
        // We need also to disable plugins which are dependent on contextmenu
        config.removePlugins += ',colordialog,liststyle,tabletools,contextmenu,';
    }
    
    config.autoGrow_onStartup = false;
    config.extraPlugins = 'link,font';
};
