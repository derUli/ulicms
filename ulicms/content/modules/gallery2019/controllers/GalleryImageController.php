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
        $order = Request::getVar("position", 0, "int");
        
        if (! $gallery_id or StringHelper::isNullOrWhitespace($path)) {
            ExceptionResult(get_translation("fill_all_fields"));
        }
        $gallery = new Gallery($gallery_id);
        if (! $gallery->getID()) {
            ExceptionResult("No gallery with id {$gallery_id}");
        }
        
        $image = new Image();
        $image->setPath($path);
        $image->setDescription($description);
        $image->setOrder($order);
        
        $gallery->addImage($image);
        
        Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$gallery_id}"));
    }

    public function editPost()
    {
        $gallery_id = Request::getVar("gallery_id", null, "int");
        $id = Request::getVar("id", null, "int");
        
        $image = new Image($id);
        if (! $id && ! $image->getID()) {
            ExceptionResult("No image with id {$id}");
        }
        $path = Request::getVar("path");
        $description = Request::getVar("description", "", "str");
        $order = Request::getVar("position", 0, "int");
        
        if (! $gallery_id or StringHelper::isNullOrWhitespace($path)) {
            ExceptionResult(get_translation("fill_all_fields"));
        }
        
        $image->setPath($path);
        $image->setDescription($description);
        $image->setOrder($order);
        $image->save();
        
        Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$gallery_id}"));
    }

    public function delete()
    {
        $id = Request::getVar("id", 0, "int");
        if (! $id) {
            throw new Exception("No id set");
        }
        
        $model = new Image($id);
        $gallery_id = $model->getGalleryId();
        if ($model->getID()) {
            $model->delete();
            Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$gallery_id}"));
        } else {
            throw new FileNotFoundException("No gallery with id {$id}");
        }
    }
}