<?php

class BackendHelper
{

    public static function formatDatasetCount($count)
    {
        if ($count == 1) {
            translate("ONE_DATASET_FOUND");
        } else {
            translate("X_DATASETS_FOUND", array(
                "%x" => $count
            ));
        }
    }

    public static function getAction()
    {
        if (isset($_REQUEST["action"])) {
            return $_REQUEST["action"];
        } else {
            return "home";
        }
    }

    public static function setAction($action)
    {
        $_REQUEST["action"] = $action;
        $_GET["action"] = $action;
        if (Request::isPost()) {
            $_POST["action"] = $action;
        }
    }
}