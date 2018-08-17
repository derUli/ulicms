<?php
$migrator = new DBMigrator("module/gallery2019", ModuleHelper::buildModuleRessourcePath("gallery2018", "sql/up"));
$migrator->migrate();