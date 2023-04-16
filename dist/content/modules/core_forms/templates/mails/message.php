<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta charset="utf-8">
    </head>
    <body>
        <table border="1">
            <tbody>
                <?php foreach (\App\Storages\ViewBag::get('data') as $label => $value) { ?>
                    <tr>
                        <td><strong><?php esc($label); ?></strong></td>
                        <td><?php echo nl2br(_esc($value)); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </body>
</html>
