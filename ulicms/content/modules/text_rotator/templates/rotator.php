<?php

use UliCMS\Backend\BackendPageRenderer;

$model = BackendPageRenderer::getModel();
?>
<span class="text-rotator"
      data-animation="<?php esc($model->getAnimation()); ?>"
      data-separator="<?php esc($model->getSeparator()); ?>"
      data-speed="<?php esc($model->getSpeed()); ?>"
      ><?php esc($model->getWords()); ?>
</span>