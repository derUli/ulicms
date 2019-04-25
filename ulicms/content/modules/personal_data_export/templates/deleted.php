<?php
echo UliCMS\HTML\Alert::success(get_translation("deleted_personal_data_of_x", array("%name%" => UliCMS\Backend\BackendPageRenderer::getModel())));
?>

<div class="voffset2">
    <a href="<?php echo ModuleHelper::buildAdminURL(PersonalDataController::MODULE_NAME); ?>" class="btn btn-default">
        <i class="fas fa-check"></i> <?php translate("ok"); ?></a>
</div>