<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\VCS;

$content_id = (int)$_GET['content_id'];
$revisions = VCS::getRevisionsByContentID($content_id);
?>
<p>
    <a
        href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('pages_edit', 'page=' . $content_id); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<h1><?php translate('versions'); ?></h1>
<div class="scroll">
    <table class="tablesorter">
        <thead>
            <tr>
                <th><?php translate('id'); ?></th>
                <th><?php translate('content'); ?></th>
                <th><?php translate('user'); ?></th>
                <th><?php translate('date'); ?></th>
                <th><?php translate('restore'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($revisions as $revision) {
                $view_diff_link = 'index.php?action=view_diff&content_id=' . $revision->content_id . '&history_id=' . $revision->id;
                ?>
                <tr>
                    <td><?php esc($revision->id); ?></td>
                    <td><a href="<?php echo $view_diff_link; ?>" class="btn btn-info"
                            target="_blank"><i class="fas fa-eye"></i> <?php translate('view_diff'); ?></a></td>
                    <td><?php
                        $user = getUserById((int)$revision->user_id);
                if ($user && isset($user ['username'])) {
                    esc($user ['username']);
                }
                ?></td>
                    <td><?php echo $revision->date; ?></td>
                    <td><a
                            href="<?php echo \App\Helpers\ModuleHelper::buildMethodCallUrl('HistoryController', 'doRestore', 'version_id=' . $revision->id); ?>"
                            class="btn btn-danger"
                            onclick="return confirm('<?php translate('ask_for_restore'); ?>');"><i class="fas fa-undo"></i>
                            <?php translate('restore'); ?></a>
                    </td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
</div>
