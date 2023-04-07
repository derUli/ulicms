<?php

declare(strict_types=1);

class VideoController extends Controller
{
    public function createPost(): void
    {
        $video_folder = ULICMS_ROOT . "/content/videos";

        if (isset($_FILES)) {
            $mp4_file_value = '';
            // MP4
            if (!empty($_FILES ['mp4_file'] ['name'])) {
                $mp4_file = time() . "-" .
                        basename($_FILES ['mp4_file'] ['name']);
                $mp4_type = $_FILES ['mp4_file'] ["type"];
                $mp4_allowed_mime_type = array(
                    "video/mp4"
                );
                if (in_array($mp4_type, $mp4_allowed_mime_type)) {
                    $target = $video_folder . '/' . $mp4_file;
                    if (move_uploaded_file(
                        $_FILES ['mp4_file'] ['tmp_name'],
                        $target
                    )) {
                        $mp4_file_value = basename($mp4_file);
                    }
                }
            }

            $ogg_file_value = '';
            // ogg
            if (!empty($_FILES ['ogg_file'] ['name'])) {
                $ogg_file = time() . "-" . $_FILES ['ogg_file'] ['name'];
                $ogg_type = $_FILES ['ogg_file'] ["type"];
                $ogg_allowed_mime_type = array(
                    "video/ogg",
                    "application/ogg",
                    "audio/ogg"
                );
                if (in_array($ogg_type, $ogg_allowed_mime_type)) {
                    $target = $video_folder . '/' . $ogg_file;
                    if (move_uploaded_file(
                        $_FILES ['ogg_file'] ['tmp_name'],
                        $target
                    )) {
                        $ogg_file_value = basename($ogg_file);
                    }
                }
            }

            // WebM
            $webm_file_value = '';
            // webm
            if (!empty($_FILES ['webm_file'] ['name'])) {
                $webm_file = time() . "-" . $_FILES ['webm_file'] ['name'];
                $webm_type = $_FILES ['webm_file'] ["type"];
                $webm_allowed_mime_type = array(
                    "video/webm",
                    "audio/webm",
                    "application/webm"
                );
                if (in_array($webm_type, $webm_allowed_mime_type)) {
                    $target = $video_folder . '/' . $webm_file;
                    if (move_uploaded_file(
                        $_FILES ['webm_file'] ['tmp_name'],
                        $target
                    )) {
                        $webm_file_value = basename($webm_file);
                    }
                }
            }

            $name = db_escape($_POST['name']);
            $category_id = intval($_POST["category_id"]);
            $ogg_file_value = db_escape($ogg_file_value);
            $webm_file_value = db_escape($webm_file_value);
            $mp4_file_value = db_escape($mp4_file_value);

            $width = intval($_POST["width"]);
            $height = intval($_POST["height"]);
            $timestamp = time();

            if (!empty($ogg_file_value) || !empty($mp4_file_value) || !empty($webm_file_value)) {
                db_query("INSERT INTO " . tbname("videos") .
                        " (name, ogg_file, webm_file, mp4_file, width, "
                        . "height, created, category_id, `updated`) "
                        . "VALUES ('$name', '$ogg_file_value', "
                        . "'$webm_file_value',  '$mp4_file_value', "
                        . "$width, $height, $timestamp, $category_id, "
                        . "$timestamp);");
            }
        }
        Response::redirect(ModuleHelper::buildActionURL("videos"));
    }

    public function _updatePost(): bool
    {
        $name = db_escape($_POST['name']);
        $id = intval($_POST['id']);
        $ogg_file = db_escape(basename($_POST["ogg_file"]));
        $webm_file = db_escape(basename($_POST["webm_file"]));
        $mp4_file = db_escape(basename($_POST["mp4_file"]));
        $width = intval($_POST["width"]);
        $height = intval($_POST["height"]);
        $updated = time();
        $category_id = intval($_POST["category_id"]);
        db_query("UPDATE " . tbname("videos") . " SET name='$name', "
                . "ogg_file='$ogg_file', mp4_file='$mp4_file', "
                . "webm_file='$webm_file', width=$width, height=$height, "
                . "category_id = $category_id, `updated` = $updated "
                . "where id = $id");
        return Database::getAffectedRows() > 0;
    }

    public function updatePost(): void
    {
        $this->_updatePost();
        Response::redirect(ModuleHelper::buildActionURL("videos"));
    }

    public function deletePost(): void
    {
        $result = db_query("select ogg_file, webm_file, mp4_file from " .
                tbname("videos") . " where id = " . intval($_REQUEST["delete"]));
        if (db_num_rows($result) > 0) {
            // OGG
            $dataset = db_fetch_object($result);
            $filepath = ULICMS_ROOT . "/content/videos/" .
                    basename($dataset->ogg_file);
            if (!empty($dataset->ogg_file) && is_file($filepath)) {
                unlink($filepath);
            }

            // WebM
            $filepath = ULICMS_ROOT . "/content/videos/" .
                    basename($dataset->webm_file);
            if (!empty($dataset->webm_file) && is_file($filepath)) {
                unlink($filepath);
            }

            // MP4
            $filepath = ULICMS_ROOT . "/content/videos/" .
                    basename($dataset->mp4_file);
            if (!empty($dataset->mp4_file) && is_file($filepath)) {
                @unlink($filepath);
            }

            db_query("DELETE FROM " . tbname("videos") . " where id = " .
                    intval($_REQUEST["delete"]));
        }
        Response::redirect(ModuleHelper::buildActionURL("videos"));
    }
}
