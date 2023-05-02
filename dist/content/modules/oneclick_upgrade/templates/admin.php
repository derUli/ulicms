<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Storages\ViewBag;

use function App\HTML\text;

?>
<form action="<?php echo getModuleAdminSelfPath(); ?>"
        method="post">
            <?php csrf_token_html(); ?>
    <div>
        <label for="oneclick_upgrade_channel"><?php translate('channel'); ?></label><br />
        <select
            name="oneclick_upgrade_channel"
            size=1
            id="oneclick_upgrade_channel">
                <?php foreach(ViewBag::get('channels') as $channel) { ?>
                <option value="<?php Template::escape($channel); ?>"
                <?php

                if (ViewBag::get('oneclick_upgrade_channel') === $channel) {
                    echo ' selected';
                }
                    ?>><?php Template::escape(get_translation($channel)); ?></option>
                    <?php } ?>

        </select>
    </div>
    <div id="help-texts" class="voffset3 alert alert-info">
        <div data-channel="fast">
            <?php echo text(get_translation('fast_description')); ?>
        </div>

        <div data-channel="slow">
            <?php echo text(get_translation('slow_description')); ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> <?php translate('save'); ?></button>
</form>
<?php
enqueueScriptFile(
    \App\Helpers\ModuleHelper::buildRessourcePath(
        'oneclick_upgrade',
        'js/settings.js'
    )
);
combinedScriptHtml();
