<?php
$gallery = ViewBag::get("gallery");
$images = $gallery->getImages();
$gridcount = 0;
$imageColWidth = 3;
?>
<div class="gallery">
	<div class="row">
    	<?php foreach($images as $image){?>
		  <div class="col-md-<?php esc($imageColWidth);?>">
			<div class="thumbnail">
				<a href="<?php esc($image->getPath());?>"
					data-lightbox="gallery<?php esc($image->getGalleryId());?>"
					data-title="<?php esc($image->getDescription());?>"><img
					src="<?php esc($image->getPath());?>"
					alt="<?php esc($image->getDescription());?>"> </a>
			</div>
		</div>
	<?php
        
        $gridCount += $imageColWidth;
        if ($gridCount >= 12) {
            $gridCount = 0;
            ?>
	    </div>

	<div class="row">
<?php
        }
        ?>
<?php }?>

</div>