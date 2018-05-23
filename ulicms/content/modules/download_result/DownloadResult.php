<?php

function DownloadResult($filename, $attachmentFilename = null, $status = 200)
{
    @set_time_limit(0);
    if (! $attachmentFilename) {
        $attachmentFilename = $filename;
    }
    $attachmentFilename = basename($attachmentFilename);
    if (is_readable($filename)) {
        $filesize = filesize($filename);
        @set_time_limit(0); // Kein Zeitlimit
        header($_SERVER["SERVER_PROTOCOL"] . " " . Request::getStatusCodeByNumber($status));
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $attachmentFilename);
        header("Cache-Control: public");
        header("Content-length: " . $filesize); // tells file size
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($filename);
        exit();
    }
}

function DownloadResultFromString($string, $attachmentFilename, $status = 200)
{
    @set_time_limit(0);
    $attachmentFilename = basename($attachmentFilename);
    $filesize = getStringLengthInBytes($string);
    @set_time_limit(0); // Kein Zeitlimit
    header($_SERVER["SERVER_PROTOCOL"] . " " . Request::getStatusCodeByNumber($status));
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $attachmentFilename);
    header("Cache-Control: public");
    header("Content-length: " . $filesize); // tells file size
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $string;
    exit();
}

function DownloadResultFromFile($filename, $attachmentFilename = null, $status = 200)
{
    DownloadResult($filename, $attachmentFilename, $status);
}
	
