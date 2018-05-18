<?php
$migrator = new DBMigrator("module/mail_queue", ModuleHelper::buildRessourcePath("mail_queue", "sql/up"));
$migrator->migrate();