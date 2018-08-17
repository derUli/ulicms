<?php
$migrator = new DBMigrator("module/gallery2018", ModuleHelper::buildModuleRessourcePath("gallery2018", "sql/up"));
$migrator->migrate();