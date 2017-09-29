<?php
$migrator = new DBMigrator ( "mod/todolist", ModuleHelper::buildModuleRessourcePath ( "todolist", "sql/up" ) );
$migrator->migrate();
