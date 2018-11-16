<?php

class CommunitySettingsController extends Controller
{

    public function savePost()
    {
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
        Response::redirect(ModuleHelper::buildActionURL("community_settings", "save=1"));
    }
}