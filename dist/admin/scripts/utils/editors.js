const refreshCodeMirrors = () => {
    $('.CodeMirror').each((i, el) =>
        el.CodeMirror.refresh()
    );
};

const updateCKEditors = () => {
    if (typeof CKEDITOR === "undefined") {
        return;
    }

    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
};