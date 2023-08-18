const refreshCodeMirrors = () => {
    $('.CodeMirror').each((i, el) =>
        el.CodeMirror.refresh()
    );
};

const updateCKEditors = () => {
 // TODO: Check if this is obsolete
};