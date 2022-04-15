<?php

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

html5_doctype();
og_html_prefix();
?>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    og_tags();
    enqueueStylesheet(getModulePath("bootstrap") . "css/bootstrap.min.css");
    enqueueStylesheet(getTemplateDirPath(get_theme()) . "styles/style.scss");
    combinedStylesheetHtml();

    base_metas();
    ?>
    <meta name="theme-color" content="<?php esc(Settings::get("header-background-color")); ?>;" />
    <style>
        header.header .hamburger .line:nth-child(1),
        header.header .hamburger .line:nth-child(3) {
            background: <?php echo getBarColor1(); ?>;
        }

        .header.header h1 {
            color: <?php echo getHeadlineColor(); ?>;
        }

        header.header .hamburger .line:nth-child(2){
            background: <?php echo getBarColor2(); ?>;
        }
    </style>
</head>
<body class="<?php body_classes(); ?>">
    <div class="root">
        <header class="header">
            <div class="flex">
                <div class="hamburger">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <?php Template::headline('<div class="headline"><h1>%title%</h1></div>'); ?> 
                <?php
                if (getconfig("logo_disabled") == "no") {
                    echo '<a href="./" class="logo-wrapper">';
                    Template::logo();
                    echo '</a>';
                }
                ?>
            </div>
            <nav class="navbar active">
                <?php menu("top"); ?>
            </nav>
        </header>
        <main>
            <article>