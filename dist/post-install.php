<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Database\DBMigrator;
use App\Helpers\ModuleHelper;

$migrator = new DBMigrator('package/better_cron', ModuleHelper::buildRessourcePath('better_cron', 'sql/up'));
$migrator->migrate();
