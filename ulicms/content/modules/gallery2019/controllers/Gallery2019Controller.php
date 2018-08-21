<?php
use Gallery2019\Gallery;

class Gallery2019Controller extends Controller
{

    private $moduleName = "gallery2019";

    public function uninstall()
    {
        $migrator = new DBMigrator("module/{$this->moduleName}", ModuleHelper::buildModuleRessourcePath($this->moduleName, "sql/down"));
        $migrator->rollback();
    }

    public function getSettingsLinkText()
    {
        return get_translation("edit");
    }

    public function getSettingsHeadline()
    {
        return get_translation("galleries");
    }

    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "list.php");
    }

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
            Response::redirect(ModuleHelper::buildActionURL("gallery_edit", "id={$id}"));
        } else {
            throw new FileNotFoundException("No gallery with id {$id}");
        }
    }
}