<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class VideoController extends \App\Controllers\Controller {
    public function createPost(): void {
        $video_folder = ULICMS_ROOT . '/content/videos';

            $mp4_file_value = '';
            // MP4
            if (! empty($_FILES ['mp4_file'] ['name'])) {
                $mp4_file = time() . '-' .
                        basename($_FILES ['mp4_file'] ['name']);
                $mp4_type = $_FILES ['mp4_file'] ['type'];
                $mp4_allowed_mime_type = [
                    'video/mp4'
                ];
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
            if (! empty($_FILES ['ogg_file'] ['name'])) {
                $ogg_file = time() . '-' . $_FILES ['ogg_file'] ['name'];
                $ogg_type = $_FILES ['ogg_file'] ['type'];
                $ogg_allowed_mime_type = [
                    'video/ogg',
                    'application/ogg',
                    'audio/ogg'
                ];
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
            if (! empty($_FILES ['webm_file'] ['name'])) {
                $webm_file = time() . '-' . $_FILES ['webm_file'] ['name'];
                $webm_type = $_FILES ['webm_file'] ['type'];
                $webm_allowed_mime_type = [
                    'video/webm',
                    'audio/webm',
                    'application/webm'
                ];
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

            $name = Database::escapeValue($_POST['name']);
            $category_id = (int)$_POST['category_id'];
            $ogg_file_value = Database::escapeValue($ogg_file_value);
            $webm_file_value = Database::escapeValue($webm_file_value);
            $mp4_file_value = Database::escapeValue($mp4_file_value);

            $width = (int)$_POST['width'];
            $height = (int)$_POST['height'];
            $timestamp = time();

            if (! empty($ogg_file_value) || ! empty($mp4_file_value) || ! empty($webm_file_value)) {
                Database::query('INSERT INTO ' . Database::tableName('videos') .
                        ' (name, ogg_file, webm_file, mp4_file, width, '
                        . 'height, created, category_id, `updated`) '
                        . "VALUES ('{$name}', '{$ogg_file_value}', "
                        . "'{$webm_file_value}',  '{$mp4_file_value}', "
                        . "{$width}, {$height}, {$timestamp}, {$category_id}, "
                        . "{$timestamp});");
            }

        Response::redirect(ModuleHelper::buildActionURL('videos'));
    }

    public function _updatePost(): bool {
        $name = Database::escapeValue($_POST['name']);
        $id = (int)$_POST['id'];
        $ogg_file = Database::escapeValue(basename($_POST['ogg_file']));
        $webm_file = Database::escapeValue(basename($_POST['webm_file']));
        $mp4_file = Database::escapeValue(basename($_POST['mp4_file']));
        $width = (int)$_POST['width'];
        $height = (int)$_POST['height'];
        $updated = time();
        $category_id = (int)$_POST['category_id'];
        Database::query('UPDATE ' . Database::tableName('videos') . " SET name='{$name}', "
                . "ogg_file='{$ogg_file}', mp4_file='{$mp4_file}', "
                . "webm_file='{$webm_file}', width={$width}, height={$height}, "
                . "category_id = {$category_id}, `updated` = {$updated} "
                . "where id = {$id}");
        return Database::getAffectedRows() > 0;
    }

    public function updatePost(): void {
        $this->_updatePost();
        Response::redirect(ModuleHelper::buildActionURL('videos'));
    }

    public function deletePost(): void {
        $result = Database::query('select ogg_file, webm_file, mp4_file from ' .
                Database::tableName('videos') . ' where id = ' . (int)$_REQUEST['delete']);
        if (Database::getNumRows($result) > 0) {
            // OGG
            $dataset = Database::fetchObject($result);
            $filepath = ULICMS_ROOT . '/content/videos/' .
                    basename($dataset->ogg_file);
            if (! empty($dataset->ogg_file) && is_file($filepath)) {
                unlink($filepath);
            }

            // WebM
            $filepath = ULICMS_ROOT . '/content/videos/' .
                    basename($dataset->webm_file);
            if (! empty($dataset->webm_file) && is_file($filepath)) {
                unlink($filepath);
            }

            // MP4
            $filepath = ULICMS_ROOT . '/content/videos/' .
                    basename($dataset->mp4_file);
            if (! empty($dataset->mp4_file) && is_file($filepath)) {
                @unlink($filepath);
            }

            Database::query('DELETE FROM ' . Database::tableName('videos') . ' where id = ' .
                (int)$_REQUEST['delete']);
        }
        Response::redirect(ModuleHelper::buildActionURL('videos'));
    }
}
