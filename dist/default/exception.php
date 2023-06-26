<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php translate('error'); ?></title>
        <style>
<?php
        readfile(\App\Utils\Path::resolve('ULICMS_ROOT/lib/css/core.scss'));
readfile(\App\Utils\Path::resolve('ULICMS_ROOT/admin/css/main.scss'));
?> body {
                padding: 10px;
            }
        </style>
    </head>

    <body>
        <h1><?php translate('error'); ?></h1>
        <blockquote>
            <?php echo \App\Storages\ViewBag::get('exception'); ?>
        </blockquote>
    </body>
</html>