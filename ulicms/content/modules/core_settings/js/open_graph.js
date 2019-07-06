function openMenuImageSelectWindow(field) {
    window.KCFinder = {
        callBack: function (url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window
            .open(
                    'kcfinder/browse.php?type=images&dir=images&langCode=' + $("html").data("select2-language"),
                    'og_image',
                    'status=0, toolbar=0, location=0, menubar=0, directories=0, '
                    + 'resizable=1, scrollbars=0, width=800, height=600');
}

$(function () {
    $("#open_graph")
            .ajaxForm(
                    {
                        beforeSubmit: function () {
                            $("#message").html("");
                            $("#loading").show();
                        },
                        success: function () {
                            $("#loading").hide();
                            // FIXME: localize this string
                            $("#message")
                                    .html(
                                            "<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
                        }
                    });
});
