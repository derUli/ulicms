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
    $(".kcfinder").each(function (index, element) {
        $(element).click(function (event) {
            openKCFinder(event.target);
        });
    });
});

function openKCFinder(field) {
    window.KCFinder = {
        callBack: function (url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window.open('kcfinder/browse.php?type=images&dir=images&lang=' + $("html").data("select2-language"), 'menu_image',
            'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
            'resizable=1, scrollbars=0, width=800, height=600'
            );
}

window.onbeforeunload = confirmExit;
function confirmExit()
{
    if (typeof formchanged !== "undefined" && formchanged === 1 && submitted === 0)
        return PageTranslation.ConfirmExitWithoutSave;
    else
        return;
}
