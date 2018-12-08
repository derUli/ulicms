<?php

class DefaultAccessRestrictionsController extends Controller
{

    public function savePost()
    {
        Settings::set("only_admins_can_edit", Request::getVar("only_admins_can_edit", null, "int"));
        Settings::set("only_group_can_edit", Request::getVar("only_group_can_edit", null, "int"));
        Settings::set("only_owner_can_edit", Request::getVar("only_owner_can_edit", null, "int"));
        Settings::set("only_others_can_edit", Request::getVar("only_others_can_edit", null, "int"));
        Request::redirect(ModuleHelper::buildActionURL("default_access_restrictions", "submit_form=1"));
    }
}