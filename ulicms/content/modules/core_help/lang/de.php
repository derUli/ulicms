<?php

add_translation("HELP", "Hilfe");
add_translation("UNKNOWN_TOPIC", "Unbekanntes Thema");
add_translation(
        "PATCH_INSTALL_HELP",
        file_get_contents(
                ModuleHelper::buildModuleRessourcePath(
                        "core_help",
                        "docs/de/patch_install_help.html",
                        true
                )
        )
);
add_translation(
        "HELP_IS_ADMIN",
        "Diese Option deaktiviert alle Zugriffsbeschränkungen für "
        . "diesen Nutzer. Er hat somit vollen Zugriff auf das System."
);
