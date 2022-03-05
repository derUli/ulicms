<?php
html5_doctype();
og_html_prefix();
?>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    base_metas();
    og_tags();
    enqueueStylesheet(getModulePath("bootstrap", true) . "css/bootstrap.min.css");
    enqueueStylesheet(getTemplateDirPath(get_theme(), true) . "styles/style.scss");
    combinedStylesheetHtml();
    ?>
</head>
<body class="<?php body_classes(); ?>">
    <div class="root">
        <header>
            <div class="hamburger">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
            <nav class="navbar active">
                <?php menu("top"); ?>
            </nav>
        </header>
        <main>
            <?php Template::headline("<header><h1>%title%</h1></header>") ?>
            <article>