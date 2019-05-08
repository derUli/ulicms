$(function () {
    $("#edit-form").find("select, input").change(function () {
        updatePreview();
    });

    updatePreview();
})

function updatePreview() {
    $("#preview-text").html('<i class="fa fa-spinner fa-spin"></i>');
    const editForm = $("#edit-form");
    const previewForm = $("#preview-form");
    editForm.find("select, input").each(function (index, element) {
        previewForm.find("[name='" + $(element).attr("name") + "']").val(
                $(element).val());
    });

    previewForm.find("[name='sMethod']").val("preview");
    $.ajax({
        type: "POST",
        url: "index.php",
        data: previewForm.serialize(),
        success: function (response) {
            console.log(response);
            $("#preview-text").html(response);
            const element = $("#preview-text").find(".text-rotator").first();
            $(element).Morphext({
                // The [in] animation type. Refer to Animate.css for a list of available animations.
                animation: $(element).data("animation"),
                // An array of phrases to rotate are created based on this separator. Change it if you wish to separate the phrases differently (e.g. So Simple | Very Doge | Much Wow | Such Cool).
                separator: $(element).data("separator"),
                // The delay between the changing of each phrase in milliseconds.
                speed: $(element).data("speed")
            });
        }

    });
}
