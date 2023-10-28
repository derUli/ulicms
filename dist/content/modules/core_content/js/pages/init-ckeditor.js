/* global CKEDITOR, PageTranslation */

const CKCHANGED = (event) => {
    formChanged = 1;
    if (event.data.$.keyCode === 17) {
        isCtrl = false;
    }
};

$(() => {

    const ckeditorSettings = {
        language: $('html').data('select2-language'),
        removePlugins: ['MediaEmbed'],
        toolbar: {
            items: [
                'heading',
                '|',
                'fontFamily',
                'fontSize',
                'fontColor',
                'fontBackgroundColor',
                '|',
                'bold',
                'italic',
                'underline',
                'strikethrough',
                'subscript',
                'superscript',
                'code',
                '|',
                'alignment',
                '|',
                'bulletedList',
                'numberedList',
                '|',
                'indent',
                'outdent',
                '|',
                'horizontalLine',
                'codeBlock',
                'blockQuote',
                'insertTable',
                '|',
                'link',
                'uploadImage',
                '|',
                'undo',
                'redo',
                'findAndReplace'
            ]
        },
        
        shouldNotGroupWhenFull: true,
        simpleUpload: {
            // The URL that the images are uploaded to.
            uploadUrl: $('body').data('image-upload-url'),
            
            // Enable the XMLHttpRequest.withCredentials property if required.
            withCredentials: true,
  
            // Headers sent along with the XMLHttpRequest to the upload server.
            headers: {
              "X-CSRF-TOKEN": $('body').data('csrf-token')
            }
          }
    };

    document.querySelectorAll('textarea.ckeditor').forEach((element, index) => {
        ClassicEditor
        .create( element, ckeditorSettings )
        .catch( error => {
            bootbox.alert(error);
            console.error( error );
        } );

    });


    $('form').each(function (i, n) {
        $('input', n).change(() => {
            formChanged = 1;
        });

        $('textarea', n).change(() => {
            formChanged = 1;
        });

        $('select', n).change(() => {
            formChanged = 1;
        });

        $(n).submit(() => {
            submitted = 1;
        });
    });

    $(window).on('beforeunload', () =>
    {
        if (typeof formChanged !== "undefined" && formChanged === 1
                && submitted === 0) {
            return PageTranslation.ConfirmExitWithoutSave;
        }
        return;
    }
    );
});
