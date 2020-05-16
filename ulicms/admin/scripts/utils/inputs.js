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