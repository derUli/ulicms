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
            "node_modules/fullpage.js/dist/fullpage.min.css");
    enqueueStylesheet(
            getTemplateDirPath(
                    get_theme()
            ) .
            "css/main.scss");
    combinedStylesheetHtml();
    echo \UliCMS\HTML\Style::fromString($css);

    $titles = [get_translation("frontpage")];
    $filteredPages = [];

    $slugs = ["frontpage"];
    foreach ($pages as $index => $page) {
        if ($page->isDeleted() || $page->language !== getCurrentLanguage() ||
                !$page->isRegular() ||
                (!$page->active && !is_logged_in()) ||
                !$permissionChecker->canRead($page->id)) {
            continue;
        }
        $slugs[] = $page->slug;
        $titles[] = $page->title;
        $filteredPages[] = $page;
    }

    $slugAttr = implode("||", $slugs);
    $titleAttr = implode("||", $titles);
    ?>

</head>
<body class="<?php body_classes(); ?>">
    <div class="main"
         id="fullpage"
         data-slugs="<?php esc($slugAttr); ?>"
         data-titles="<?php esc($titleAttr); ?>"
         >
        <div class="section start-page">
            <?php Template::logo();
            ?>
            <blockquote class="site-slogan text-fade-in">
                <?php Template::siteSlogan(); ?> </blockquote>
            <?php
            if (count($pages)) {
                $firstPage = $pages[0];
                ?>
                <?php if (count($slugs) >= 2) { ?>
                    <a href="#<?php esc($slugs[1]); ?>" class="button move-down"><?php esc($firstPage->getHeadline()); ?></a>
                <?php } ?>
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
        </div>
        <?php
        $color = 0;
        foreach ($filteredPages as $index => $page) {

            set_requested_pagename($page->slug, $page->language);
            $text_position = get_text_position();
            $color ++;
            ?>
            <div class="section bgcolor<?php echo $color; ?>">
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
                <footer style="display: none">
                    <?php Template::footerText(); ?>
                </footer>
            </div>
            <?php
            if ($color >= 5) {
                $color = 0;
            }
        }
        ?>
    </div>