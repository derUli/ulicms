<?php
define("TRANSLATION_YOUR_LOGO", "Ihr Logo");
define("TRANSLATION_UPLOAD_NEW_LOGO", "Neues Logo hochladen");
define("TRANSLATION_UPLOAD", "Hochladen");
define("TRANSLATION_LOGO_INFOTEXT", "Laden Sie ein beliebiges Logo hoch, welches im Head Bereich Ihrer Homepage angezeigt wird.<br/>
Sie können das Logo in den Grundeinstellungen deaktivieren.");
define("TRANSLATION_UPLOADED_IMAGE_TOO_BIG", "Die von Ihnen hochgeladene Grafik ist zu groß.");
define("TRANSLATION_CREATE_OPTION", "Konfigurationsvariable erstellen");
define("TRANSLATION_OPTION", "Option");
define("TRANSLATION_VALUE", "Wert");

// translations for smtp_encryption select field
add_translation("smtp_encryption", "Verschlüsselung");
add_translation("unencrypted", "Unverschlüsselt");

add_translation("smtp_no_verify_certificate", "Zertifikate nicht validieren");
add_translation("smtp_no_verify_certificate_warning", "Nutzen Sie diese Option nur, wenn es unvermeidlich ist. Damit verringern Sie die Sicherheit der verschlüsselten Verbindung erheblich.");

add_translation("google_fonts_privacy_warning", "<strong>Achtung</strong> " . "Sie haben eine Google Font ausgewählt. Die Nutzung von Google Fonts kann mitunter einen Verstoß gegen den Datenschutz darstellen.");

add_translation("privacy", "Datenschutz");
add_translation("privacy_policy_checkbox_enable", "Aktiviere DSGVO Checkbox für Formulare");
add_translation("privacy_policy_checkbox_text", "Text für DSGVO Checkbox");
add_translation("dsgvo_checkbox", "DSGVO Checkbox");

add_translation("no_auto_cron_help", "Aktivieren Sie diese Option, wenn Sie einen richtigen Cronjob einrichten möchten.\nWenn diese Option nicht aktiviert ist, werden Cronjobs beim Laden einer Seite ausgefürt, was die Ladezeiten verschlechtert.");