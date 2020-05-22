<?php
$migrator = new DBMigrator("package/better_cron", ModuleHelper::buildRessourcePath("better_cron", "sql/up"));
$migrator->migrate();