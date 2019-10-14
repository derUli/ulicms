$(() => {
    $('.checkall').on('click', (event) => {
        $(event.target).closest('fieldset')
                .find('input[type=checkbox]')
                .prop('checked', $(event.target).prop('checked'));
    });
});