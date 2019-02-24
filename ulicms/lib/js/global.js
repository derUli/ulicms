// function to ask if a dataset should be deleted
function askForDelete() {
    return confirm(Translation.AskForDelete);
}

$(function () {
    // delete form handling
    $("form.delete-form").submit(function () {
        return askForDelete();
    });
    // apply codemirror source code editor to all textareas with "codemirror"
    // class
    $('textarea.codemirror').each(function (index, elem) {
        var mode = "text/html";
        // if the textarea has a data-mimetype attribute use this for syntax
        // higlighting scheme
        // else fallback to html mode
        if ($(elem).data("mimetype")) {
            mode = $(elem).data("mimetype");
        }
        CodeMirror.fromTextArea(elem, {
            lineNumbers: true,
            matchBrackets: true,
            mode: mode,
            indentUnit: 0,
            indentWithTabs: false,
            enterMode: "keep",
            tabMode: "shift",
            readOnly: $(elem).prop("readonly")
        });
    });
})
