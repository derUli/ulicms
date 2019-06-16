<?php
$id = get_ID();
if ($id !== null) {
    $image = new Image_Page();
    $image->loadByID($id);
    if ($image->image_url !== null and ! empty($image->image_url)) {
        ?>
        <div class="ulicms-content-image">
            <img src="<?php Template::escape($image->image_url) ?>">
        </div>

        <?php
    }
}
