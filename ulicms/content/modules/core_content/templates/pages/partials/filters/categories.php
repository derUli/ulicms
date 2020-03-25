<?php 
use UliCMS\Models\Content\Categories;
?>

<?php translate("category"); ?>
<?php
echo Categories::getHTMLSelect(null, true, "filter_category");