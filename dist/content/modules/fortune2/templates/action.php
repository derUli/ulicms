<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Backend\BackendPageRenderer;

$fortune = BackendPageRenderer::getModel();
?>
<h1><?php translate('fortune'); ?></h1>
<blockquote class="fortune">
    <?php
    echo nl2br($fortune);
?>
</blockquote>