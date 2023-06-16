<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$currentAction = \App\Helpers\BackendHelper::getAction();
$icons = [
    'legal_composer' => 'fas fa-file-contract',
    'legal_npm' => 'fas fa-file-contract',
];

$selectedButton = 'btn btn-primary';
$notSelectedButton = 'btn btn-light';
?>
<div class="btn-toolbar" role="toolbar"
     aria-label="Toolbar with button groups">
    <div class="btn-group" role="group">
        <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('info'); ?>"
           class="btn btn-light btn-back is-ajax"
           ><i class="fa fa-arrow-left"></i>
            <?php translate('back'); ?></a>
    </div>
    <?php foreach ($icons as $action => $cssClass) { ?>
        <div class="btn-group" role="group">
            <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL($action); ?>"
               class="<?php
               echo $action == $currentAction ?
                       $selectedButton : $notSelectedButton;
        ?> is-ajax">
                <i class="<?php echo $cssClass; ?>"></i>
                <span class="hide-on-820">
                    <?php
             (
                 isset($specialLabels[$action]) ?
                                                            esc(
                                                                $specialLabels[$action]
                                                            ) : translate(
                                                                $action
                                                            )
             );
        ?></span>
            </a>
        </div>
    <?php } ?>
</div>