function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function validateCodeMirrorJson(cmEditor, wrapper) {
    if (isJsonString(cmEditor.getValue())) {
        wrapper.removeClass("border-red");
        wrapper.addClass("border-green");
        return true;
    } else {
        wrapper.removeClass("border-green");
        wrapper.addClass("border-red");
        return false;
    }
}

$(function () {
    // apply codemirror source code editor to all textareas with "codemirror"
    // class
    $("textarea.codemirror").each(function (index, elem) {
        var mode = "text/html";
        // if the textarea has a data-mimetype attribute use this for syntax
        // higlighting scheme
        // else fallback to html mode
        if ($(elem).data("mimetype")) {
            mode = $(elem).data("mimetype");
        }

        var editor = CodeMirror.fromTextArea(elem, {
            lineNumbers: true,
            matchBrackets: true,
            mode: mode,
            indentUnit: 0,
            indentWithTabs: false,
            enterMode: "keep",
            tabMode: "shift",
            readOnly: $(elem).prop("readonly")
        });
        if ($(elem).data("validate") === "json") {
            editor.on("change", function (cmEditor) {
                var wrapper = $(editor.getWrapperElement());
                validateCodeMirrorJson(cmEditor, wrapper);

                // Make sure that the is updated
                // TODO: Do this only on before submit for performance reasons
                cmEditor.save();
            });

            validateCodeMirrorJson(editor, $(editor.getWrapperElement()));
        }
    });
});