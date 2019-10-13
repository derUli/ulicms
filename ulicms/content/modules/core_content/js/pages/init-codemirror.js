/* global CodeMirror */

let formChanged = 0;
let submitted = 0;
let isCtrl = false;

const isJsonString = (str) => {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
};

const validateCodeMirrorJson = (cmEditor, wrapper) => {
    if (isJsonString(cmEditor.getValue())) {
        wrapper.removeClass("border-red");
        wrapper.addClass("border-green");
        return true;
    }
    wrapper.removeClass("border-green");
    wrapper.addClass("border-red");
    return false;
};

$(() => {
    // apply codemirror source code editor to all textareas
    // with "codemirror" class
    $("textarea.codemirror").each((index, elem) => {
        let mode = "text/html";
        // if the textarea has a data-mimetype attribute use this for syntax
        // higlighting scheme
        // else fallback to html mode
        if ($(elem).data("mimetype")) {
            mode = $(elem).data("mimetype");
        }

        const editor = CodeMirror.fromTextArea(elem, {
            lineNumbers: true,
            matchBrackets: true,
            mode: mode,
            indentUnit: 0,
            indentWithTabs: false,
            enterMode: "keep",
            tabMode: "shift",
            readOnly: $(elem).prop("readonly")
        });
        switch ($(elem).data("validate")) {
            case "json":
                editor.on("change", (cmEditor) => {
                    var wrapper = $(editor.getWrapperElement());
                    validateCodeMirrorJson(cmEditor, wrapper);
                });

                editor.on("blur", function (cmEditor) {
                    var wrapper = $(editor.getWrapperElement());
                    if (validateCodeMirrorJson(cmEditor, wrapper)) {
                        cmEditor.save();
                    }
                });
                validateCodeMirrorJson(editor, $(editor.getWrapperElement()));
                break;
            default:
                editor.on("blur", function (cmEditor) {
                    cmEditor.save();
                });
        }
    });
});