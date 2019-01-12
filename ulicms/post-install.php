<?php
$migrator = new DBMigrator("module/message_service", ModuleHelper::buildModuleRessourcePath("message_service", "sql/up"));
$migrator->migrate();