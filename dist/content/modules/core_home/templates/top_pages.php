<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$controller = ControllerRegistry::get('HomeController');
$model = $controller->getModel();
?>
<table class="table">
    <thead>
    <tr>
        <th><?php translate('title'); ?>
        </th>
        <th><?php translate('views'); ?>
        </th>
    </tr>
</thead>
<tbody>
    <?php
    foreach ($model->topPages as $row) {
        $domain = getDomainByLanguage($row->language);
        if (! $domain) {
            $url = '../' . $row->slug;
        } else {
            $url = 'http://' . $domain . '/' . $row->slug;
        }
        ?>
        <tr>
            <td><a href="<?php echo $url; ?>"
                   target="_blank"><?php esc($row->title); ?></a></td>
            <td class="text-right"><?php echo $row->views; ?></td>
            <?php
    }
?>
    </tr>
</tbody>
</table>