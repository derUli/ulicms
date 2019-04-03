<?php

class ImapLoginController extends Controller {

    private $moduleName = "imap_login";
    private $logger;

    public function beforeInit() {
        $cfg = $this->getConfig();
        $logPath = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/log/imap_login");
        if (isset($cfg["log_enabled"]) and $cfg["log_enabled"]) {
            if (!file_exists($logPath)) {
                mkdir($logPath, null, true);
            }
            $this->logger = new Katzgrau\KLogger\Logger($logPath, Psr\Log\LogLevel::DEBUG, array(
                "extension" => "log"
            ));
        }

        $timeout = DEFAULT_IMAP_LOGIN_TIMEOUT;
        if (isset($cfg["imap_timeout"])) {
            $timeout = intval($cfg["imap_timeout"]);
        }
        imap_timeout(IMAP_OPENTIMEOUT, $timeout);
        imap_timeout(IMAP_READTIMEOUT, $timeout);
        imap_timeout(IMAP_WRITETIMEOUT, $timeout);
        imap_timeout(IMAP_CLOSETIMEOUT, $timeout);
    }

    public function sessionDataFilter($sessionData) {
        // empty passwords are not supported
        if (empty($_POST["user"]) or empty($_POST["password"])) {
            return $sessionData;
        }
        $cfg = $this->getConfig();

        if (isset($cfg)) {
            $this->debug("IMAP Connection String: " . $cfg["imap_mailbox"]);
        } else {
            $this->error("IMAP Connection String not set.");
        }

        $username = $_POST["user"];
        $password = $_POST["password"];
        $email = $_POST["user"];
        $firstname = $cfg["first_name"] ?? DEFAULT_FIRSTNAME;
        $lastname = $cfg["last_name"] ?? DEFAULT_LASTNAME;
        $skip_on_error = (isset($cfg["skip_on_error"]) and $cfg["skip_on_error"]);
        if (!$skip_on_error) {
            $sessionData = false;
        } else {
            $this->debug("skip_on_error is enabled");
        }

        $authenticator = new ImapAuthenticator($cfg);
        $success = $authenticator->authenticate($username, $password);
        if ($success) {
            if (isset($cfg["remove_realm"]) and $cfg["remove_realm"]) {
                $explodedUser = explode("@", $username);
                $username = $explodedUser[0];
                $this->debug("Remove realm from username: {$username}");
            }
            $user = getUserByName($username);
            if ($user) {
                if (isset($cfg["sync_passwords"]) and $cfg["sync_passwords"] and $user["password"] != Encryption::hashPassword($password)) {
                    $this->debug("Password of {$username} changed. Update password in database.");
                    changePassword($password, intval($user["id"]));
                    $user["password"] = Encryption::hashPassword($password);
                }
                $sessionData = $user;
            } else if (isset($cfg["create_user"]) and $cfg["create_user"]) {
                $this->debug("User $username doesn't exists. So create it.");

                $user = new User();
                $user->setUsername($username);
                $user->setLastname($lastname);
                $user->setFirstname($firstname);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setPrimaryGroupId(Settings::get("default_acl_group") ? Settings::get("default_acl_group") : null );
                $user->save();

                $sessionData = getUserByName($username);
            }
        } else {
            // FIXME: Fehlermeldung benutzerfreundlich aufbereiten
            $_REQUEST["error"] = imap_last_error();
            $this->error("IMAP Error: " . imap_last_error() . "");
        }
        return $sessionData;
    }

    public function debug($message, $context = array()) {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }

    public function info($message, $context = array()) {
        if ($this->logger) {
            $this->logger->info($message, $context);
        }
    }

    public function error($message, $context = array()) {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }

    private function getConfig() {
        $cfg = new CMSConfig();
        if (!isset($cfg->imap_login)) {
            return null;
        }
        return $cfg->imap_login;
    }

}
