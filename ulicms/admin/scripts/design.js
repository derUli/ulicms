// This script contains the code for the "design settings" page

// show a message if a "design for mobile devices" is set but Mobile_Detect is not installed
function initMobileDetectNotice() {
    if ($("select[name='mobile_theme']").val() != ""
            && $("#mobile_detect_notice").data("installed") == false) {
        $("#mobile_detect_notice").slideDown();
    } else {
        $("#mobile_detect_notice").slideUp();
    }
}

function loadThemePreview(selectField) {
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
        success: function (result) {
            targetElement.find(".preview").html(result);
            targetElement.find(".fa-spinner").hide();
            targetElement.find(".preview").show();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            targetElement.find(".fa-spinner").hide();
            targetElement.find(".preview").hide();
            $(targetElement).hide();
        }
    });
}

// show a privacy warning if a google font is selected
function onChangeDefaultFont() {
    var value = $("select#default_font").val();
    if (value === "google") {
        $("div#google-fonts").slideDown();
    } else {
        $("div#google-fonts").slideUp();
    }
}

$(function () {
    $("#mobile_detect_notice").hide();
    initMobileDetectNotice();
    $("select[name='mobile_theme']").change(function () {
        initMobileDetectNotice();
    });

    $("select#default_font").change(onChangeDefaultFont);

    loadThemePreview($("select[name='theme']"));
    loadThemePreview($("select[name='mobile_theme']"));
    $("select[name='theme'], select[name='mobile_theme']").change(function (event) {
        loadThemePreview($(event.target));
    });


    // ajax form submit
    $("#designForm").ajaxForm(
            {
                beforeSubmit: function (e) {
                    $("#message").html("");
                    $("#msgcontainer, #loading").show();
                },
                success: function (e) {
                    $("#loading").hide();
                    $("#message").html(
                            "<span style=\"color:green;\">"
                            + Translation.ChangesWasSaved + "</span>");
                    $("#msgcontainer, #loading").hide();
                }
            });
});