<?php
define("TRANSLATION_YOUR_LOGO", "Your Logo");
define("TRANSLATION_UPLOAD_NEW_LOGO", "Upload new logo");
define("TRANSLATION_UPLOAD", "Upload");
define("TRANSLATION_LOGO_INFOTEXT", "You can upload logo here.");
define("TRANSLATION_UPLOADED_IMAGE_TOO_BIG", "Your uploaded image was to big.");
define("TRANSLATION_CREATE_OPTION", "Create configuration variable");
define("TRANSLATION_OPTION", "Variable");
define("TRANSLATION_VALUE", "Value");

// translations for smtp_encryption select field
add_translation("smtp_encryption", "Encryption");
add_translation("unencrypted", "unencrypted");

// translations for smtp_no_verify_certificate checkbox field
add_translation("smtp_no_verify_certificate", "Disable certificate validation");
add_translation("smtp_no_verify_certificate_warning", "Use this only if it's unavoidable. This will decrease the security of the connection.");

add_translation("google_fonts_privacy_warning", "<strong>Warning</strong> " . "You've selected a google font. This could be a privacy issue dependent on the laws in your country.");

add_translation("privacy", "Privacy");

add_translation("privacy_policy_checkbox_enable", "Enable GDPR Checkbox for forms");
add_translation("privacy_policy_checkbox_text", "Text for  GDPR Checkbox");
add_translation("dsgvo_checkbox", "GDPR Checkbox");

add_translation("no_auto_cron_help", "Enable this option if you wan't to setup a real cronjob.\nIf this option is disabled cronjobs will be executed while page load. This may decrease the site performance.");

add_translation("language_shortcode", "Shortcode");

add_translation("reject_requests_from_bots", "Reject Requests From Bots");

add_translation("performance", "Performance");

add_translation("community", "Community");
add_translation("community_settings", "Community Settings");
add_translation("comments_enabled", "Comments enabled");
add_translation("comments_must_be_approved", "Comments muste be approved");
add_translation("commentable_content_types", "Commentable Content Types");