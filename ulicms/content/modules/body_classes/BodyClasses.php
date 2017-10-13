<?php

class BodyClasses extends Controller
{

    public function bodyClassesFilter($str)
    {
        $customData = CustomData::get();
        if ($customData and isset($customData["body_classes"])) {
            if (is_string($customData["body_classes"])) {
                $str .= " " . _esc($customData["body_classes"]);
            } else if (is_array($customData["body_classes"])) {
                $str .= " " . _esc(implode(" ", $customData["body_classes"]));
            }
        }
        
        $str = trim($str);
        return $str;
    }

    public function beforeBackendHeader()
    {
        CustomData::setDefault("body_classes", "");
    }
}