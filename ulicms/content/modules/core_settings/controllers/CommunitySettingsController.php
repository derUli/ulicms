<?php

class CommunitySettingsController extends Controller {

    public function savePost() {
        if (Request::getVar("comments_enabled")) {
            Settings::set("comments_enabled", "1");
        } else {
            Settings::delete("comments_enabled");
        }
        if (Request::getVar("comments_must_be_approved")) {
            Settings::set("comments_must_be_approved", "1");
        } else {
            Settings::delete("comments_must_be_approved");
        }
        if (isset($_POST["commentable_content_types"]) and is_array($_POST["commentable_content_types"])) {
            Settings::set("commentable_content_types", implode(";", $_POST["commentable_content_types"]));
        } else {
            Settings::delete("commentable_content_types");
        }
        Response::redirect(ModuleHelper::buildActionURL("community_settings", "save=1"));
    }

}
