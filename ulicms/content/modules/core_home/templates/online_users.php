<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use function UliCMS\HTML\imageTag;

$users = ViewBag::get("users");
?>
<div class="online-users">
    <?php foreach ($users as $user) { ?>
        <div class="online-user">
            <?php
            echo imageTag(
                    $user->getAvatar(),
                    ["class" => "img-responsive"]
            );
            ?>
            <div class="username">
                <?php esc($user->getUsername()); ?>
            </div>
        </div>
    <?php }
    ?>
</div>