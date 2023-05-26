/* global SettingsTranslation */

$(() => {
    $("#spamfilter_enabled").change((event) => {
        if (event.target.checked) {
            $('#country_filter_settings').slideDown();
        } else {
            $('#country_filter_settings').slideUp();
        }
    });
});