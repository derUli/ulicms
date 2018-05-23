<?php
$migrator = new DBMigrator("module/newsletter2", ModuleHelper::buildRessourcePath("newsletter2", "sql/up"));
$migrator->migrate();