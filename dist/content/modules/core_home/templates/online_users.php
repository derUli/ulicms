<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use function App\HTML\imageTag;

$users = \App\Storages\ViewBag::get('users');
?>
<div class="online-users">
    <?php foreach ($users as $user) { ?>
        <div class="online-user">
            <?php
            echo imageTag(
                $user->getAvatar(),
                ['class' => 'img-fluid']
            );
        ?>
            <div class="username">
                <?php esc($user->getUsername()); ?>
            </div>
        </div>
    <?php }
    ?>
</div>