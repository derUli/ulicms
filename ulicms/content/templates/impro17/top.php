<?php
html5_doctype();
og_html_prefix();
$site_slogan = get_site_slogan();
$data = get_custom_data();
if (isset($data["site_slogan"])) {
    $site_slogan = $data["site_slogan"];
}
$q = "";
if (isset($_GET["q"])) {
    $q = $_GET["q"];
}
$modules = getAllModules();
$hasSearch = (faster_in_array("search", $modules) || faster_in_array("extended_search", $modules));
$searchPage = ModuleHelper::getFirstPageWithModule("extended_search");
if (!$searchPage) {
    $searchPage = ModuleHelper::getFirstPageWithModule("search");
}
?>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="<?php echo getModulePath("bootstrap"); ?>css/bootstrap.min.css">
          <?php
          base_metas();
          og_tags();
          ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    <?php
    enqueueStylesheet(getTemplateDirPath("impro17") . "style.scss");
    combinedStylesheetHtml();
    ?>
</head>
<body class="<?php body_classes(); ?>">
    <div class="container" id="root">

        <div class="header clearfix">
            <nav>
                <?= jumbotron_get_menu("top"); ?>
            </nav>
            <h3 class="text-muted">
                <a href="/">
                    <?php homepage_title(); ?></a>
            </h3>

            <div id="mobile-nav"></div>
            <?php
            if ((!containsModule(null, "extended_search") && !containsModule(null, "search")) and $hasSearch and $searchPage) {
                ?>
                <form id="search-form-head" method="get"
                      action="<?php Template::escape(buildSEOURL($searchPage->slug)); ?>">
                    <input type="search" required="required" name="q"
                           value="<?php Template::escape($q); ?>" results="10"
                           autosave="<?php echo md5($_SERVER ["SERVER_NAME"]); ?>"
                           placeholder="<?php translate("search"); ?>...">
                </form>
            <?php
            } ?>
        </div>
        <?php if (is_frontpage()) { ?>
            <div class="jumbotron">
                <?php
                if (getconfig("logo_disabled") == "no") {
                    logo();
                }
                ?>
                <div class="lead"><?php echo Settings::get("motd"); ?></div>
                <p>
                    <a class="btn btn-lg btn-success" href="admin/" role="button"><?php translate("login") ?></a>
                </p>
            </div>
        <?php } ?>
        <div class="row marketing">
            <?php if ($site_slogan) { ?>
                <blockquote>
                    <?php Template::escape($site_slogan); ?></blockquote>
            <?php } ?>
            <main>
                <?php Template::headline(); ?>