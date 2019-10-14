/* global CKEDITOR, PageTranslation */

const CKCHANGED = (event) => {
    formChanged = 1;
    if (event.data.$.keyCode === 17) {
        isCtrl = false;
    }
};

$(() => {
    for (name in CKEDITOR.instances) {
        const id = CKEDITOR.instances[name].element.getId();
        CKEDITOR.instances[name].destroy();
        const editor = CKEDITOR.replace(id,
                {
                    skin: $("body").data("ckeditor-skin")}
        );
        editor.on("instanceReady", ({editor}) =>
        {
            editor.document.on("keyup", CKCHANGED);
            editor.document.on("paste", CKCHANGED);

            editor.document.on('keydown', (event) =>
            {
                if (event.data.$.keyCode === 17)
                    isCtrl = true;
                if (event.data.$.keyCode === 83 && isCtrl === true)
                {
                    //The preventDefault() call prevents the browser's save popup to appear.
                    //The try statement fixes a weird IE error.
                    try {
                        event.data.$.preventDefault();
                    } catch (err) {
                    }
                    $("form").last().find("button[type=submit]").click();
                    //Call to your save function

                    return false;
                }
            });
        });
    }

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
});

window.onbeforeunload = () =>
        {
            if (typeof formChanged !== "undefined" && formChanged === 1 && submitted === 0) {
                return PageTranslation.ConfirmExitWithoutSave;
            }
            return;
        };
