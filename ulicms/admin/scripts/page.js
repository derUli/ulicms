function showAndHideFieldsByTypeWithoutEffects() {
    var type = $('input[name=type]:checked').val()
    if (typeof AllTypes[type] === "undefined") {
        type = "page";
    }
    $(".typedep").hide();
    var typeData = AllTypes[type];
    var show = typeData["show"];

    for (i = 0; i < show.length; i++) {
        $(show[i]).show();
    }

    if ($("#type_snippet").is(":checked")) {
        unbindEvents();
        $("select[name='hidden']").val("1").trigger("change");
        $("select[name='menu']").val("not_in_menu").trigger("change");
        bindEvents();
    }

    $(".custom-field-tab").each(function (index, el) {
        if ($(el).data("type") === $("input[name='type']:checked").val()) {
            $(el).show();
        } else {
            $(el).hide();
        }
    });

    if ($("#type_node").is(":checked") || $("#type_snippet").is(":checked")) {
        $("#btn-view-page").hide();
    } else {
        $("#btn-view-page").show();
    }

    if ($("select[name='menu']").val() === "not_in_menu") {
        $("#parent-div").hide();
        $("#menu_image_div").hide();
    } else {
        $("#parent-div").show();
        $("#menu_image_div").show();
    }
}
// this function shows and hides areas for the selected content type
function showAndHideFieldsByType() {
    if (typeof AllTypes[type] === "undefined") {
        type = "page";
    }
    var type = $('input[name=type]:checked').val()
    var showSelector = AllTypes[type]["show"].join(",")
    $(".typedep").not(showSelector).slideUp();
    var typeData = AllTypes[type];
    var show = typeData["show"];

    for (i = 0; i < show.length; i++) {
        $(show[i]).slideDown();
    }

    if ($("#type_snippet").is(":checked")) {
        unbindEvents();
        $("select[name='hidden']").val("1").trigger("change");
        $("select[name='menu']").val("not_in_menu").trigger("change");
        bindEvents();
    }

    $(".custom-field-tab").each(function (index, el) {
        if ($(el).data("type") === $("input[name='type']:checked").val()) {
            $(el).slideDown();
        } else {
            $(el).slideUp();
        }
    });

    if ($("#type_node").is(":checked") || $("#type_snippet").is(":checked")) {
        $("#btn-view-page").slideUp();
    } else {
        $("#btn-view-page").slideDown();
    }

    if ($("select[name='menu']").val() === "not_in_menu") {
        $("#parent-div").slideUp();
        $("#menu_image_div").slideUp();
    } else {

        $("#parent-div").slideDown();
        $("#menu_image_div").slideDown();
    }
}

// this shows a thumbnail of the selected file on text inputs with
// kcfinder image uploader attached
function refreshFieldThumbnails() {
    $("input.kcfinder[data-kcfinder-type=images]").each(
            function (index, element) {
                var id = $(element).attr("name");
                if ($(element).val().length > 0) {
                    $("img#thumbnail-" + id).attr("src", $(element).val());
                    $("img#thumbnail-" + id).show();
                } else {
                    $("img#thumbnail-" + id).hide();
                }
            });
}

// Bind events for dependend fields, clear-buttons, and file inputs
function bindEvents() {
    $("input[name=\"type\"]").change(showAndHideFieldsByType);
    $("select[name='menu']").change(showAndHideFieldsByType);
    $("select[name='menu']").select2("destroy").select2({
        "width": "100%"
    });
    $(".clear-field").on("click", function (event) {
        event.preventDefault();
        var element = $(event.target);
        var linkFor = $(element).data("for");
        $(linkFor).val("");
        refreshFieldThumbnails();
    });

    refreshFieldThumbnails();
    $("input.kcfinder")
            .on(
                    "click",
                    function (event) {
                        var field = $(event.target);
                        var name = $(field).data("kcfinder-name") ? $(field)
                                .data("kcfinder-name") : "kcfinder_textbox";
                        var type = $(field).data("kcfinder-type") ? $(field)
                                .data("kcfinder-type") : "images"

                        window.KCFinder = {
                            callBack: function (url) {
                                field.val(url);
                                window.KCFinder = null;
                                refreshFieldThumbnails();
                            }
                        };
                        window
                                .open(
                                        'kcfinder/browse.php?type=' + type + "&langCode=" + $("html").data("select2-language"),
                                        name,
                                        'status=0, toolbar=0, location=0, menubar=0, directories=0, '
                                        + 'resizable=1, scrollbars=0, width=800, height=600');

                    });
}
// undbindEvents to prevent endless loop when selecting specific content types
function unbindEvents() {
    $("input[name=\"type\"]").off("change");
    $("select[name='menu']").off("change");
    $("input.kcfinder").off("click");
    $(".clear-field").off("click");
}


AllTypes = {}

$(function () {
    if ($("#page-list").length <= 0) {
        var url = $(".main-form").first().data("get-content-types-url");

        $.ajax({
            url,
            success: function (response, status) {

                AllTypes = response;
                showAndHideFieldsByTypeWithoutEffects();
                $(".loadspinner").hide();
                $(".pageform").show();
                // Refresh CodeMirror
                refreshCodeMirrors();
                $('.accordion-header').click(function () {
                    refreshCodeMirrors();
                });
            }});

        bindEvents();
    }
});

// this suggest a slug which may be used as the url for a page
function suggestSlug(text) {
    var pageSlug = slug(text);
    $("#slug").val(pageSlug);
}

// this checks if a slug is free within the selected language
// the combination of slug + language must be unique
function slugOrLanguageChanged(item) {
    var id_field = $("input[name='page_id']");
    var myid = 0;
    if (id_field) {
        myid = $(id_field).val();
    }
    var data = {
        csrf_token: $("input[name=csrf_token]").first().val(),
        slug: $("input[name='slug']").val(),
        language: $("select[name='language']").val(),
        id: myid
    };
    var url = $(".main-form").first().data("slug-free-url");

    $.post(url, data, function (text, status) {
        if (text === "yes") {
            $("input[name='slug']").removeClass("error-field");
            $("select[name='language']").removeClass("error-field");
        } else {
            $("input[name='slug']").addClass("error-field");
            $("select[name='language']").addClass("error-field");
        }
    });

}
// filter parent pages by selected language and menu
function filterParentPages() {
    var data = {
        csrf_token: $("input[name=csrf_token]").first().val(),
        mlang: $("select[name='language']").val(),
        mmenu: $("select[name='menu']").val(),
        mparent: $("select[name='parent_id']").val()
    };

    var url = $(".main-form").first().data("parent-pages-url");

    $.post(url, data, function (text, status) {
        $("select[name='parent_id']").html(text);
    });
}

$(function () {
    // bind event to reset filters button
    $("#btn-reset-filters").click(function (e) {
        if (!window.confirm(Translation.ResetFilters + "?")) {
            e.preventDefault();
        }
    })
    // check if a slug is free on changing system title or menu
    // XXX: this field should be named slug everywhere in the code
    $("input[name='slug']").keyup(function () {
        slugOrLanguageChanged($(this));
    });
    $("select[name='menu']").change(function () {
        filterParentPages();
    });

    // check if slug is free and update parent page options
    $("select[name='language']").change(function () {
        slugOrLanguageChanged($(this));
        filterParentPages();
    });
    // bind event to "View" button at the bottom of page edit form
    $("#btn-view-page").click(function () {
        var url = "../?goid=" + $("#page_id").val();
        // if page has unsaved changes open it in new window/tab
        // else open it in the same window/tab
        if (formChanged && !submitted) {
            window.open(url);
        } else {
            location.href = url;
        }
    })

    slugOrLanguageChanged($("input[name='slug']"));
    slugOrLanguageChanged($("select[name='language']"));

    filterParentPages();

    // AJAX submit page edit form
    $("#pageform-edit")
            .ajaxForm(
                    {
                        beforeSubmit: function (e) {
                            $("#message_page_edit").html("");
                            $("#message_page_edit").hide();
                            $(".loading").show();
                        },
                        beforeSerialize: function ($Form, options) {
                            /* Before serialize */
                            for (instance in CKEDITOR.instances) {
                                CKEDITOR.instances[instance].updateElement();
                            }
                            return true;
                        },
                        success: function (e) {
                            $(".loading").hide();
                            $("#message_page_edit")
                                    .html(
                                            "<span style=\"color:green;\">Die Seite wurde gespeichert</span>");
                            $("#message_page_edit").show();
                        }
                    });

    // filter by category
    $('#page-list #category_id').on(
            'change',
            function (e) {
                var valueSelected = $('#category_id').val();
                location.replace("index.php?action=pages&filter_category="
                        + valueSelected)
            });
    $("#page-list form.page-delete-form").off("submit");
    $("#page-list form.page-delete-form").ajaxForm(ajaxOptionsDelete);
    $("#page-list form.undelete-form").ajaxForm(ajaxOptionsUndelete);

    $("#show_filters").change(function (event) {
        var url = $(event.target).data("url");
        $.ajax({
            method: "get",
            url: url,
            success: function () {
                $(".page-list-filters").slideToggle();
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });

    });

});

// various filter functions
// XXX: this functions should be binded unobstrusive
function filterByLanguage(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_language="
                + element.options[index].value)
    }
}

function filterByType(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_type="
                + element.options[index].value)
    }
}

function filterByMenu(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_menu="
                + element.options[index].value)
    }
}

function filterByActive(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_active="
                + element.options[index].value)
    }
}

function filterByApproved(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_approved="
                + element.options[index].value)
    }
}

function filterByParent(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_parent="
                + element.options[index].value)
    }
}

function filterByStatus(element) {
    var index = element.selectedIndex;
    if (element.options[index].value !== "") {
        location.replace("index.php?action=pages&filter_status="
                + element.options[index].value)
    }
}

// empty recycle bin without reloading the page
function ajaxEmptyTrash(url) {
    if (confirm(Translation.WannaEmptyTrash)) {
        $.ajax({
            url: url,
            success: function () {
                $("table.dataset-list tbody tr").fadeOut();
            }
        });
    }
    return false;
}

// undelete action
var ajaxOptionsUndelete = {
    success: function (responseText, statusText, xhr, $form) {
        var action = $($form).attr("action");
        var id = $($form).data("id");
        $($form).closest("tr").fadeOut();
    }
}

// handling delete action
var ajaxOptionsDelete = {
    beforeSubmit: function () {
        return askForDelete();
    },
    success: function (responseText, statusText, xhr, $form) {
        var action = $($form).attr("action");
        var id = $($form).data("id");
        $($form).closest("tr").fadeOut();
    }
};
