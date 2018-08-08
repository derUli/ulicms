<?php

class ViewLogController extends Controller
{

    private $moduleName = "view_log";

    public function settings()
    {
        $dir = Request::getVar("dir");
        $file = Request::getVar("file");
        // if the user selects a file show it's content
        if ($dir and $file) {
            $dir = basename($dir);
            $file = basename($file);
            $path = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/log/{$dir}/{$file}");
            if (is_file($path) and in_array(file_extension($file), array(
                "txt",
                "log"
            ))) {
                ViewBag::set("file", $path);
                return Template::executeModuleTemplate($this->moduleName, "view.php");
            } else {
                ExceptionResult("Can not open log file $path");
            }
        }
        // show list of log files which have .txt or .log extensions and are not empty
        return Template::executeModuleTemplate($this->moduleName, "list.php");
    }

    public function getSettingsHeadline()
    {
        return "Log Viewer";
    }

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }

    // get all log files and the containing folders
    public function getLogs()
    {
        $logs = array();
        $logs = array();
        $dirs = glob(Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/log/*"), GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $files = glob($dir . "/*.{log,txt}", GLOB_BRACE);
            $files = array_filter($files, function ($file) {
                return filesize($file) > 0;
            });
            $files = array_map("basename", $files);
            if (count($files) > 0) {
                $logs[basename($dir)] = $files;
            }
        }
        ksort($files);
        return $logs;
    }
}