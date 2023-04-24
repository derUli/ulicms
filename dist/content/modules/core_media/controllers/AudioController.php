<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class AudioController extends \App\Controllers\Controller {
    public function createPost(): void {
        $mp3_file_value = '';
        $audio_folder = ULICMS_ROOT . '/content/audio';
        // mp3
        if (! empty($_FILES ['mp3_file'] ['name'])) {
            $mp3_file = time() . '-' . basename($_FILES ['mp3_file'] ['name']);
            $mp3_type = $_FILES ['mp3_file'] ['type'];
            $mp3_allowed_mime_type = [
                'audio/mp3',
                'audio/mpeg3',
                'audio/x-mpeg-3',
                'video/mpeg',
                'video/x-mpeg',
                'audio/mpeg'
            ];
            if (in_array($mp3_type, $mp3_allowed_mime_type)) {
                $target = $audio_folder . '/' . $mp3_file;
                if (move_uploaded_file(
                    $_FILES ['mp3_file'] ['tmp_name'],
                    $target
                )) {
                    $mp3_file_value = basename($mp3_file);
                }
            }
        }

        $ogg_file_value = '';

        // ogg
        if (! empty($_FILES ['ogg_file'] ['name'])) {
            $ogg_file = time() . '-' . $_FILES ['ogg_file'] ['name'];
            $ogg_type = $_FILES ['ogg_file'] ['type'];
            $ogg_allowed_mime_type = [
                'audio/ogg',
                'application/ogg',
                'video/ogg'
            ];

            if (in_array($ogg_type, $ogg_allowed_mime_type)) {
                $target = $audio_folder . '/' . $ogg_file;
                if (move_uploaded_file(
                    $_FILES ['ogg_file'] ['tmp_name'],
                    $target
                )) {
                    $ogg_file_value = basename($ogg_file);
                }
            }
        }

        $name = Database::escapeValue($_POST['name']);
        $category_id = (int)$_POST['category_id'];
        $ogg_file_value = Database::escapeValue($ogg_file_value);
        $mp3_file_value = Database::escapeValue($mp3_file_value);
        $timestamp = time();

        if (! empty($ogg_file_value) || ! empty($mp3_file_value)) {
            Database::query('INSERT INTO ' . Database::tableName('audio') .
                    ' (name, ogg_file, mp3_file, created, category_id, '
                    . "`updated`) VALUES ('{$name}', '{$ogg_file_value}', "
                    . "'{$mp3_file_value}', {$timestamp}, {$category_id}, "
                    . "{$timestamp});");
        }
        Response::redirect(ModuleHelper::buildActionURL('audio'));
    }

    public function _updatePost(): bool {
        $name = Database::escapeValue($_POST['name']);
        $id = (int)$_POST['id'];
        $ogg_file = Database::escapeValue(basename($_POST['ogg_file']));
        $mp3_file = Database::escapeValue(basename($_POST['mp3_file']));
        $updated = time();
        $category_id = (int)$_POST['category_id'];
        Database::query('UPDATE ' . Database::tableName('audio') . " SET name='{$name}', "
                . "ogg_file='{$ogg_file}', mp3_file='{$mp3_file}', "
                . "category_id = {$category_id}, `updated` = {$updated} "
                . "where id = {$id}");

        return Database::getAffectedRows() > 0;
    }

    public function updatePost(): void {
        $this->_updatePost();
        Response::redirect(ModuleHelper::buildActionURL('audio'));
    }

    public function deletePost(): void {
        $result = Database::query('select ogg_file, mp3_file from ' .
                Database::tableName('audio') . ' where id = ' .
                (int)$_REQUEST['delete']);
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $filepath = ULICMS_ROOT . '/content/audio/' .
                    basename($dataset->ogg_file);
            if (! empty($dataset->ogg_file) && is_file($filepath)) {
                @unlink($filepath);
            }

            $filepath = ULICMS_ROOT . '/content/audio/' .
                    basename($dataset->mp3_file);
            if (! empty($dataset->mp3_file) && is_file($filepath)) {
                @unlink($filepath);
            }

            Database::query('DELETE FROM ' . Database::tableName('audio') . ' where id = ' .
                    $_REQUEST['delete']);
        }
        Response::redirect(ModuleHelper::buildActionURL('videos'));
    }
}
