/*
*   Plugin developed by Netbroad, C.B.
*   Improved by Argon
*
*   LICENCE: GPL, LGPL, MPL
*   NON-COMMERCIAL PLUGIN.
*
*   Website: netbroad.eu
*   Twitter: @netbroadcb
*   Facebook: Netbroad
*   LinkedIn: Netbroad
*
*/

CKEDITOR.plugins.add( 'fixed', {
    init: function( editor ) {
      editor.on('instanceReady', function (readyEvent) {
        if (CKEDITOR.toolbarFixer == undefined) {      // To prevent double activation - event listener and its handler should be set only once
          CKEDITOR.toolbarFixer = function toolbarFixerF(event) {
            for(var i=0; i<CKEDITOR.toolbarFixer.ckeRootNodes.length; ++i) {
              var editor = CKEDITOR.toolbarFixer.ckeRootNodes[i];
              var content = editor.getElementsByClassName('cke_contents').item(0);
              var toolbar = editor.getElementsByClassName('cke_top').item(0);
              var scrollvalue = document.documentElement.scrollTop > document.body.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;

              toolbar.style.width = content.offsetWidth + "px";
              toolbar.style.top = "0px";
              toolbar.style.left = "0px";
              toolbar.style.right = "0px";
              toolbar.style.margin = "0 auto";
              toolbar.style.boxSizing = "border-box";

              if (editor.offsetTop <= scrollvalue && (editor.offsetTop + editor.offsetHeight) > (scrollvalue + toolbar.offsetHeight + 100)) {
                toolbar.style.position = "fixed";
                content.style.marginTop = toolbar.offsetHeight + "px";
              } else {
                toolbar.style.position = "relative";
                content.style.marginTop = "0px";
              }
            }
          };
          CKEDITOR.toolbarFixer.ckeRootNodes = [];
          window.addEventListener('scroll', CKEDITOR.toolbarFixer, false);
        }
        CKEDITOR.toolbarFixer.ckeRootNodes.push(readyEvent.editor.container.$);
        readyEvent.editor.container.$.getElementsByClassName('cke_top').item(0).style.zIndex = 100;    // For codemirror plugin compatibility
      });
    }
});