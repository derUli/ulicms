<?php
$migrator = new DBMigrator("module/gallery2019", ModuleHelper::buildModuleRessourcePath("gallery2019", "sql/up"));
$migrator->migrate();