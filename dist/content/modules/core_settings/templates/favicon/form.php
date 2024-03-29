<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\ImagineHelper;
use App\Translations\JSTranslation;
use App\Utils\File;

use function App\HTML\imageTag;

?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('design'); ?>"
        class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
</p>
<?php
if (isset($_GET['error'])) {
    ?>
    <div class="alert alert-danger">
        <?php translate(_esc($_GET['error'])); ?>
    </div>
<?php }
?>
<h1><?php translate('favicon'); ?></h1>
<p><?php translate('favicon_infotext'); ?>
</p>
<form enctype="multipart/form-data" action="index.php?action=favicon"
        method="post">
    <input type="hidden" name="sClass" value="FaviconController"> <input
        type="hidden" name="sMethod" value="doUpload">
        <?php csrf_token_html(); ?>
    <table style="height: 250px">
        <tr>
            <td>
                <strong>
                    <?php translate('current_favicon'); ?>
                </strong>
            </td>
            <td>
                <div id="favicon-wrapper">
                    <?php
                    $favicon_path = ULICMS_ROOT . '/content/images/favicon.ico';
$faviconUrl = '../content/images/favicon.ico';
if (is_file($favicon_path)) {
    $faviconUrl .= '?time=' . File::getLastChanged($favicon_path);
    echo imageTag(
        $faviconUrl,
        ['alt' => Settings::get('homepage_title')]
    );
    ?>
                        <div class="voffset2">
                            <button
                                type="button"
                                class="btn btn-light"
                                id="delete-favicon"
                                data-url="<?php
            echo \App\Helpers\ModuleHelper::buildMethodCallUrl(
                FaviconController::class,
                'deleteFavicon'
            );
    ?>
                                "
                                >
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <?php translate('delete_favicon'); ?>
                            </button>
                        </div>
                    <?php }
?>
                </div>
                <img
                    id="delete-favicon-loading"
                    src="gfx/loading.gif"
                    alt="<?php translate('loading_alt'); ?>"
                    style="display: none;"
                    >
            </td>
        </tr>
        <tr>
            <td>
                <label for="high_resolution">
                    <strong>
                        <?php translate('high_resolution'); ?>
                    </strong>
                </label>
            </td>
            <td>
                <input
                    type="checkbox"
                    id="high_resolution"
                    name="high_resolution"
                    value="high_resolution"
                    class="js-switch"
                    >
            </td>
        </tr>
        <tr>
            <td width=480><strong><?php translate('upload_new_favicon'); ?>
                </strong></td>
            <td>
                <input
                    name="favicon_upload_file"
                    type="file"
                    required
                    class="form-control"
                    accept="<?php echo ImagineHelper::ACCEPT_MIMES; ?>"
                    >
                <br /></td>
        </tr>
        <tr>
            <td></td>
            <td class="text-center">
                <button type="submit"class="btn btn-primary">
                    <?php translate('upload'); ?>
                </button>
            </td>
        </tr>
    </table>
</form>
<?php
$translation = new JSTranslation();
$translation->addKey('delete_favicon');
$translation->addKey('favicon_deleted');
$translation->render();

enqueueScriptFile(
    \App\Helpers\ModuleHelper::buildRessourcePath(
        'core_settings',
        'js/favicon.js'
    )
);
combinedScriptHtml();
