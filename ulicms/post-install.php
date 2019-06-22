<?php

$migrator = new DBMigrator("telegram",
        ModuleHelper::buildRessourcePath("telegram", "migrations/up"));
$migrator->migrate();
