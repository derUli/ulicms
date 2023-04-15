<?php translate('hello_name', ['%firstname%' => \App\Storages\ViewBag::get('firstname'), '%lastname%' => \App\Storages\ViewBag::get('lastname')]); ?>


<?php translate('password_reset1', ['%ip%' => \App\Storages\ViewBag::get('ip'), '%domain%' => get_domain()]); ?>


<?php translate('password_reset2'); ?>


<?php echo \App\Storages\ViewBag::get('url'); ?>


<?php translate('password_reset3'); ?>
