<?php 

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Storages\Vars;
?>
<div id="content-container">
    <div id="main-backend-content">
        <?php
        require_once Vars::get("action_filename");
        ?>
    </div>

    <div id="main-content-loadspinner">
        <?php require "inc/loadspinner.php"; ?>
    </div>
</div>