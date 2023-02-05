/* global Translation */

const openMenuImageSelectWindow = (target) => {
    window.open(
            "fm/dialog.php?fldr=images&editor=ckeditor&type=1&langCode=" +
            $("html").data("select2-language") + "&popup=1&field_id=og_image",
            "og_image",
            "status=0, toolbar=0, location=0, menubar=0, directories=0, " +
            "resizable=1, scrollbars=0, width=850, height=600"
            );
};

$(() => {
    $("#og_image").click((event) => openMenuImageSelectWindow(event.target))
    $("#open_graph").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#message").html("");
                    $("#loading").show();
                },
                success: () => {
                    $("#loading").hide();
                    // FIXME: localize this string
                    $("#message")
                            .html(`<span style="color:green;">${Translation.ChangesWasSaved}</span>`);
                }
            });
});
