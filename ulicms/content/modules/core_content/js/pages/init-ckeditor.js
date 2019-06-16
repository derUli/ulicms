function CKCHANGED(event) {
    formChanged = 1;
    if (event.data.$.keyCode == 17) {
        isCtrl = false;
    }
}

formChanged = 0;
submitted = 0;
isCtrl = false;


$(function () {
    for (name in CKEDITOR.instances)
    {
        var id = CKEDITOR.instances[name].element.getId();
        CKEDITOR.instances[name].destroy()
        var editor = CKEDITOR.replace(id,
                {
                    skin: $("body").data("ckeditor-skin")}
        );
        editor.on("instanceReady", function ()
        {
            this.document.on("keyup", CKCHANGED);
            this.document.on("paste", CKCHANGED);

            editor.document.on('keydown', function (event)
            {
                if (event.data.$.keyCode == 17)
                    isCtrl = true;
                if (event.data.$.keyCode == 83 && isCtrl == true)
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
        $('input', n).change(function () {
            formChanged = 1
        });
        $('textarea', n).change(function () {
            formChanged = 1
        });
        $('select', n).change(function () {
            formChanged = 1
        });
        $(n).submit(function () {
            submitted = 1
        });
    });

});

window.onbeforeunload = confirmExit;
function confirmExit()
{
    if (typeof formChanged !== "undefined" && formChanged === 1 && submitted === 0) {
        return PageTranslation.ConfirmExitWithoutSave;
    } else {
        return;
    }
}
