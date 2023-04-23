<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<?php
use App\Utils\VersionComparison;

$minPhpRequired = '8.1.0';

$phpVersion = PHP_VERSION;

$phpVersionCompatible = VersionComparison::compare(
    $phpVersion,
    $minPhpRequired,
    '>='
);

$isWritable = is_writable(ULICMS_ROOT);

$phpModules = [
    'mysqli',
    'gd',
    'json',
    'mbstring',
    'openssl',
    'dom',
    'xml',
    'curl',
    'intl'
];

sort($phpModules);
?>

<ul>
    <li>
        <?php echo TRANSLATION_PHP_VERSION; ?>
        <?php echo $minPhpRequired; ?>
        <?php
        if ($phpVersionCompatible) {
            echo '<i class="fa fa-check text-green" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-exclamation-triangle text-red" aria-hidden="true"></i>';
        }
?>
    </li>
    <li>
    <?php

        echo TRANSLATION_IS_WRITABLE . ' ';

        if ($isWritable) {
            echo '<i class="fa fa-check text-green" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-exclamation-triangle text-red" aria-hidden="true"></i>';
        }
?>
    </li>
    <?php
    foreach ($phpModules as $module) {
        $check = extension_loaded($module);
        echo '<li>';
        echo TRANSLATION_PHP_MODULE . ' ' . htmlspecialchars($module) . ' ';

        if ($check) {
            echo '<i class="fa fa-check text-green" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-exclamation-triangle text-red" aria-hidden="true"></i>';
        }

        echo '</li>';
    }
?>
</ul>

<p>
    <a href="?step=4" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_NEXT; ?></a>
</p>