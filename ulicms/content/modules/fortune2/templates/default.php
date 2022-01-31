<?php
$controller = ControllerRegistry::get(getModuleMeta("fortune2", "main_class"));
?>
<div class="fortune">
    <?php
    $fortune = $controller->getRandomFortune();
    echo nl2br(_esc($fortune));
    ?>
</div>
