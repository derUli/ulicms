<?php

declare(strict_types=1);

namespace App\Models\Users;

use App\Utils\Mailer;
use Database;
use ViewBag;
use Template;
use ModuleHelper;
use Settings;
use SessionManager;

/**
 * Reset admin user password
 */
class PasswordReset
{
    /**
     * Insert token to database
     * @param int $user_id
     * @return string
     */
    public function addToken(int $user_id): string
    {
        $token = md5(uniqid() . strval($user_id));
        $sql = 'INSERT INTO {prefix}password_reset (token, user_id) '.
                'values (?, ?)';

        $args = [
            $token,
            intval($user_id)
        ];

        Database::pQuery($sql, $args, true);
        return $token;
    }

    /**
     * Send password reset email
     * @param string $token
     * @param string $to
     * @param string $ip
     * @param string $firstname
     * @param string $lastname
     * @return void
     */
    public function sendMail(
        string $token,
        string $to,
        string $ip,
        string $firstname,
        string $lastname
    ): void {
        ViewBag::set('url', $this->getPasswordResetLink($token));
        ViewBag::set('firstname', $firstname);
        ViewBag::set('lastname', $lastname);
        ViewBag::set('ip', $ip);

        $message = Template::executeDefaultOrOwnTemplate('email/password_reset');
        $subject = get_translation('reset_password_subject');
        $from = Settings::get('email');
        $headers = "From: $from\r\n";
        $headers .= "Mime-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=utf-8";
        Mailer::send($to, $subject, $message, $headers);
    }

    /**
     * Generates password reset link for token
     * @param string $token
     * @return string
     */
    public function getPasswordResetLink(string $token): string
    {
        $url = getBaseFolderURL();
        $url = rtrim($url, '/');

        if (!is_admin_dir()) {
            $url .= '/admin';
        }

        $url .= '/' . ModuleHelper::buildMethodCallUrl(
            SessionManager::class,
            'resetPassword',
            "token=$token"
        );
        return $url;
    }

    /**
     * Get all tokens
     * @return array
     */
    public function getAllTokens(): array
    {
        $tokens = [];
        $result = Database::selectAll('password_reset');

        while ($token = Database::fetchObject($result)) {
            $tokens[] = $token;
        }

        return $tokens;
    }

    /**
     * Get all tokens by user Id
     * @param int $user_id
     * @return array
     */
    public function getAllTokensByUserId(int $user_id): array
    {
        $tokens = [];

        $result = Database::selectAll(
            'password_reset',
            [],
            "user_id={$user_id}"
        );

        while ($token = Database::fetchObject($result)) {
            $tokens[] = $token;
        }
        return $tokens;
    }

    /**
     * Get token by token string
     * @param string $token
     * @return object|null
     */
    public function getTokenByTokenString(string $token): ?object
    {
        $sql = 'select * from {prefix}password_reset where token = ?';

        $args = [
            $token
        ];

        $result = Database::pQuery($sql, $args, true);

        if (Database::any($result)) {
            return Database::fetchObject($result);
        }

        return null;
    }

    /**
     * Delete token
     * @param string $token
     * @return void
     */
    public function deleteToken(string $token): void
    {
        $sql = 'delete from {prefix}password_reset where token = ?';
        $args = [
            strval($token)
        ];

        Database::pQuery($sql, $args, true);
    }
}
