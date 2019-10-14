
<ul>
    <?php
    foreach (getOnlineUsers() as $user) {
        ?>
        <li><?php Template::escape($user); ?></li>
    <?php } ?>
</ul>