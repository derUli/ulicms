<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<?php

$isWritable = is_writable(ULICMS_ROOT);
?>

<ul>
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
</ul>

<p>
    <a href="?step=4" class="btn btn-primary"><i class="fas fa-check"></i> <?php echo TRANSLATION_NEXT; ?></a>
</p>