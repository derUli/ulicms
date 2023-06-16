<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo InstallerController::getTitle(); ?> - <?php echo APPLICATION_TITLE; ?> </title>
        <link rel="stylesheet" type="text/css"
              href="../content/modules/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="media/style.css" />
        <link rel="stylesheet"
              href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    </head>
    <body>
        <div class="container" id="root-container">
            <div class="row">
                <div class="col col-sm-12" id="header">
                    <p>
                        <img src="../admin/gfx/logo.png" alt="UliCMS">
                    </p>
                    <p>
                        <strong><?php echo TRANSLATION_INSTALLATION; ?></strong>
                    </p>
                </div>
            </div>
            <div class="row" id="my-container">
                <div class="col col-sm-4" id="steps">
                    <ol id="navigation">
                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                            <li><a href="index.php?step=<?php echo $i; ?>"
                                   class="<?php
                                   if ($i == InstallerController::getStep()) {
                                       echo 'current-item';
                                   }
                            ?>">
                                    <?php echo constant('TRANSLATION_TITLE_STEP_' . $i); ?></a></li>
                        <?php } ?>
                    </ol>
                </div>
                <div class="col col-sm-8" id="main">
                    <h1><?php echo InstallerController::getTitle(); ?></h1>
