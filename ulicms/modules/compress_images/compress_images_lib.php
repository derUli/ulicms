<?php
function compress_image($source_url, $destination_url, $quality) {
	$info = getimagesize($source_url);
 
	if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
	elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
	elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
 
        imagealphablending( $image, false );
        imagesavealpha( $image, true );
 
	//save file
	if($info['mime'] == 'image/jpeg'))
           imagejpeg($image, $destination_url, $quality);
        else if($info['mime'] == 'image/gif'))
           imagegif($image, $destination_url);
        else if($info['mime'] == 'image/png'))
           imagepng($image, $destination_url, 9);
	
 
	//return destination file
	return $destination_url;
}
