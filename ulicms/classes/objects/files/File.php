<?php

if (!defined("KCFINDER_PAGE")) {

    class File {

        public static function write($file, $data) {
            return file_put_contents($file, $data);
        }

        public static function append($file, $data) {
            return file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
        }

        public static function read($file) {
            return file_get_contents($file);
        }

        public static function delete($file) {
            return unlink($file);
        }

        public static function rename($old, $new) {
            return rename($old, $new);
        }

        public static function lastChanged($file) {
            echo self::getLastChanged($file);
        }

        public static function getLastChanged($file) {
            clearstatcache();
            $retval = filemtime($file);
            clearstatcache();
            return $retval;
        }

        public static function getExtension($filename) {
            $ext = explode(".", $filename);
            $ext = end($ext);
            $ext = strtolower($ext);
            return $ext;
        }

        public static function loadLines($url) {
            $data = file_get_contents_wrapper($url);
            if (!$data) {
                return null;
            }
            $data = str_replace("\r\n", "\n");
            $data = explode("\n", $data);
            return $data;
        }

        public static function loadLinesAndTrim($url) {
            $data = self::loadLines($url);
            if ($data) {
                $data = array_map('trim', $data);
            }
            return $data;
        }

        public static function existsLocally($path) {
            return ( preg_match('~^(\w+:)?//~', $path) === 0 and is_file($path));
        }

        public static function toDataUri($file, $mime = null) {
            $url = null;
            if (is_file($file)) {
                $mime = is_null($mime) ? get_mime($file) : $mime;
                $data = file_get_contents($file);
                $base64_data = base64_encode($data);
                $url = "data:{$mime};base64,{$base64_data}";
            }
            return $url;
        }

        // Mimetypen einer Datei ermitteln
        public static function getMime($file) {
            if (function_exists("finfo_file")) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                $mime = finfo_file($finfo, $file);
                finfo_close($finfo);
                return $mime;
            } else if (function_exists("mime_content_type")) {
                return mime_content_type($file);
            } else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
                // http://stackoverflow.com/a/134930/1593459
                $file = escapeshellarg($file);
                $mime = shell_exec("file -bi " . $file);
                return $mime;
            }
            return false;
        }

    }

}