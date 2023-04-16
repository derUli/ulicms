<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$user = \App\Storages\ViewBag::get('user');

translate('hello_x', ['%x%' => $user->getFirstname()]);
?>,

<?php translate('admin_created_an_account', ['%url%' => \App\Storages\ViewBag::get('url')]); ?>

<?php translate('here_are_your_credentials'); ?>

<?php translate('username'); ?>: <?php esc($user->getUserName()); ?>

<?php translate('password'); ?>: <?php esc(\App\Storages\ViewBag::get('password')); ?>