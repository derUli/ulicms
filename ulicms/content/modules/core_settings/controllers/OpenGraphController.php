<?php

class OpenGraphController extends Controller
{

    public function savePost()
    {
        if (isset($_POST["og_type"])) {
            Settings::set("og_type", $_POST["og_type"]);
        }
        
        if (isset($_POST["og_image"])) {
            Settings::set("og_image", $_POST["og_image"]);
        }
        Request::redirect(ModuleHelper::buildActionURL("open_graph"));
    }
}