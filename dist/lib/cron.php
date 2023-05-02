<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

// Run cron tasks of modules
do_event('cron');
