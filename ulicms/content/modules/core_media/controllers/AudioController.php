<?php

declare(strict_types=1);

class AudioController extends Controller {

    public function createPost(): void {
        $mp3_file_value = "";
        $audio_folder = ULICMS_DATA_STORAGE_ROOT . "/content/audio";
        // mp3
        if (!empty($_FILES ['mp3_file'] ['name'])) {
            $mp3_file = time() . "-" . basename($_FILES ['mp3_file'] ['name']);
            $mp3_type = $_FILES ['mp3_file'] ["type"];
            $mp3_allowed_mime_type = array(
                "audio/mp3",
                "audio/mpeg3",
                "audio/x-mpeg-3",
                "video/mpeg",
                "video/x-mpeg",
                "audio/mpeg"
            );
            if (faster_in_array($mp3_type, $mp3_allowed_mime_type)) {
                $target = $audio_folder . "/" . $mp3_file;
                if (move_uploaded_file(
                                $_FILES ['mp3_file'] ['tmp_name'],
                                $target
                        )) {
                    // Google Cloud: make file public
                    if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://")
                            and class_exists("GoogleCloudHelper")) {
                        GoogleCloudHelper::changeFileVisiblity($target, true);
                    }
                    $mp3_file_value = basename($mp3_file);
                }
            }
        }

        $ogg_file_value = "";
        // ogg
        if (!empty($_FILES ['ogg_file'] ['name'])) {
            $ogg_file = time() . "-" . $_FILES ['ogg_file'] ['name'];
            $ogg_type = $_FILES ['ogg_file'] ["type"];
            $ogg_allowed_mime_type = array(
                "audio/ogg",
                "application/ogg",
                "video/ogg"
            );
            if (faster_in_array($ogg_type, $ogg_allowed_mime_type)) {
                $target = $audio_folder . "/" . $ogg_file;
                if (move_uploaded_file(
                                $_FILES ['ogg_file'] ['tmp_name'],
                                $target
                        )) {
                    // Google Cloud: make file public
                    if (startsWith(ULICMS_DATA_STORAGE_ROOT, "gs://")
                            and class_exists("GoogleCloudHelper")) {
                        GoogleCloudHelper::changeFileVisiblity($target, true);
                    }
                    $ogg_file_value = basename($ogg_file);
                }
            }
        }

        $name = db_escape($_POST ["name"]);
        $category_id = intval($_POST ["category_id"]);
        $ogg_file_value = db_escape($ogg_file_value);
        $mp3_file_value = db_escape($mp3_file_value);
        $timestamp = time();

        if (!empty($ogg_file_value) || !empty($mp3_file_value)) {
            db_query("INSERT INTO " . tbname("audio") .
                    " (name, ogg_file, mp3_file, created, category_id, "
                    . "`updated`) VALUES ('$name', '$ogg_file_value', "
                    . "'$mp3_file_value', $timestamp, $category_id, "
                    . "$timestamp);");
        }
        Request::redirect(ModuleHelper::buildActionURL("audio"));
    }

    public function _updatePost(): bool {
        $name = db_escape($_POST ["name"]);
        $id = intval($_POST ["id"]);
        $ogg_file = db_escape(basename($_POST ["ogg_file"]));
        $mp3_file = db_escape(basename($_POST ["mp3_file"]));
        $updated = time();
        $category_id = intval($_POST ["category_id"]);
        db_query("UPDATE " . tbname("audio") . " SET name='$name', "
                . "ogg_file='$ogg_file', mp3_file='$mp3_file', "
                . "category_id = $category_id, `updated` = $updated "
                . "where id = $id");
        
        return Database::getAffectedRows() > 0;
    }

    public function updatePost(): void {

        $this->_updatePost();
        Request::redirect(ModuleHelper::buildActionURL("audio"));
    }

    public function deletePost(): void {
        $result = db_query("select ogg_file, mp3_file from " .
                tbname("audio") . " where id = " .
                intval($_REQUEST ["delete"]));
        if (db_num_rows($result) > 0) {
            $dataset = db_fetch_object($result);
            $filepath = ULICMS_DATA_STORAGE_ROOT . "/content/audio/" .
                    basename($dataset->ogg_file);
            if (!empty($dataset->ogg_file) and file_exists($filepath)) {
                @unlink($filepath);
            }

            $filepath = ULICMS_DATA_STORAGE_ROOT . "/content/audio/" .
                    basename($dataset->mp3_file);
            if (!empty($dataset->mp3_file) and file_exists($filepath)) {
                @unlink($filepath);
            }

            db_query("DELETE FROM " . tbname("audio") . " where id = " .
                    $_REQUEST ["delete"]);
        }
        Request::redirect(ModuleHelper::buildActionURL("videos"));
    }

}
