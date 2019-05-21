<?php

class InstagramController extends MainClass {

    public function cron() {
        $username = Settings::get("instagram/username");
        $password = Settings::get("instagram/password");
        if (!is_present($username) or ! is_present($password)) {
            return;
        }
        $this->postUnpostedImages($username, $password);
    }

    protected function postUnpostedImages($username, $password) {
        ignore_user_abort(true);
        $images = $this->getNotPosted();
        if (!count($images)) {
            return;
        }

        \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $instaCrap = new \InstagramAPI\Instagram();
        $instaCrap->login($username, $password);

        foreach ($images as $image) {
            $image->postImage($instaCrap);
        }
        $instaCrap->logout();

        ignore_user_abort(false);
    }

    public function getNotPosted($order = "id") {
        $result = [];
        $sql = "SELECT id, `type` FROM " . tbname("content") . " where `type` = 'image' and posted2instagram = 0 ORDER BY $order";

        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = new InstagramImage($row->id);
        }
        return $result;
    }

}
