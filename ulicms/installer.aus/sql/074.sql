UPDATE
    `{prefix}groups`
SET
    permissions = '{
   "audio":true,
   "audio_create":true,
   "audio_edit":true,
   "banners":true,
   "banners_create":true,
   "banners_edit":true,
   "cache":true,
   "categories":true,
   "categories_create":true,
   "categories_edit":true,
   "comments_manage":true,
   "community_settings":true,
   "dashboard":true,
   "default_access_restrictions_edit":true,
   "design":true,
   "expert_settings":true,
   "expert_settings_edit":true,
   "extend_upgrade_helper":true,
   "favicon":true,
   "files":true,
   "forms":true,
   "forms_create":true,
   "forms_edit":true,
   "fortune2_get":true,
   "fortune2_post":true,
   "fortune_settings":true,
   "groups":true,
   "groups_create":true,
   "groups_edit":true,
   "images":true,
   "info":true,
   "install_packages":true,
   "languages":true,
   "list_packages":true,
   "logo":true,
   "module_settings":true,
   "motd":true,
   "oneclick_upgrade_settings":true,
   "open_graph":true,
   "other":true,
   "pages":true,
   "pages_activate_others":true,
   "pages_activate_own":true,
   "pages_change_owner":true,
   "pages_create":true,
   "pages_edit_others":true,
   "pages_edit_own":true,
   "pages_edit_permissions":true,
   "pages_show_positions":true,
   "patch_management":true,
   "performance_settings":true,
   "pkg_settings":true,
   "privacy_settings":true,
   "remove_packages":true,
   "settings_simple":true,
   "spam_filter":true,
   "update_system":true,
   "upload_patches":true,
   "users":true,
   "users_create":true,
   "users_edit":true,
   "videos":true,
   "videos_create":true,
   "videos_edit":true}'
WHERE
    id = 1