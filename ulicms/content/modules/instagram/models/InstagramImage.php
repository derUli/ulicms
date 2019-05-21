<?php

class InstagramImage extends Image_Page {

    public function getImagePath() {
        return Path::resolve("ULICMS_ROOT" . urldecode($this->image_url));
    }

    public function postImage($instaCrap) {

        $resizer = new \InstagramAPI\Media\Photo\InstagramPhoto($this->getImagePath());

        $cacheDir = Path::resolve("ULICMS_CACHE/instagram");
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $pathInfo = pathinfo($this->getImagePath());
        $cacheFile = md5_file($this->getImagePath()) . "." . $pathInfo["extension"];
        var_dump($cacheFile);

        $cacheFullPath = "$cacheDir/$cacheFile";
        var_dump($cacheFullPath);
        copy($resizer->getFile(), $cacheFullPath);


        $instaCrap->timeline->uploadPhoto(
                $cacheFullPath, $this->getMetadata()
        );
        Database::pQuery("update {prefix}content set posted2instagram = 1 where id = ?", array(
            $this->id
                ), true);
    }

    public function getMetadata() {
        return [
            "caption" => $this->meta_description
        ];
    }

}
