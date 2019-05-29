<?php
$permissionChecker = new UliCMS\Security\ContentPermissionChecker(get_user_id());

html5_doctype();
og_html_prefix();

$pages = ContentFactory::getAllByMenu("top", "position");

$frontpagePhotoFile = ULICMS_CONTENT . "/images/theme/frontpage_photo.png";

$frontpagePhoto = file_exists($frontpagePhotoFile) ? UliCMS\HTML\imageTag("content/images/theme/frontpage_photo.png"
        ) : null;
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
            <blockquote class="site-slogan text-fade-in">
                <strong><?php Template::motto(); ?></strong> </blockquote>
            <?php
            if (count($pages)) {
                $firstPage = $pages[0];
                ?>
                <a href="#" class="button move-down"><?php esc($firstPage->getHeadline()); ?></a>
                <?php if ($frontpagePhoto) { ?>
                    <div id="frontpage-photo">
                        <?php echo $frontpagePhoto; ?>
                    </div>
                    <?php
                }
                ?>
            <?php } ?>
            <div class="advertisement">
                <?php random_banner(); ?>
            </div>
        </section>
        <?php
        $color = 0;
        foreach ($pages as $index => $page) {
            if ($page->language !== getCurrentLanguage() ||
                    !$page->isRegular() ||
                    (!$page->active && !is_logged_in()) ||
                    !$permissionChecker->canRead($page->id)) {
                continue;
            }
            set_requested_pagename($page->slug, $page->language);
            $text_position = get_text_position();
            $color ++;
            ?>
            <section class="bgcolor bgcolor<?php echo $color; ?>">
                <div class="content">
                    <?php
                    echo $page->getShowHeadline() ? "<h1 class=\"sliding\">{$page->getHeadline()}</h1>" : "";
                    ?>

                    <div class="text-content">
                        <?php
                        if ($text_position == "after") {
                            Template::outputContentElement();
                        }

                        if ($page instanceof Article and
                                $page->article_image) {
                            echo UliCMS\HTML\imageTag($page->article_image, ["class" => "article-image"]);
                        }
                        content();
                        if ($text_position == "before") {
                            Template::outputContentElement();
                        }
                        ?>
                    </div>

                    <?php Template::comments(); ?>
                </div>
                <footer>
                    <?php Template::footerText(); ?>
                </footer>
            </section>
            <?php
            if ($color >= 4) {
                $color = 0;
            }
        }
        ?>
    </div>