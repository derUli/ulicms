<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php translate("error"); ?></title>
        <style>
<?php
readfile(Path::resolve("ULICMS_ROOT/core.css "));
readfile(Path::resolve("ULICMS_ROOT/admin/css/modern.scss "));
?> body {
                padding: 10px;
            }
        </style>
    </head>

    <body>
        <h1><?php translate("error"); ?></h1>
        <blockquote>
            <?php echo ViewBag::get("exception"); ?>
        </blockquote>
    </body>
</html>