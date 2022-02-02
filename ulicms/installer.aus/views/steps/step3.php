<?php

use UliCMS\Utils\VersionComparison;

$minPhpRequired = "8.0.2";

$phpVersion = phpversion();

$phpVersionCompatible = \UliCMS\Utils\VersionComparison\compare(
        $phpVersion,
        $minPhpRequired,
        ">="
);

$phpModules = [
    "mysqli",
    "gd",
    "json",
    "mbstring",
    "openssl",
    "dom",
    "xml",
    "intl"
];
?>

<h2><?php echo TRANSLATION_PHP_MODULES; ?></h2>
<ul>
    <li>
        <?php echo htmlspecialchars(TRANSLATION_PHP_VERSION); ?>
        <?php echo $minPhpRequired; ?>
        <?php
        if ($phpVersionCompatible) {
            echo '<i class="fa fa-check text-green" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-exclamation-triangle text-red" aria-hidden="true"></i>';
        }
        ?>
    </li>
    <?php
    foreach ($phpModules as $module) {
        $check = extension_loaded($module);
        echo "<li>";
        echo htmlspecialchars($module) . " ";

        if ($check) {
            echo '<i class="fa fa-check text-green" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-exclamation-triangle text-red" aria-hidden="true"></i>';
        }

        echo "</li>";
    }
    ?>
</ul>

<p>
    <a href="?step=4" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_ACCEPT_LICNSE; ?></a>
</p>