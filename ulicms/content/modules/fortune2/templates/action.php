<?php
$fortune = Model::getModel();
?>
<blockquote class="fortune">
<?php
echo nl2br($fortune);
?>
</blockquote>