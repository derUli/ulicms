
function CKCHANGED() {
    formchanged = 1;
}

formchanged = 0;
submitted = 0;

$(document).ready(function () {
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
        });
    }
    $('form').each(function (i, n) {
        $('input', n).change(function () {
            formchanged = 1
        });
        $('textarea', n).change(function () {
            formchanged = 1
        });
        $('select', n).change(function () {
            formchanged = 1
        });
        $(n).submit(function () {
            submitted = 1
        });
    });

});

window.onbeforeunload = confirmExit;
function confirmExit()
{
    if (typeof formchanged !== "undefined" && formchanged === 1 && submitted === 0)
        return PageTranslation.ConfirmExitWithoutSave;
    else
        return;
}
