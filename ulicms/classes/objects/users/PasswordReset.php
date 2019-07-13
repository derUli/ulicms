<?php

class PasswordReset {

    public function addToken($user_id) {
        $token = md5(uniqid() . strval($user_id));
        $sql = "INSERT INTO {prefix}password_reset (token, user_id) values (?, ?)";
        $args = array(
            $token,
            intval($user_id)
        );
        Database::pQuery($sql, $args, true);
        return $token;
    }

    public function sendMail($token, $to, $ip, $firstname, $lastname) {
        ViewBag::set("url", $this->getPasswordResetLink($token));
        ViewBag::set("firstname", $firstname);
        ViewBag::set("lastname", $lastname);
        ViewBag::set("ip", $ip);

        $message = Template::executeDefaultOrOwnTemplate("email/password_reset");
        $subject = get_translation("reset_password_subject");
        $from = Settings::get("email");
        $headers = "From: $from\r\n";
        $headers .= "Mime-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=utf-8";
        Mailer::send($to, $subject, $message, $headers);
    }

    public function getPasswordResetLink($token) {
        $url = getBaseFolderURL();
        $url = rtrim($url, "/");
        if (!is_admin_dir()) {
            $url .= "/admin";
        }
        $url .= "/" . ModuleHelper::buildMethodCallUrl(SessionManager::class, "resetPassword", "token=$token");
        return $url;
    }

    public function getAllTokens() {
        $tokens = [];
        $result = Database::selectAll("password_reset");
        if (Database::getNumRows($result) === 0) {
            return $tokens;
        }
        while ($token = Database::fetchObject($result)) {
            $tokens[] = $token;
        }
        return $tokens;
    }

    public function getAllTokensByUserId(int $user_id) {
        $tokens = [];
        $result = Database::selectAll("password_reset", [], "user_id={$user_id}");
        if (Database::getNumRows($result) === 0) {
            return $tokens;
        }
        while ($token = Database::fetchObject($result)) {
            $tokens[] = $token;
        }
        return $tokens;
    }

    public function getTokenByTokenString($token) {
        $sql = "select * from {prefix}password_reset where token = ?";
        $args = array(
            strval($token)
        );
        $query = Database::pQuery($sql, $args, true);
        if (Database::any($query)) {
            return Database::fetchObject($query);
        }
        return null;
    }

    public function deleteToken($token) {
        $sql = "delete from {prefix}password_reset where token = ?";
        $args = array(
            strval($token)
        );
        Database::pQuery($sql, $args, true);
    }

}
