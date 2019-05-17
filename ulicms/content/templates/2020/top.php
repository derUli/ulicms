<?php
$permissionChecker = new UliCMS\Security\ContentPermissionChecker(get_user_id());

html5_doctype();
og_html_prefix();

$pages = ContentFactory::getAllByMenu("top", "position");
?>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    base_metas();
    og_tags();
    enqueueStylesheet(
            getTemplateDirPath(
                    get_theme()
            ) .
            "node_modules/onepage-scroll/onepage-scroll.css");
    enqueueStylesheet(
            getTemplateDirPath(
                    get_theme()
            ) .
            "css/main.scss");
    combinedStylesheetHtml();
    $css = ".start-page{ background-color:" . Settings::get("header-background-color") . ";}";
    echo \UliCMS\HTML\Style::FromString($css);
    ?>

</head>
<body class="<?php body_classes(); ?>">
    <div class="main">
        <section class="start-page">
            <?php Template::logo();
            ?>
            <p class="site-slogan"><strong><?php Template::motto(); ?> </strong></p>
            <?php
            if (count($pages)) {
                $firstPage = $pages[0];
                ?>
                <a href="#" class="button move-down"><?php esc($firstPage->getHeadline()); ?></a>
            <?php } ?>
            <div class="advertisement">
                <?php random_banner(); ?>
            </div>
        </section>
        <?php
        foreach ($pages as $index => $page) {
            if ($page->language !== getCurrentLanguage() ||
                    !$page->isRegular() ||
                    (!$page->active && !is_logged_in()) ||
                    !$permissionChecker->canRead($page->id)) {
                continue;
            }
            set_requested_pagename($page->slug, $page->language);
            ?>
            <section>
                <div class="content">
                    <?php
                    echo $page->getShowHeadline() ? "<h1>{$page->getHeadline()}</h1>" : "";
                    if ($text_position == "after") {
                        Template::outputContentElement();
                    }

                    content();

                    if ($text_position == "before") {
                        Template::outputContentElement();
                    }
                    ?>
                    <?php Template::comments(); ?>
                </div>
                <footer>
                    <?php Template::footerText(); ?>
                </footer>
            </section>
            <?php
        }
        ?>
    </div>