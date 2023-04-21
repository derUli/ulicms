/* global Translation */

// This script contains the code for the "design settings" page

const loadThemePreview = (selectField) => {
    const url = $(selectField).find("option:selected").data("preview-url");

    const targetElement = $($(selectField).data("preview-target-element"));
    const previewImage = targetElement.find(".preview");
    const loadSpinner = targetElement.find('.fa-spinner');

    if (!url) {
        $(targetElement).hide();
        return;
    }

    $(targetElement).show();

    loadSpinner.toggleVisibility(true);

    $.ajax({
        url: url,
        success: (result) => {
            const image = $(result);
            const imageSrc = image.attr('src');

            // Preload image file before showing it
            const preloadImage = new Image();
            preloadImage.src = image.attr('src');

            // After image is loaded show it
            preloadImage.onload = () => {
                previewImage.html(result);
                loadSpinner.toggleVisibility(false);
                previewImage.show();
            };

        },
        error: (jqXHR, textStatus, errorThrown) => {
            // If there is no preview image the controller returns status 404
            loadSpinner.toggleVisibility(false);
            previewImage.hide();
        }
    });
};


const updateFontPreview = () => {
    const fontFamily = $("select#default_font").val();
    const fontSize = $("select#font_size").val();

    $("#font-preview").css(
            {
                fontFamily: fontFamily,
                fontSize: fontSize
            }
    );
};

$(() => {
    $("select#default_font, select#font_size").change(updateFontPreview);

    updateFontPreview();

    loadThemePreview($("select[name='theme']"));
    loadThemePreview($("select[name='mobile_theme']"));
    $("select[name='theme'], select[name='mobile_theme']").change(
            (event) => {
        loadThemePreview($(event.currentTarget));
    });

    // ajax form submit
    $("#designForm").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#message").html("");
                    $("#msgcontainer, #loading").show();
                },
                success: () => {
                    $("#loading").hide();
                    $("#message").html(
                            `<span style="color:green;">
                    ${Translation.ChangesWasSaved}
        </span>`);
                    $("#msgcontainer, #loading").hide();
                }
            });
});