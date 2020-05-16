// this bind an event to a checkbox to toggle a password field between clear
// text and stars
const bindTogglePassword = (inputField, checkboxField) => {
    const input = $(inputField);
    const checkbox = $(checkboxField);
    $(checkbox).click(() => {
        if ($(checkbox).is(':checked')) {
            $(input).attr('type', 'text');
        } else {
            $(input).attr('type', 'password');
        }
    });
};

const checkboxChecked = (event) => {
    const item = $(event.currentTarget).data("select-all-checkbox");
    const group = $(event.currentTarget).data("checkbox-group");
    checkSelectAllIfAllChecked(item, group);
};

const checkSelectAllIfAllChecked = (item, group) => {
    if (!item) {
        return;
    }

    // if the count of the checked checkboxes in the group is equal to the count
    // of all checkboxes in this group
    const allSelected = $("input[type=checkbox][data-checkbox-group='" + group
            + "']:checked").length === $("input[type=checkbox][data-checkbox-group='"
            + group + "']").length;
    // check the "Select All" Checkbox, else uncheck it
    $(item).prop("checked", allSelected);
};

const selectAllChecked = (event) => {
    const selectAllCheckbox = $(event.target);
    const target = $(selectAllCheckbox).data("target");
    $(target).prop("checked",
            $(selectAllCheckbox).
            is(":checked")).change();
};

// dynamically add class form-control to all form elements to
// make inputs prettier
const addCssClassToInputs = (container) => {
    // dynamically add class form-control to all form elements to
    // make inputs prettier
    $(container).find("input, select, textarea")
            .not("input[type=checkbox]")
            .not("input[type=radio]")
            .not("input[type=button]")
            .not("input[type=submit]")
            .not("input[type=reset]")
            .not("input[type=image]")
            .addClass("form-control");
};

const getSelect2Language = () => {
    return $("html").data("select2-language");
};

const initSelect2 = (container) => {
    // prettier select-boxes
    $(container).find("select").select2({
        width: "100%",
        language: getSelect2Language()
    });
};

const initBootstrapToggle = (container) => {
     // Toggle switches for some checkboxes
    $(container).find(".js-switch").bootstrapToggle({
        on: MenuTranslation.On,
        off: MenuTranslation.Off
    });
}