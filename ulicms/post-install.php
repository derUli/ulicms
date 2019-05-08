<?php

$migrator = new DBMigrator("text_rotator",
        ModuleHelper::buildRessourcePath("text_rotator", "migrations/up"));
$migrator->migrate();
