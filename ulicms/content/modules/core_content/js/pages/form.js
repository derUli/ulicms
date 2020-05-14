// Script for new page and edit page form

window.slugChecking = false;
window.allConstants = {
    allInputs: 'input, select, textarea, checkbox, radio, button, submit'
};
/* global Translation, formChanged, submitted, bootbox, instance, CKEDITOR, allConstants */

window.AllTypes = {};

$(() => {
    const url = $(".main-form")
            .first()
            .data("get-content-types-url");

    $.ajax({
        url,
        success: (response) => {
            AllTypes = response;
            showAndHideFieldsByTypeWithoutEffects();
            $(".loadspinner").hide();
            $(".pageform").show();
            // Refresh CodeMirror
            refreshCodeMirrors();
            $(".accordion-header").click(() => refreshCodeMirrors());
        }
    });

    bindEvents();
    slugOrLanguageChanged();
    filterParentPages();

    $("#btn-submit").click((event) => {
        const form = $(event.target).closest("form");
        // Check if form is valid
        const formIsValid = form.get(0).checkValidity();
        // If is valid do submit
        if (formIsValid) {
            form.submit();
            return;
        }

        const accordionContents = $(".accordion-content");
        const accordionHeaders = $(".accordion-header").toArray();

        let firstInvalidField = null;
        let fieldAccordionIndex = null;
        let accordionIsVisible = false;
        accordionContents.each((accordionIndex, accordionElement) => {
            if (firstInvalidField) {
                return;
            }
            const inputs = $(accordionElement)
                    .find(window.allConstants.allInputs).toArray();
            const invalidInputs = inputs.filter((x) => !x.checkValidity());
            if (invalidInputs.length) {
                const firstInvalidInput = invalidInputs[0];
                firstInvalidField = invalidInputs[0];
                fieldAccordionIndex = accordionIndex;
                accordionIsVisible = $(accordionElement).hasClass("open-content");
                return;
            }
        });

        if (!firstInvalidField) {
            return;
        }

        if (firstInvalidField && fieldAccordionIndex !== null && !accordionIsVisible) {
            accordionHeaders[fieldAccordionIndex].click();
        } else if(!accordionIsVisible && firstInvalidField){
             firstInvalidField.reportValidity();
             return;
        }

        setTimeout(() => {
            zenscroll.to(
                   accordionHeaders[fieldAccordionIndex],
           500,
           () => firstInvalidField.reportValidity()
                   );
    }, 200);
    });
    
    // AJAX submit page edit form
    $("#pageform-edit").ajaxForm({
        beforeSubmit: () => {
            $("#message-page-edit").html("");
            $("#message-page-edit").hide();
            $(".loading").show();
        },
        beforeSerialize: () => {
            /* Before serialize */
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            return true;
        },
        success: () => {
            $(".loading").hide();
            $("#message-page-edit").html(
                    `<span style="color:green;">
                        ${Translation.PageSaved}
                     </span>`
                    );
            $("#message-page-edit").show();
        }
    });

    // check if a slug is free on changing system title or menu
    $("input[name='slug']").blur(() => slugOrLanguageChanged());
    $("input[name='title']").blur(() => slugOrLanguageChanged());

    $("select[name='menu']").change(() => filterParentPages());

    // check if slug is free and update parent page options
    $("select[name='language']").change(() => {
        slugOrLanguageChanged();
        filterParentPages();
    });

    // bind event to "View" button at the bottom of page edit form
    $("#btn-view-page").click(() => {
        const url = "../?goid=" + $("#page_id").val();
        // if page has unsaved changes open it in new window/tab
        // else open it in the same window/tab
        if (formChanged && !submitted) {
            window.open(url);
        } else {
            location.href = url;
        }
    });
});

showAndHideFieldsByTypeWithoutEffects = () => {
    const type = $("input[name=type]:checked").val();

    $(".typedep").hide();
    if (typeof AllTypes[type] === 'undefined') {
        return;
    }

    const typeData = AllTypes[type];
    const show = typeData["show"];

    show.forEach((element) => $(element).show());

    if ($("#type_snippet").is(":checked")) {
        unbindEvents();
        $("select[name='hidden']")
                .val("1")
                .trigger("change");
        $("select[name='menu']")
                .val("not_in_menu")
                .trigger("change");
        bindEvents();
    }

    $(".custom-field-tab").each((index, el) => {
        if ($(el).data("type") === $("input[name='type']:checked").val()) {
            $(el).find(allConstants.allInputs).prop("disabled", false);
            $(el).show();
        } else {
            $(el).hide();
            $(el).find(allConstants.allInputs).prop("disabled", true);
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
};

// this function shows and hides areas for the selected content type
showAndHideFieldsByType = () => {
    const type = $("input[name=type]:checked").val();
    const showSelector = AllTypes[type]["show"].join(",");
    $(".typedep")
            .not(showSelector)
            .slideUp();
    const typeData = AllTypes[type];
    const show = typeData["show"];

    show.forEach((element) => $(element).slideDown());

    if ($("#type_snippet").is(":checked")) {
        unbindEvents();
        $("select[name='hidden']")
                .val("1")
                .trigger("change");
        $("select[name='menu']")
                .val("not_in_menu")
                .trigger("change");
        bindEvents();
    }

    $(".custom-field-tab").each((index, el) => {
        if ($(el).data("type") === $("input[name='type']:checked").val()) {
            $(el).slideDown();
            $(el).find(allConstants.allInputs).prop("disabled", false);
        } else {
            $(el).slideUp();
            $(el).find(allConstants.allInputs).prop("disabled", true);
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
};

// this shows a thumbnail of the selected file on text inputs with
// fm image uploader attached
refreshFieldThumbnails = () => {
    $("input.fm[data-fm-type=images]").each((index, element) => {
        const id = $(element).attr("name");
        if ($(element).val().length > 0) {
            $("img#thumbnail-" + id).attr("src", $(element).val());
            $("img#thumbnail-" + id).show();
        } else {
            $("img#thumbnail-" + id).hide();
        }
    });
};

// Bind events for dependend fields, clear-buttons, and file inputs
bindEvents = () => {
    $('input[name="type"]').change(showAndHideFieldsByType);
    $("select[name='menu']").change(showAndHideFieldsByType);
    $("select[name='menu']")
            .select2("destroy")
            .select2({
                width: "100%"
            });
    $(".clear-field").on("click", (event) => {
        event.preventDefault();
        event.stopPropagation()
        const element = $(event.target);
        const linkFor = $(element).data("for");
        $(linkFor).val("");
        refreshFieldThumbnails();
    });

    refreshFieldThumbnails();
    $("input.fm").on("click", (event) => {
        const field = $(event.target);

        const name = $(field).attr("id")
                ? $(field).attr("id")
                : $(field).attr("name")

        const type = $(field).data("fm-type")
                ? $(field).data("fm-type")
                : "images";

        window.open(
                "fm/dialog.php?fldr=" +
                type +
                "&editor=ckeditor&type=2&langCode=" +
                $("html").data("select2-language") + "&popup=1&field_id=" + field.attr("id"),
                name,
                "status=0, toolbar=0, location=0, menubar=0, directories=0, " +
                "resizable=1, scrollbars=0, width=800, height=600"
                );
    });
};
// undbindEvents to prevent endless loop when selecting specific content types
unbindEvents = () => {
    $('input[name="type"]').off("change");
    $("select[name='menu']").off("change");
    $("input.fm").off("click");
    $(".clear-field").off("click");
};

// this suggest a slug which may be used as the url for a page
suggestSlug = (text) => {
    const pageSlug = slug(text, {lower: true});
    $("#slug").val(pageSlug);
};

// this checks if a slug is free within the selected language
// the combination of slug + language must be unique
slugOrLanguageChanged = () => {
    if (window.slugChecking) {
        return;
    }
    window.slugChecking = true;
    const id_field = $("input[name='page_id']");
    let myid = 0;
    if (id_field) {
        myid = $(id_field).val();
    }
    const data = {
        csrf_token: $("input[name=csrf_token]")
                .first()
                .val(),
        slug: $("input[name='slug']").val(),
        language: $("select[name='language']").val(),
        id: myid
    };
    const url = $(".main-form")
            .first()
            .data("slug-free-url");

    $.get(url, data, function (text) {
        if (text.length > $("input[name='slug']").val().length) {
            $("input[name='slug']").val(text);
        }
        window.slugChecking = false;
    });
};

// filter parent pages by selected language and menu
filterParentPages = () => {
    const data = {
        csrf_token: $("input[name=csrf_token]")
                .first()
                .val(),
        mlang: $("select[name='language']").val(),
        mmenu: $("select[name='menu']").val(),
        mparent: $("select[name='parent_id']").val()
    };

    const url = $(".main-form")
            .first()
            .data("parent-pages-url");
    $.get(url, data, function (text, status) {
        $("select[name='parent_id']").html(text);
    });
};
