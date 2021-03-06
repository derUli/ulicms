<?php

add_translation("HELP", "Help");
add_translation("UNKNOWN_TOPIC", "Unknown Topic");
add_translation(
    "PATCH_INSTALL_HELP",
    file_get_contents(
        ModuleHelper::buildModuleRessourcePath(
            "core_help",
            "docs/en/patch_install_help.html",
            true
        )
    )
);
add_translation("HELP_IS_ADMIN", "This option disables all "
        . "access restrictions. The user has full access to the system.");
