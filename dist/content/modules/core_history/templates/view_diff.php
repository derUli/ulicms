<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$diff = ControllerRegistry::get('PageController')->_diffContents();
?>
<p>
    <a
        href="<?php
        echo \App\Helpers\ModuleHelper::buildActionURL(
            'restore_version',
            'content_id=' . $diff->content_id
        );
?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
        <?php translate('back'); ?></a>
</p>
<h1><?php translate('diff'); ?></h1>
<p><?php
    translate(
        'COMPARE_VERSION_FROM_TO',
        [
            '%current%' => $diff->current_version_date,
            '%old_version%' => $diff->old_version_date
        ]
    );
?></p>

<div class="diff">
    <?php echo nl2br($diff->html); ?>
</div>
<p>
    <a
        href="<?php
    echo \App\Helpers\ModuleHelper::buildMethodCallUrl(
        'HistoryController',
        'doRestore',
        'version_id=' .
                                        $diff->history_id
    );
?>"
        class="btn btn-danger voffset3"
        onclick="return confirm(
                            '<?php translate('ask_for_restore'); ?>'
                            );"><i class="fas fa-undo"></i>
        <?php translate('restore'); ?></a>

</p>
</div>
