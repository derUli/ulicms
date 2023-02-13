<?php

declare(strict_types=1);

class PasswordReset
{
    // create a password reset token
    public function addToken(int $user_id): string
    {
        $token = md5(uniqid() . strval($user_id));
        $sql = "INSERT INTO {prefix}password_reset (token, user_id) "
                . "values (?, ?)";
        $args = array(
            $token,
            intval($user_id)
        );
        Database::pQuery($sql, $args, true);
        return $token;
    }

    // send a password reset mail
    public function sendMail(
        string $token,
        string $to,
        string $ip,
        string $firstname,
        string $lastname
    ): void {
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

    // get a password reset link
    public function getPasswordResetLink(string $token): string
    {
        $url = getBaseFolderURL();
        $url = rtrim($url, '/');
        if (!is_admin_dir()) {
            $url .= "/admin";
        }
        $url .= '/' . ModuleHelper::buildMethodCallUrl(
            SessionManager::class,
            "resetPassword",
            "token=$token"
        );
        return $url;
    }

    // get all tokens
    public function getAllTokens(): array
    {
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

    // get all tokens for a user
    public function getAllTokensByUserId(int $user_id): array
    {
        $tokens = [];
        $result = Database::selectAll(
            "password_reset",
            [],
            "user_id={$user_id}"
        );
        if (Database::getNumRows($result) === 0) {
            return $tokens;
        }
        while ($token = Database::fetchObject($result)) {
            $tokens[] = $token;
        }
        return $tokens;
    }

    public function getTokenByTokenString(string $token): ?object
    {
        $sql = "select * from {prefix}password_reset where token = ?";

        $args = [
            $token
        ];

        $result = Database::pQuery($sql, $args, true);

        if (Database::any($result)) {
            return Database::fetchObject($result);
        }
        return null;
    }

    public function deleteToken(string $token): void
    {
        $sql = "delete from {prefix}password_reset where token = ?";
        $args = array(
            strval($token)
        );
        Database::pQuery($sql, $args, true);
    }
}
