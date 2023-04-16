<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<div id="content-container">
    <div id="main-backend-content">
        <?php
        require_once \App\Storages\Vars::get('action_filename');
        ?>
    </div>

    <div id="main-content-loadspinner">
        <?php require 'inc/loadspinner.php'; ?>
    </div>
</div>