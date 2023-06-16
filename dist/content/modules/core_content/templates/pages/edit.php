<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

// TODO: Rewrite this view, move logic to PageController
// Join new and edit views to one form

use App\CoreContent\UIUtils;
use App\Helpers\NumberFormatHelper;
use App\HTML\Input;
use App\Models\Content\Categories;
use App\Models\Content\Comment;
use App\Models\Content\Language;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Models\Content\VCS;
use App\Security\Permissions\ContentPermissionChecker;
use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

use function App\HTML\icon;

$permissionChecker = new PermissionChecker(get_user_id());

// FIXME: Die SQL Statements in einen Controller bzw. Model auslagern.
$page = (int)$_GET['page'];
$result = Database::query('SELECT * FROM ' . Database::tableName('content') . " WHERE id='{$page}'");

$allThemes = getAllThemes();

$cols = Database::getColumnNames('content');

$sql = 'SELECT id, name FROM ' . Database::tableName('videos');
$videos = Database::query($sql);

$sql = 'SELECT id, name FROM ' . Database::tableName('audio');
$audios = Database::query($sql);

$users = getUsers();

$groups = Group::getAll();
$groupsSql = Database::query('SELECT id, name from ' . Database::tableName('groups'));

$pages_change_owner = $permissionChecker->hasPermission('pages_change_owner');

$types = get_available_post_types();

$pages_approve_own = $permissionChecker->hasPermission('pages_approve_own');
$pages_approve_others = $permissionChecker->hasPermission('pages_approve_others');

while ($row = Database::fetchObject($result)) {
    $list_data = new List_Data($row->id);
    $is_owner = $row->author_id == get_user_id();

    // TODO: refactor this into a method
    // Can the current user change the value of "active"?
    // If the page is not approved yet, then only permitted users
    // can activate it
    // On first activation of a page it's status is set to approved.
    // If the page was initially approved then any user with
    // edit permissions can change it.
    $canActivateThis = false;

    if ($row->approved) {
        $canActivateThis = true;
    } elseif ($is_owner && $pages_approve_own) {
        $canActivateThis = true;
    } elseif (! $is_owner && $pages_approve_others) {
        $canActivateThis = true;
    }

    $owner_group = $row->group_id;

    $checker = new ContentPermissionChecker(get_user_id());
    $can_edit_this = $checker->canWrite($row->id);

    $languageAssignment = getAllLanguages(true);
    if (count($languageAssignment) > 0 && ! in_array($row->language, $languageAssignment)) {
        $can_edit_this = false;
    }

    if (! $can_edit_this) {
        noPerms();
    } else {
        ?>
        <div class="loadspinner">
            <?php require 'inc/loadspinner.php'; ?>
        </div>
        <div class="pageform" style="display: none">
            <div class="top-bar">
                <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('pages'); ?>"
                    class="btn btn-light btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate('back'); ?></a>
                    <?php
                    echo \App\Helpers\ModuleHelper::buildMethodCallButton('PageController', 'delete', '<i class="fas fa-trash"></i> ' . get_translation('delete'), [
                        'class' => 'btn btn-danger',
                        'type' => 'submit'
                    ], [
                        'page' => $row->id
                    ], [
                        'class' => 'float-end  delete-form page-delete-form'
                    ]);
        ?>
                    <?php echo \App\Helpers\ModuleHelper::endForm(); ?>
            </div>
            <?php
            echo \App\Helpers\ModuleHelper::buildMethodCallForm('PageController', 'edit', [], 'post', [
                'id' => 'pageform-edit',
                'class' => 'pageform main-form edit-page-form',
                'data-get-content-types-url' => \App\Helpers\ModuleHelper::buildMethodCallUrl(PageController::class, 'getContentTypes'),
                'data-slug-free-url' => \App\Helpers\ModuleHelper::buildMethodCallUrl(PageController::class, 'nextFreeSlug'),
                'data-parent-pages-url' => \App\Helpers\ModuleHelper::buildMethodCallUrl(PageController::class, 'filterParentPages')
            ]);
        ?>
            <input type="hidden" name="edit_page" value="edit_page"> <input
                type="hidden" name="page_id" id="page_id"
                value="<?php echo $row->id; ?>">
            <div id="accordion-container">
                <h2 class="accordion-header"><?php translate('title_and_headline'); ?></h2>
                <div class="accordion-content">
                    <div class="field">
                        <strong class="field-label">
                            <?php translate('permalink'); ?>*
                        </strong>
                        <input
                            type="text" required="required" name="slug"
                            value="<?php esc($row->slug); ?>">
                    </div>

                    <div class="field">
                        <strong class="field-label">
                            <?php translate('page_title'); ?>*
                        </strong>

                        <input type="text" name="title"
                                value="<?php esc($row->title); ?>"
                                required>
                    </div>
                    <div class="typedep hide-on-snippet hide-on-non-regular">

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('ALTERNATE_TITLE'); ?> </strong>
                            <input type="text" name="alternate_title"
                                    value="<?php esc($row->alternate_title); ?>"> <small><?php translate('ALTERNATE_TITLE_INFO'); ?> </small>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('show_headline'); ?>
                            </strong>
                            <select name="show_headline">
                                <option value="1"
                                <?php
                            if ($row->show_headline == 1) {
                                echo 'selected';
                            }
        ?>><?php translate('yes'); ?></option>
                                <option value="0"
                                <?php
        if ($row->show_headline == 0) {
            echo 'selected';
        }
        ?>><?php translate('no'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="typedep show-on-snippet">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('snippet_code'); ?>
                            </strong>
                            <input
                                type="text" class="select-on-click"
                                value="<?php Template::escape('[include=' . $row->id . ']'); ?>" readonly>
                            <small><?php translate('snippet_code_help'); ?></small>
                        </div>
                    </div>
                </div>
                <h2 class="accordion-header"><?php translate('page_type'); ?></h2>
                <div class="accordion-content">
                    <div class="field">
                        <?php
                        foreach ($types as $type) {
                            $model = TypeMapper::getModel($type);
                            ?>
                            <div>
                                <input type="radio" name="type" id="type_<?php echo $type; ?>"
                                        value="<?php echo $type; ?>"
                                        <?php
                                        if ($type == $row->type) {
                                            echo 'checked';
                                        }
                            ?>> <label
                                        for="type_<?php echo $type; ?>">
                                            <?php
                                echo icon(
                                    $model->getIcon(),
                                    ['class' => 'type-icon']
                                );
                            ?>
                                            <?php translate($type); ?>
                                </label>
                            </div>
                        <?php }
                        ?>
                    </div>
                </div>
                <h2 class="accordion-header"><?php translate('menu_entry'); ?></h2>
                <div class="accordion-content">
                    <div class="field">
                        <strong class="field-label"><?php translate('language'); ?></strong>  <select
                            name="language">
                                <?php
                                $languages = getAllLanguages(true);

        $page_language = $row->language;

        $languagesCount = count($languages);

        for ($j = 0; $j < $languagesCount; $j++) {
            if ($languages[$j] === $page_language) {
                echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . '</option>';
            } else {
                echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . '</option>';
            }
        }

        $pages = getAllPages($page_language, 'title', false);
        ?>
                        </select>
                    </div>
                    <div class="typedep menu-stuff">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('menu'); ?>
                                <span
                                    class="has-help" onclick="$('div#menu_help').slideToggle()"><i class="fa fa-question-circle text-info" aria-hidden="true"></i></span>
                            </strong>
                            <select name="menu" size=1>
                                <?php
        foreach (get_all_menus() as $menu) {
            ?>
                                    <option
                                    <?php
            if ($row->menu == $menu) {
                echo 'selected="selected" ';
            }
            ?>
                                        value="<?php echo $menu; ?>">
                                            <?php translate($menu); ?>
                                    </option>
                                <?php }
        ?>
                            </select>
                        </div>
                        <div id="menu_help" class="help" style="display: none">
                            <?php echo nl2br(get_translation('help_menu')); ?>
                        </div>
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('position'); ?>
                                <span
                                    class="has-help"
                                    onclick="$('div#position_help').slideToggle()"><i class="fa fa-question-circle text-info" aria-hidden="true"></i></span>
                            </strong>
                            <input
                                type="number" name="position" required="required" min="0" step="1"
                                value="<?php esc($row->position); ?>">
                        </div>
                        <div id="position_help" class="help" style="display: none">
                            <?php echo nl2br(get_translation('help_position')); ?>
                        </div>

                        <div class="typedep" id="parent-div">
                            <div class="field">
                                <strong class="field-label">
                                    <?php translate('parent_id'); ?>
                                </strong> <select
                                    name="parent_id" size=1>
                                    <option value="NULL">
                                        [
                                        <?php translate('none'); ?>
                                        ]
                                    </option>
                                    <?php
            foreach ($pages as $key => $page) {
                ?>
                                        <option
                                            value="<?php echo $page['id']; ?>"
                                            <?php
                    if ($page['id'] == $row->parent_id) {
                        echo " selected='selected'";
                    }
                ?>>
                                            <?php esc($page['title']); ?>
                                            (ID:
                                            <?php echo $page['id']; ?>
                                            )
                                        </option>
                                    <?php }
            ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <strong class="field-label">
                            <?php translate('activated'); ?>
                        </strong>
                        <select
                            name="active" size=1
                            <?php
                            if (! $canActivateThis) {
                                echo 'disabled';
                            }
        ?>>
                            <option value="1"
                            <?php
        if ($row->active == 1) {
            echo 'selected';
        }
        ?>>
                                    <?php translate('enabled'); ?>
                            </option>
                            <option value="0"
                            <?php
        if ($row->active == 0) {
            echo 'selected';
        }
        ?>>
                                    <?php translate('disabled'); ?>
                            </option>
                        </select>
                    </div>
                    <div class="typedep" id="tab-target">

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('open_in'); ?>
                            </strong>
                            <select
                                name="target" size=1>
                                <option
                                <?php
            if ($row->target == '_self') {
                echo 'selected="selected" ';
            }
        ?>
                                    value="_self">
                                    <?php translate('target_self'); ?></option>
                                <option
                                <?php
        if ($row->target == '_blank') {
            echo 'selected="selected" ';
        }
        ?>
                                    value="_blank">
                                    <?php translate('target_blank'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="typedep" id="hidden-attrib">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('hidden'); ?>
                            </strong>
                            <select name="hidden" size="1"><option value="1">
                                    <?php translate('yes'); ?>
                                </option>
                                <option value="0" <?php
            if ($row->hidden == 0) {
                echo 'selected';
            }
        ?>>
                                        <?php translate('no'); ?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <strong class="field-label">
                            <?php translate('category'); ?>
                        </strong>
                        <?php echo Categories::getHTMLSelect($row->category_id); ?>
                    </div>
                    <div id="menu_image_div">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('menu_image'); ?>
                            </strong>

                            <input type="text" id="menu_image" name="menu_image"
                                    readonly="readonly" class="fm"
                                    value="<?php esc($row->menu_image); ?>"
                                    style="cursor: pointer" /> <a href="#"
                                    onclick="$('#menu_image').val('');
                                                        return false;"
                                    class="btn btn-light voffset2">
                                <i class="fa fa-eraser"></i> <?php translate('clear'); ?> </a>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-link">
                    <h2 class="accordion-header"><?php translate('link_url'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('link_url'); ?>
                            </strong>
                            <input
                                type="url" name="link_url"
                                value="<?php esc($row->link_url); ?>">
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-language-link" style="display: none;">
                    <h2 class="accordion-header"><?php translate('language_link'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('language_link'); ?>
                            </strong>

                            <select name="link_to_language">
                                <option value=""
                                <?php
                                if ($row->link_to_language === null) {
                                    echo 'selected';
                                }
        ?>>[<?php translate('none'); ?>]</option>
                                        <?php foreach (Language::getAllLanguages() as $language) { ?>
                                    <option value="<?php Template::escape($language->getID()); ?>"
                                    <?php
            if ($language->getID() == $row->link_to_language) {
                echo ' selected';
            }
                                            ?>><?php Template::escape($language->getName()); ?></option>
                                        <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-metadata" style="display: none">
                    <h2 class="accordion-header"><?php translate('metadata'); ?></h2>
                    <div class="accordion-content">

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('meta_description'); ?>
                            </strong>
                            <input
                                type="text" name="meta_description"
                                value="<?php esc($row->meta_description); ?>"
                                maxlength="200">
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('meta_keywords'); ?>
                            </strong>
                            <input type="text" name="meta_keywords"
                                    value="<?php esc($row->meta_keywords); ?>"
                                    maxlength="200"
                                    placeholder="<?php translate('comma_separated'); ?>">
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('robots'); ?>
                            </strong>
                            <?php
                            echo Input::singleSelect(
                                'robots',
                                $row->robots,
                                UIUtils::getRobotsListItems()
                            );
        ?>
                        </div>
                        <div class="typedep" id="article-metadata">

                            <div class="field">
                                <strong class="field-label">
                                    <?php translate('author_name'); ?>
                                </strong>
                                <input
                                    type="text" name="article_author_name"
                                    value="<?php echo _esc($row->article_author_name); ?>"
                                    maxlength="80">
                            </div>

                            <div class="field">
                                <strong class="field-label">
                                    <?php translate('author_email'); ?>
                                </strong>

                                <input type="email" name="article_author_email"
                                        value="<?php echo _esc($row->article_author_email); ?>"
                                        maxlength="80">
                            </div>


                            <div class="field">
                                <strong class="field-label">
                                    <?php translate('article_date'); ?>
                                </strong> 
                                <input
                                    name="article_date" type="text"
                                    class="datetimepicker"
                                    value="<?php
                if (! empty($row->article_date)) {
                    echo NumberFormatHelper::timestampToSqlDate(
                        strtotime($row->article_date)
                    );
                }
        ?>"
                                    step="any">
                            </div>

                            <div class="field">
                                <strong class="field-label"><?php translate('excerpt'); ?></strong>
                                <?php echo Input::editor('excerpt', $row->excerpt); ?>
                            </div>
                        </div>
                        <div class="typedep" id="tab-og" style="display: none">
                            <h3><?php translate('open_graph'); ?></h3>
                            <p><?php translate('og_help'); ?></p>

                            <div class="field">
                                <strong class="field-label">
                                    <?php translate('title'); ?>
                                </strong>
                                <input type="text" name="og_title"
                                        value="<?php esc($row->og_title); ?>">
                            </div>

                            <div class="field">
                                <strong class="field-label">
                                    <?php translate('description'); ?>
                                </strong>
                                <input type="text" name="og_description"
                                        value="<?php esc($row->og_description); ?>"></div>

                            <div class="field">
                                <strong class="field-label"><?php translate('type'); ?>
                                </strong>
                                <input type="text" id="og_image" name="og_image"
                                        readonly="readonly" class="fm"
                                        value="<?php esc($row->og_image); ?>"
                                        style="cursor: pointer" /> <a href="#"
                                        onclick="$('#og_image').val('');
                                                            return false;"
                                        class="btn btn-light voffset2">
                                    <i class="fa fa-eraser"></i>
                                    <?php translate('clear'); ?>
                            </div>
                            </a>
                            <?php
                            if (! empty($row->og_image)) {
                                $og_url = get_protocol_and_domain() . $row->og_image;
                                ?>
                                <div style="margin-top: 15px;">
                                    <img class="small-preview-image"
                                            src="<?php esc($og_url); ?>" />
                                </div>
                            <?php }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="custom_fields_container">
                    <?php
                    foreach (DefaultContentTypes::getAll() as $name => $type) {
                        $fields = $type->customFields;
                        if (count($fields) > 0) {
                            ?>
                            <div class="custom-field-tab" data-type="<?php echo $name; ?>">
                                <h2 class="accordion-header"><?php translate($type->customFieldTabTitle ?: $name); ?></h2>

                                <div class="accordion-content">
                                    <?php
                                    foreach ($fields as $field) {
                                        $field->name = "{$name}_{$field->name}";
                                        ?>
                                        <?php echo $field->render(CustomFields::get($field->name, $row->id, false)); ?>
                                    <?php }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
        ?>
                </div>
                <div class="typedep list-show" id="tab-list">
                    <h2 class="accordion-header"><?php translate('list_properties'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('type'); ?>
                            </strong>
                            <?php $types = get_available_post_types(); ?>
                            <select name="list_type">
                                <option value="null"
                                <?php
                    if ('null' == $list_data->type) {
                        echo 'selected';
                    }
        ?>>
                                    [<?php translate('every'); ?>]
                                </option>
                                <?php
        foreach ($types as $type) {
            if ($type == $list_data->type) {
                echo '<option value="' . $type . '" selected>' . get_translation($type) . '</option>';
            } else {
                echo '<option value="' . $type . '">' . get_translation($type) . '</option>';
            }
        }
        ?>
                            </select>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('language'); ?>
                            </strong>
                            <select name="list_language">
                                <option value=""
                                <?php
        if ($list_data->language === 'null') {
            echo 'selected';
        }
        ?>>[<?php translate('every'); ?>]</option>
                                        <?php
                $languages = getAllLanguages();
        $languagesCount = count($languages);
        for ($j = 0; $j < $languagesCount; $j++) {
            if ($list_data->language === $languages[$j]) {
                echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . '</option>';
            } else {
                echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . '</option>';
            }
        }
        ?>
                            </select>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('category'); ?>
                            </strong>
                            <?php
                            $lcat = $list_data->category_id;
        if ($lcat === null) {
            $lcat = -1;
        }
        ?>
                            <?php echo Categories::getHTMLSelect($lcat, true, 'list_category'); ?>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('menu'); ?>
                            </strong>
                            <select name="list_menu" size="1">
                                <option value="">[<?php translate('every'); ?>]</option>
                                <?php
            foreach (get_all_menus() as $menu) {
                ?>
                                    <option value="<?php echo $menu; ?>"
                                    <?php
                if ($menu == $list_data->menu) {
                    echo 'selected';
                }
                ?>>
                                            <?php translate($menu); ?></option>
                                    <?php }
            ?>
                            </select>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('parent_id'); ?>
                            </strong>
                            <select name="list_parent" size=1>
                                <option
                                <?php
                                if ($list_data->parent_id === null) {
                                    echo 'selected="selected"';
                                }
        ?>
                                    value="">
                                    [
                                    <?php translate('every'); ?>
                                    ]
                                </option>
                                <?php
        foreach ($pages as $key => $page) {
            ?>
                                    <option
                                        value="<?php echo $page['id']; ?>"
                                        <?php
                if ($list_data->parent_id === (int)($page['id'])) {
                    echo 'selected="selected"';
                }
            ?>>
                                        <?php esc($page['title']); ?>
                                        (ID:
                                        <?php echo $page['id']; ?>
                                        )
                                    </option>
                                <?php }
        ?>
                            </select>
                        </div>
                        <div class="field">
                            <strong class="field-label"><?php translate('order_by'); ?>
                            </strong>
                            <select name="list_order_by">
                                <?php foreach ($cols as $col) { ?>
                                    <option value="<?php echo $col; ?>"
                                    <?php
            if ($col == $list_data->order_by) {
                echo 'selected';
            }
                                    ?>><?php echo $col; ?></option>
                                        <?php } ?>
                            </select>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('order_direction'); ?>
                            </strong>
                            <select name="list_order_direction">
                                <option value="asc"><?php translate('asc'); ?></option>
                                <option value="desc"
                                <?php
                                if ($list_data->order_direction === 'desc') {
                                    echo ' selected';
                                }
        ?>><?php translate('desc'); ?></option>
                            </select>
                        </div>
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('entries_per_page'); ?>
                            </strong>
                            <input type="number" name="limit" min="0" step="1"
                                    value="<?php echo (int)($list_data->limit); ?>">
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('use_pagination'); ?>
                            </strong>

                            <select name="list_use_pagination">
                                <option value="1"
                                <?php
        if ($list_data->use_pagination) {
            echo 'selected';
        }
        ?>><?php translate('yes'); ?></option>
                                <option value="0"
                                <?php
        if (! $list_data->use_pagination) {
            echo 'selected';
        }
        ?>><?php translate('no'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-module" style="display: none;">
                    <h2 class="accordion-header"><?php translate('module'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('module'); ?></strong>
                            <select
                                name="module">
                                <option value="null"
                                <?php
        if ($module == null || empty($module)) {
            echo ' selected';
        }
        ?>>[<?php translate('none'); ?>]</option>
                                        <?php foreach (\App\Helpers\ModuleHelper::getAllEmbedModules() as $module) { ?>
                                    <option value="<?php echo $module; ?>"
                                    <?php
            if ($module == $row->module) {
                echo ' selected';
            }
                                            ?>><?php echo $module; ?></option>
                                        <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-video" style="display: none;">
                    <h2 class="accordion-header"><?php translate('video'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('video'); ?>
                            </strong>
                            <select
                                name="video">
                                <option value=""
                                <?php
                                if ($row->video == null || empty($row->video)) {
                                    echo ' selected';
                                }
        ?>>[<?php translate('none'); ?>]</option>
                                        <?php while ($row5 = Database::fetchObject($videos)) { ?>
                                    <option value="<?php echo $row5->id; ?>"
                                    <?php
            if ($row5->id == $row->video) {
                echo ' selected';
            }
                                            ?>><?php Template::escape($row5->name); ?> (ID: <?php echo $row5->id; ?>)</option>
                                        <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-audio" style="display: none;">
                    <h2 class="accordion-header"><?php translate('audio'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('audio'); ?>
                            </strong>
                            <select
                                name="audio">
                                <option value=""
                                <?php
                                if ($row->audio == null || empty($row->audio)) {
                                    echo ' selected';
                                }
        ?>>[<?php translate('none'); ?>]</option>
                                        <?php while ($row5 = Database::fetchObject($audios)) { ?>
                                    <option value="<?php echo $row5->id; ?>"
                                    <?php
            if ($row5->id == $row->audio) {
                echo ' selected';
            }
                                            ?>><?php Template::escape($row5->name); ?> (ID: <?php echo $row5->id; ?>)</option>
                                        <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-image" style="display: none;">
                    <h2 class="accordion-header"><?php translate('image'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <input type="text" id="image_url" name="image_url"
                                    readonly="readonly"
                                    class="fm"
                                    value="<?php Template::escape($row->image_url); ?>"
                                    style="cursor: pointer" /> <a href="#"
                                    onclick="$('#image_url').val('');
                                                        return false;"
                                    class="btn btn-light voffset2"><i class="fa fa-eraser"></i>
                                        <?php translate('clear'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-text-position" style="display: none">
                    <h2 class="accordion-header"><?php translate('position_of_description'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('position_of_description'); ?>
                            </strong>
                            <select name="text_position">
                                <option value="before"
                                <?php
                                if ($row->text_position == 'before') {
                                    echo 'selected';
                                }
        ?>><?php translate('description_before_content'); ?></option>
                                <option value="after"
                                <?php
        if ($row->text_position == 'after') {
            echo 'selected';
        }
        ?>><?php translate('description_after_content'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="article-image">
                    <h2 class="accordion-header"><?php translate('article_image'); ?></h2>
                    <div class="accordion-content">

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('article_image'); ?>
                            </strong>
                            <input type="text" id="article_image" name="article_image"
                                    readonly="readonly" class="fm"
                                    value="<?php echo _esc($row->article_image); ?>"
                                    style="cursor: pointer" maxlength="255" /> <a href="#"
                                    onclick="$('#article_image').val('');
                                                        return false;"
                                    class="btn btn-light voffset2">
                                <i class="fa fa-eraser"></i>
                                <?php translate('clear'); ?></a>
                        </div>
                    </div>
                </div>
                <div style="<?php
        echo ! $permissionChecker->hasPermission('pages_edit_permissions') ?
                'display:none' : '';
        ?>"
                        >
                    <h2 class="accordion-header"><?php translate('permissions'); ?></h2>
                    <div class="accordion-content">

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('owner'); ?>
                                <?php translate('user'); ?>
                            </strong>
                            <select name="author_id"
                            <?php
                            if (! $pages_change_owner) {
                                echo 'disabled';
                            }
        ?>>
                                    <?php
                    foreach ($users as $user) {
                        ?>
                                    <option value="<?php Template::escape($user['id']); ?>"
                                    <?php
                                    if ($user['id'] == $row->author_id) {
                                        echo 'selected';
                                    }
                        ?>><?php Template::escape($user['username']); ?></option>
                                        <?php }
                    ?>
                            </select>
                        </div>

                        <div class="field">
                            <strong class="field-label">
                                <?php translate('owner'); ?>
                                <?php translate('group'); ?>
                            </strong>
                            <select name="group_id"
                            <?php
                            if (! $pages_change_owner) {
                                echo 'disabled';
                            }
        ?>>
                                    <?php
                    foreach ($groups as $group) {
                        ?>
                                    <option value="<?php Template::escape($group->getId()); ?>"
                                    <?php
                                    if ($group->getId() == $row->group_id) {
                                        echo 'selected';
                                    }
                        ?>><?php Template::escape($group->getName()); ?></option>
                                        <?php }
                    ?>
                            </select>
                        </div>
                        <div class="field restrict-edit-access">
                            <strong class="field-label"><?php translate('restrict_edit_access'); ?></strong>
                            <div>
                                <input type="checkbox" name="only_admins_can_edit"
                                        id="only_admins_can_edit" value="1"
                                        <?php
                                        if ($row->only_admins_can_edit) {
                                            echo 'checked';
                                        }
        ?>> <label
                                        for="only_admins_can_edit"><?php translate('admins'); ?></label>
                            </div>
                            <div>
                                <input type="checkbox" name="only_group_can_edit"
                                        id="only_group_can_edit" value="1"
                                        <?php
        if ($row->only_group_can_edit) {
            echo 'checked';
        }
        ?>> <label
                                        for="only_group_can_edit"><?php translate('group'); ?></label>
                            </div>
                            <div>
                                <input type="checkbox" name="only_owner_can_edit"
                                        id="only_owner_can_edit" value="1"
                                        <?php
        if ($row->only_owner_can_edit) {
            echo 'checked';
        }
        ?>> <label
                                        for="only_owner_can_edit"><?php translate('owner'); ?></label>
                            </div>
                            <div>
                                <input type="checkbox" name="only_others_can_edit"
                                        id="only_others_can_edit" value="1"
                                        <?php
        if ($row->only_others_can_edit) {
            echo 'checked';
        }
        ?>> <label
                                        for="only_others_can_edit"><?php translate('others'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="typedep" id="tab-comments">
                    <h2 class="accordion-header"><?php translate('comments'); ?></h2>
                    <div class="accordion-content">
                        <div class="field">
                            <strong class="field-label"><?php translate('comments_enabled'); ?></strong>  <select
                                name="comments_enabled">
                                <option value="null"
                                        <?php echo $row->comments_enabled === null ? 'selected' : ''; ?>>[<?php translate('standard'); ?>]</option>
                                <option value="1"
                                        <?php echo $row->comments_enabled === '1' ? 'selected' : ''; ?>><?php translate('yes'); ?></option>
                                <option value="0"
                                        <?php echo $row->comments_enabled === '0' ? 'selected' : ''; ?>>
                                        <?php translate('no'); ?></option>
                            </select>
                        </div>
                        <?php
                        $hasComments = count(Comment::getAllByContentId($row->id)) >= 1;
        if ($hasComments && $permissionChecker->hasPermission('comments_manage')) {
            ?>
                            <div class="field">
                                <a
                                    href="<?php esc(\App\Helpers\ModuleHelper::buildMethodCallUrl(CommentsController::class, 'filterComments', "content_id={$row->id}")); ?>"
                                    class="btn btn-light" target="_blank"><i class="fa fa-comments"></i> <?php translate('comments_manage'); ?></a>
                            </div>
                        <?php }
        ?>
                    </div>
                </div>
                <h2 class="accordion-header"><?php translate('other'); ?></h2>
                <div class="accordion-content">
                    <div class="typedep" id="tab-cache-control" style="display: none;">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('cache_control'); ?>
                            </strong>
                            <select
                                name="cache_control">
                                <option value="auto"
                                <?php
                if ($row->cache_control == 'auto') {
                    echo 'selected';
                }
        ?>><?php translate('auto'); ?></option>
                                <option value="force"
                                <?php
        if ($row->cache_control == 'force') {
            echo 'selected';
        }
        ?>><?php translate('force'); ?></option>
                                <option value="no_cache"
                                <?php
        if ($row->cache_control == 'no_cache') {
            echo 'selected';
        }
        ?>><?php translate('no_cache'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="typedep" id="tab-menu-image">
                        <div class="field">
                            <strong class="field-label">
                                <?php translate('design'); ?>
                            </strong>
                            <select
                                name="theme" size=1>
                                <option value="">
                                    [
                                    <?php translate('standard'); ?>
                                    ]
                                </option>
                                <?php
        foreach ($allThemes as $th) {
            ?>
                                    <option
                                        value="<?php echo $th; ?>"
                                        <?php
                if ($row->theme == $th) {
                    echo 'selected';
                }
            ?>>
                                        <?php echo $th; ?>
                                    </option>
                                <?php }
        ?>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <strong class="field-label">
                            <?php translate('visible_for'); ?>
                        </strong>
                        <?php $access = explode(',', $row->access); ?>
                        <select name="access[]" size=4 multiple>
                            <option value="all"
                            <?php
                            if (in_array('all', $access)) {
                                echo ' selected';
                            }
        ?>>
                                    <?php translate('everyone'); ?></option>
                            <option value="registered"
                            <?php
        if (in_array('registered', $access)) {
            echo ' selected';
        }
        ?>>
                                    <?php translate('registered_users'); ?></option>
                            <option value="mobile"
                            <?php
        if (in_array('mobile', $access)) {
            echo ' selected';
        }
        ?>><?php translate('mobile_devices'); ?></option>
                            <option value="desktop"
                            <?php
        if (in_array('desktop', $access)) {
            echo ' selected';
        }
        ?>><?php translate('desktop_computers'); ?></option>
                                    <?php
                while ($row2 = Database::fetchObject($groupsSql)) {
                    if (in_array((string)$row2->id, $access)) {
                        echo '<option value="' . $row2->id . '" selected>' . _esc($row2->name) . '</option>';
                    } else {
                        echo '<option value="' . $row2->id . '">' . _esc($row2->name) . '</option>';
                    }
                }
        ?>
                        </select>
                    </div>
                    <div class="typedep" id="custom_data_json">
                        <?php do_event('before_custom_data_json'); ?>
                        <div class="field">
                            <strong class="field-label"><?php translate('custom_data_json'); ?></strong>
                            <textarea name="custom_data" style="width: 100%; height: 200px;"
                                        class="codemirror" data-mimetype="application/json" data-validate="json"
                                        cols=80 rows=10><?php esc($row->custom_data); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <?php do_event('page_option'); ?>
            <div class="typedep" id="content-editor">
                <p><?php echo Input::editor('content', $row->content); ?></p>
                <?php
                $rev = VCS::getRevisionsByContentID($row->id);
        if (count($rev) > 0) {
            ?>
                    <p>
                        <a
                            href="index.php?action=restore_version&content_id=<?php echo$row->id; ?>"
                            class="btn btn-warning"><i class="fas fa-undo"></i> <?php translate('restore_older_version'); ?></a>
                    </p>
                <?php }
        ?>	</div>
            <div class="inPageMessage">
                <img class="loading" src="gfx/loading.gif" alt="Wird gespeichert...">
            </div>
            <div class="row">
                <div class="col col--6">
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="far fa-save"></i> <?php translate('save_changes'); ?></button>
                </div>
                <div class="col col--6 text-right">
                    <button class="typedep btn btn-info" type="button" id="btn-view-page">
                        <i class="fas fa-eye"></i> <?php translate('view'); ?></button>
                </div>
            </div>
            <?php
            $translation = new JSTranslation([], 'PageTranslation');
        $translation->addKey('confirm_exit_without_save');
        $translation->addKey('fill_all_required_fields');
        $translation->render();

        $translation = new JSTranslation();
        $translation->addKey('ask_for_delete');
        $translation->addKey('page_saved');
        $translation->addKey('fill_all_required_fields');
        $translation->render();

        enqueueScriptFile('../node_modules/slug/slug.js');

        \App\Helpers\BackendHelper::enqueueEditorScripts();

        enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath('core_content', 'js/pages/form.js'));

        combinedScriptHtml();
        echo \App\Helpers\ModuleHelper::endForm();
        ?>
        </div>
        <?php
    }
}
