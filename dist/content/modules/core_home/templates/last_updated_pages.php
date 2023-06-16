<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\DateTimeHelper;

$controller = ControllerRegistry::get('HomeController');
$model = $controller->getModel();
?>
<table class="table">
    <thead>
        <tr>
            <th><?php translate('title'); ?></th>
            <th><?php translate('date'); ?></th>
            <th><?php translate('done_by'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($model->lastModfiedPages as $row) {
            $domain = getDomainByLanguage($row->language);
            if (! $domain) {
                $url = '../' . $row->slug;
            } else {
                $url = 'http://' . $domain . '/' . $row->slug;
            }
            ?>
            <tr>
                <td><a href="<?php echo $url; ?>" target="_blank"><?php esc($row->title); ?></a></td>
                <td><?php echo DateTimeHelper::timestampToFormattedDateTime($row->lastmodified); ?></td>
                <td><?php
                    $autorName = $model->admins[$row->lastchangeby];
            if (! empty($autorName)) {
            } else {
                $autorName = $model->admins[$row->author_id];
            }
            echo $autorName;
            ?></td>
            </tr>
        <?php
        }
?>
</tbody>
</table>