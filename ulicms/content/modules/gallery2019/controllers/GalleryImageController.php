<?php
use Gallery2019\Gallery;
use Gallery2019\Image;

class GalleryImageController extends Controller
{

    public function createPost()
    {
        $gallery_id = Request::getVar("gallery_id", null, "int");
        $path = Request::getVar("path");
        $description = Request::getVar("description", "", "str");
        $order = Request::getVar("order", 0, "int");
        
        if (! $gallery_id or StringHelper::isNullOrWhitespace($path)) {
            ExceptionResult(get_translation("fill_all_fields"));
        }
        $gallery = new Gallery($gallery_id);
        if (! $gallery->getID()) {
            ExceptionResult("no gallery with id {$gallery_id}");
        }
        
        $image = new Image();
        $image->setPath($path);
        $image->setDescription($description);
        $image->setOrder($order);
        
        $gallery->addImage($image);
        
        Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$gallery_id}"));
    }
}