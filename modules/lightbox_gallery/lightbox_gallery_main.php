<?php 
// Image Gallery Plugin 0.2 for UliCMS 2014 R2

// Main Function
function lightbox_gallery_render(){
	$gallery_image_folder = "content/files/gallery/";
	
	generateThumbnails($gallery_image_folder);
	generateBigImages($gallery_image_folder);

	if(!isset($_GET["img_id"])){
		return output_all($gallery_image_folder);
	}else{
		return output_single($gallery_image_folder, basename($_GET["img_id"]));
	}
	
	
	
	
}


// Output Single image
function output_single($gallery_image_folder, $id){
			$description_filename = $gallery_image_folder.$id.".txt";
			if(is_file($description_filename)){
					
					$description_content = htmlspecialchars(file_get_contents($description_filename));
				}else{
					$fhandle = fopen($description_filename, "a");
					fclose($fhandle);
					$description_content = "";
				}
	$html_output.="<p align='center'><a href='".get_requested_pagename().".html'>"."<img style='border:0px' src='".$gallery_image_folder.$id.".standard.jpg' alt='".$description_content."'/></a>
	<br/>".nl2br($description_content)."</p>";
	return $html_output;
	}


	
// Output All Images
function output_all($gallery_image_folder){
	if(getconfig("image_gallery_images_per_row") === false){
		setconfig("image_gallery_images_per_row", '3');
		
	}
	// Because setconfig() is broken in 4.3 due a typing error
	// use then standard value
	// user has to create mage_gallery_images_per_row config variable
	// manually
	if(getconfig("image_gallery_images_per_row") != false){
		$max_row_count = getconfig("image_gallery_images_per_row");
	}else{
		$max_row_count = 3;
	}
	
	$max_row_count = getconfig("image_gallery_images_per_row");
	
	$html_output = "";
	
	$directory_list = scandir($gallery_image_folder);
	$current_row_count = 0;
	for($i=0; $i<count($directory_list);$i++){
		
		
		$filename = $directory_list[$i];
			$exploded_filename = explode(".", $filename);
			if(count($exploded_filename)>1){
			

			if($exploded_filename[1] == "jpg"){
				$current_row_count++;
				if($current_row_count == 1){
					$html_output.="<p align='center'>\n";
			}
				$thumbnail_filename = $gallery_image_folder.$exploded_filename[0].".thumb.jpg";
				$standard_filename = $gallery_image_folder.$exploded_filename[0].".standard.jpg";
				$description_filename = $gallery_image_folder.$exploded_filename[0].".txt";
				
				if(file_exists($description_filename))
               			    $description_content = file_get_contents($description_filename);
               			else
               			    $description_content = "";
               			    
         			$title_attr = "";
               			    
               			if(!empty($description_content))
               			   $title_attr = "title='$description_content'";
				$path_to_original_image = $gallery_image_folder.$filename;
			
				$big_url = "".get_requested_pagename().".html?"."img_id=".$exploded_filename[0];
				$html_output.="<a href='$standard_filename' $title_attr rel='lightbox'>";
				$html_output.="<img src='".$thumbnail_filename."' style='margin-right:20px;border:0px;'";
				if(is_file($description_filename)){
					$description_content = htmlspecialchars(file_get_contents($description_filename));
					$html_output.=" alt='".$description_content."' title ='".$description_content."'";
				}else{
					$fhandle = fopen($description_filename, "a");
					fclose($fhandle);
					$description_content = "";
				}
				$html_output.="/>";
				$html_output.="</a>\n";
				}
			
		
		if($current_row_count == $max_row_count){
			$html_output.="</p>\n";
			$current_row_count = 0;
		}
		
		
	
	}
}

	return $html_output;
}


// Generate Thumbnails in Standard and Small Size
function generateThumbnails($gallery_image_folder){
	if(!file_exists($gallery_image_folder)){
		mkdir($gallery_image_folder, 0777, true);
	}
	$directory_list = scandir($gallery_image_folder);
	
	for($i=0; $i<count($directory_list);$i++){
	
		$filename = $directory_list[$i];
		
		$exploded_filename = explode(".", $filename);
		if(count($exploded_filename)>1)
			
			if($exploded_filename[1] == "jpg"){
				
				$thumbnail_filename = $gallery_image_folder.$exploded_filename[0].".thumb.jpg";
				$path_to_original_image = $gallery_image_folder.$filename;
				if(!file_exists($thumbnail_filename)){
					// get image size
					$image_size = getimagesize($path_to_original_image);
					$original_width = $image_size[0];
					$original_height = $image_size[1];
					
					// maximal thumbnail sizes
					$max_thumbnail_width = 250;
					$max_thumbnail_height = 250;
					
					$new_width = $original_width;
					$new_height = $original_height;
					
					resize_image($path_to_original_image, $thumbnail_filename, $max_thumbnail_width, $max_thumbnail_height, $crop=FALSE);
					
					
				
				}
			}
	}

}	
	
// Generate files in normal size
function generateBigImages($gallery_image_folder){
	if(!file_exists($gallery_image_folder)){
		mkdir($gallery_image_folder, 0777, true);
	}
	$directory_list = scandir($gallery_image_folder);
	
	for($i=0; $i<count($directory_list);$i++){
	
		$filename = $directory_list[$i];
		
		$exploded_filename = explode(".", $filename);
		if(count($exploded_filename)>1)

			if($exploded_filename[1] == "jpg"){
				
				$thumbnail_filename = $gallery_image_folder.$exploded_filename[0].".standard.jpg";
				$path_to_original_image = $gallery_image_folder.$filename;
				if(!file_exists($thumbnail_filename)){
					// get image size
					$image_size = getimagesize($path_to_original_image);
					$original_width = $image_size[0];
					$original_height = $image_size[1];
					
					// maximal thumbnail sizes
					$max_thumbnail_width = 550;
					$max_thumbnail_height = 550;
					
					$new_width = $original_width;
					$new_height = $original_height;
					
					resize_image($path_to_original_image, $thumbnail_filename, $max_thumbnail_width, $max_thumbnail_height, $crop=FALSE);
					
					
				
				}
			}
	}

	
	


}



// Resize image
// Resize image
function resize_image($file, $target, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*($r-$w/$h)));
        } else {
            $height = ceil($height-($height*($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
	
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
	
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	imagejpeg($dst, $target, 100);
  
}


 

?>
