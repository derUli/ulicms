<?php
use Gallery2019\Gallery;

class GalleryController extends Controller
{

    public function createPost()
    {
        $gallery = new Gallery();
        $gallery->setTitle(Request::getVar("title"));
        $gallery->setCreatedBy(get_user_id());
        $gallery->setLastChangedBy(get_user_id());
        $gallery->save();
        $id = $gallery->getID();
        Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$id}"));
    }

    public function editPost()
    {
        $id = Request::getVar("id", 0, "int");
        $title = Request::getVar("title", "", "str");
        $model = new Gallery($id);
        if ($id and $model->getID()) {
            $model->setTitle($title);
            $model->save();
            Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$id}&save=1"));
        } else {
            throw new FileNotFoundException("No gallery with id {$id}");
        }
    }

    public function delete()
    {
        $id = Request::getVar("id", 0, "int");
        if (! $id) {
            throw new Exception("No id set");
        }
        
        $model = new Gallery($id);
        if ($model->getID()) {
            $model->delete();
            Response::redirect(ModuleHelper::buildAdminURL(Gallery2019Controller::MODULE_NAME));
        } else {
            throw new FileNotFoundException("No gallery with id {$id}");
        }
    }
}