/* global bootbox */

$(() => {
    $("form#update-manager").submit((event) => {
        event.preventDefault();

        const packages = $('form#update-manager .package:checked').map(
                (_, el) => $(el).val()
        ).get();
        if (packages.length > 0) {
            const url = "index.php?action=install_modules&packages="
                    + packages.join(",");
            location.replace(url);
        } else {
            bootbox.alert($("#translation_please_select_packages").data(
                    "translation"));
        }
    });

    $('.checkall').on('click', (event) => {
        $("form#update-manager .package").prop('checked',
                event.currentTarget.checked);
    });
});