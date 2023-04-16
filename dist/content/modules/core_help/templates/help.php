<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

switch ($_GET['help']) {
    case 'patch_install':
        translate('PATCH_INSTALL_HELP');
        break;
    default:
        translate('unknown_topic');
}
