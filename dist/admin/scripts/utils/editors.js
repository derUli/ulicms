const refreshCodeMirrors = () => {
    $('.CodeMirror').each((i, el) =>
        el.CodeMirror.refresh()
    );
};
