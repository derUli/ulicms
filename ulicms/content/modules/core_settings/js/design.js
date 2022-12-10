/* global Translation */

// This script contains the code for the "design settings" page

// show a message if a "design for mobile devices" is set but
// Mobile_Detect is not installed
const initMobileDetectNotice = () => {
    if ($("select[name='mobile_theme']").val() !== ""
            && $("#mobile_detect_notice").data("installed") === false) {
        $("#mobile_detect_notice").slideDown();
    } else {
        $("#mobile_detect_notice").slideUp();
    }
};

const loadThemePreview = (selectField) => {
    const url = $(selectField).find("option:selected").data("preview-url");
    const targetElement = $($(selectField).data("preview-target-element"));

    if (!url) {
        $(targetElement).hide();
        return;
    }

    $(targetElement).show();
    targetElement.find(".fa-spinner").show();
    targetElement.find(".preview").hide();

    $.ajax({
        url: url,
        success: (result) => {
            targetElement.find(".preview").html(result);
            targetElement.find(".fa-spinner").hide();
            targetElement.find(".preview").show();
        },
        error: (jqXHR, textStatus, errorThrown) => {
            targetElement.find(".fa-spinner").hide();
            targetElement.find(".preview").hide();
            $(targetElement).hide();
        }
    });
};


$(() => {
    $("#mobile_detect_notice").hide();
    initMobileDetectNotice();

    $("select[name='mobile_theme']").change(initMobileDetectNotice);

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