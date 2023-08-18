/* global CKEDITOR, PageTranslation */

const CKCHANGED = (event) => {
    formChanged = 1;
    if (event.data.$.keyCode === 17) {
        isCtrl = false;
    }
};

$(() => {

    const ckeditorSettings = {
        removePlugins: ['MediaEmbed'],
        toolbar: [
            "heading",
            "|",
            "bold",
            "italic",
            "link",
            "bulletedList",
            "numberedList",
            "|",
            "indent",
            "outdent",
            "|",
            "codeBlock",
            "blockQuote",
            "insertTable",
            "mediaEmbed",
            "undo",
            "redo"
        ]
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
