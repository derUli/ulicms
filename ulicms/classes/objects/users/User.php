<?php

declare(strict_types=1);

use UliCMS\Exceptions\NotImplementedException;
use UliCMS\Security\PermissionChecker;
use UliCMS\Security\Encryption;

class User extends Model {

    protected $id = null;
    private $username = null;
    private $lastname = "";
    private $firstname = "";
    private $email = "";
    private $password = "";
    private $about_me = "";
    private $group_id = null;
    private $secondary_groups = [];
    private $group = null;
    private $html_editor = "ckeditor";
    private $require_password_change = false;
    private $admin = false;
    private $password_changed = null;
    private $locked = false;
    private $last_login = null;
    private $homepage = "";
    private $default_language = null;

    public function __construct($id = null) {
        if ($id) {
            $this->loadById($id);
        }
    }

    public function loadById($id) {
        $sql = "select * from {prefix}users where id = ?";
        $args = array(
            intval($id)
        );
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public static function fromSessionData(): ?User {
        return get_user_id() ? new self(get_user_id()) : null;
    }

    public function toSessionData(): ?array {
        return $this->isPersistent() ? [
            "ulicms_login" => $this->getUsername(),
            "lastname" => $this->getLastname(),
            "firstname" => $this->getFirstname(),
            "email" => $this->getEmail(),
            "login_id" => $this->getId(),
            "require_password_change" => $this->getRequirePasswordChange(),
            "group_id" => $this->getPrimaryGroupId(),
            "logged_in" => true,
            "session_begin" => time()
                ] : null;
    }

    public function registerSession(bool $redirect = true): void {
        $sessionData = $this->toSessionData();

        if (!is_array($sessionData)) {
            throw new BadMethodCallException();
        }
        if (!session_id()) {
            @session_start();
        }

        foreach ($sessionData as $key => $value) {
            $_SESSION[$key] = $value;
        }

        $this->setLastLogin(time());
        $this->save();

        if (!$redirect || isCLI()) {
            return;
        }
        $login_url = apply_filter("index.php", "login_url");
        if (isset($_REQUEST["go"])) {
            Response::safeRedirect($_REQUEST["go"]);
        } else {
            $login_url = apply_filter("index.php", "login_url");
            Response::redirect($login_url);
        }
    }

    public function loadByUsername(string $name): void {
        $sql = "select * from {prefix}users where username "
                . "COLLATE utf8mb4_general_ci = ?";
        $args = array(
            strval($name)
        );
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function loadByEmail(string $email): void {
        $sql = "select * from {prefix}users where email "
                . "COLLATE utf8mb4_general_ci = ?";
        $args = array(
            strval($email)
        );
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function save() {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
        $this->saveGroups();
    }

    // save a new user and send a welcome mail
    public function saveAndSendMail(string $password): void {
        $this->save();
        $this->sendWelcomeMail($password);
    }

    // Sent welcome mail to new user
    public function sendWelcomeMail(string $password): void {
        $subject = get_translation("new_user_account_at_site",
                array("%domain%" => get_domain()));
        $mailBody = $this->getWelcomeMailText($password);
        $headers = "From: " . Settings::get("email");

        Mailer::send($this->getEmail(), $subject, $mailBody, $headers);
    }

    // get text for welcome mail
    public function getWelcomeMailText(string $password): string {
        ViewBag::set("user", $this);
        ViewBag::set("url", ModuleHelper::getBaseUrl());
        ViewBag::set("password", $password);
        return Template::executeDefaultOrOwnTemplate("email/user_welcome.php");
    }

    public function fillVars($result = null) {
        if (Database::any($result)) {
            $result = Database::fetchAssoc($result);
            foreach ($result as $key => $value) {
                if (isset($this->$key) || property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            if (!is_null($this->group_id)) {
                $this->group = new Group($this->group_id);
            } else {
                $this->group = null;
            }
            // load secondary groups
            $this->loadGroups($result["id"]);
            $this->setId(intval($result["id"]));
            return;
        }
        $this->setSecondaryGroups([]);
    }

    protected function insert() {
        $sql = "insert into {prefix}users (username, lastname, firstname,
                email, password, about_me, group_id, html_editor,
                require_password_change, admin,
                password_changed, locked, last_login,
                homepage, default_language) values
                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $args = array(
            $this->username,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->password,
            $this->about_me,
            $this->group_id,
            $this->html_editor,
            $this->require_password_change,
            $this->admin,
            $this->password_changed,
            $this->locked,
            $this->last_login,
            $this->homepage,
            $this->default_language
        );
        $result = Database::pQuery($sql, $args, true) or die(Database::getError());
        $this->id = Database::getLastInsertID();
    }

    protected function update() {
        $sql = "update {prefix}users set username = ?, lastname = ?,
            firstname = ?, email = ?, password = ?, about_me = ?,
            group_id = ?, html_editor = ?,
            require_password_change = ?, admin = ?, password_changed = ?,
            locked = ?, last_login = ?,
            homepage = ?, default_language = ? where id = ?";
        $args = array(
            $this->username,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->password,
            $this->about_me,
            $this->group_id,
            $this->html_editor,
            $this->require_password_change,
            $this->admin,
            $this->password_changed,
            $this->locked,
            $this->last_login,
            $this->homepage,
            $this->default_language,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId($id) {
        $this->id = !is_null($id) ? intval($id) : null;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = !is_null($username) ? strval($username) : null;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void {
        $this->lastname = !is_null($lastname) ? strval($lastname) : null;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void {
        $this->firstname = !is_null($firstname) ? strval($firstname) : null;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = !is_null($email) ? strval($email) : null;
    }

    public function delete() {
        if (is_null($this->id)) {
            return false;
        }
        $sql = "delete from {prefix}users where id = ?";
        $args = array(
            $this->id
        );
        $result = Database::pQuery($sql, $args, true);
        if ($result) {
            $this->id = null;
        }
        return $result;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): void {
        $this->password = Encryption::hashPassword($password);
        $this->password_changed = date("Y-m-d H:i:s");
    }

    // The password is encrypted
    public function getPasswordChanged(): ?int {
        return $this->password_changed;
    }

    // reset password for this user
    public function resetPassword(): void {
        $passwordReset = new PasswordReset();
        $token = $passwordReset->addToken($this->getId());
        $passwordReset->sendMail(
                $token,
                $this->getEmail(),
                "xxx.xxx.xxx.xxx",
                $this->getFirstname(),
                $this->getLastname()
        );
    }

    public function checkPassword(string $password): bool {
        return Encryption::hashPassword($password) == $this->getPassword();
    }

    public function getAboutMe(): ?string {
        return $this->about_me;
    }

    public function setAboutMe(?string $text): void {
        $this->about_me = !is_null($text) ? strval($text) : null;
    }

    public function getLastAction(): int {
        $lastAction = 0;
        if (!is_null($this->id)) {

            $sql = "select last_action from {prefix}users where id = ?";
            $args = array(
                $this->id
            );
            $result = Database::pQuery($sql, $args, true);
            if (Database::any($result)) {
                $data = Database::fetchObject($result);
                $lastAction = $data->last_action;
            }
        }
        return $lastAction;
    }

    public function setLastAction(?int $time): void {
        if (is_null($this->id)) {
            return;
        }
        $time = intval($time);
        $sql = "update {prefix}users set last_action = ? where id = ?";
        $args = array(
            $time,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function getGroupId() {
        return $this->getPrimaryGroupId();
    }

    public function getPrimaryGroupId() {
        return $this->group_id;
    }

    public function setPrimaryGroupId($gid) {
        $this->group_id = !is_null($gid) ? $gid : null;
        $this->group = !is_null($gid) ? new Group($gid) : null;
    }

    public function setGroupId($gid) {
        $this->setPrimaryGroupId($gid);
    }

    public function getPrimaryGroup() {
        return $this->group;
    }

    public function getGroup() {
        return $this->getPrimaryGroup();
    }

    public function setPrimaryGroup($group) {
        $this->group = $group;
        $this->group_id = !is_null($group) ? $group->getId() : null;
    }

    public function setGroup($group) {
        $this->setPrimaryGroup($group);
    }

    public function getHTMLEditor(): ?string {
        return $this->html_editor;
    }

    public function setHTMLEditor(string $editor): void {
        $allowedEditors = array(
            "ckeditor",
            "codemirror"
        );
        if (!faster_in_array($editor, $allowedEditors)) {
            $editor = "ckeditor";
        }
        $this->html_editor = $editor;
    }

    public function getRequirePasswordChange(): bool {
        return boolval($this->require_password_change);
    }

    public function setRequirePasswordChange($val): void {
        $this->require_password_change = boolval($val);
    }

    public function getAdmin(): bool {
        return boolval($this->admin);
    }

    public function setAdmin($val): void {
        $this->admin = boolval($val);
    }

    public function getLocked(): bool {
        return boolval($this->locked);
    }

    public function setLocked($val) {
        $this->locked = boolval($val);
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function setLastLogin($val): void {
        $this->last_login = !is_null($val) ? intval($val) : null;
    }

    public function getFailedLogins(): int {
        $failedLogins = 0;
        if (!is_null($this->id)) {

            $sql = "select failed_logins from {prefix}users where id = ?";
            $args = array(
                $this->id
            );
            $result = Database::pQuery($sql, $args, true);
            if (Database::any($result)) {
                $data = Database::fetchObject($result);
                $failedLogins = intval($data->failed_logins);
            }
        }
        return $failedLogins;
    }

    public function increaseFailedLogins(): void {
        if (is_null($this->id)) {
            return;
        }
        $time = intval($time);
        $sql = "update {prefix}users set failed_logins = failed_logins + 1 "
                . "where id = ?";
        $args = array(
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function resetFailedLogins(): void {
        if (is_null($this->id)) {
            return;
        }
        $time = intval($time);
        $sql = "update {prefix}users set failed_logins = ? "
                . "where id = ?";
        $args = array(
            0,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function setFailedLogins($amount): void {
        if (is_null($this->id)) {
            return;
        }
        $time = intval($time);
        $sql = "update {prefix}users set failed_logins = ? where id = ?";
        $args = array(
            $amount,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function getHomepage(): ?string {
        return $this->homepage;
    }

    public function setHomepage(?string $val): void {
        $this->homepage = strval($val);
    }

    public function getDefaultLanguage(): ?string {
        return $this->default_language;
    }

    public function setDefaultLanguage(?string $val): void {
        $this->default_language = StringHelper::isNotNullOrWhitespace($val) ?
                strval($val) : null;
    }

    public function getAvatar(): ?string {
        return ModuleHelper::getBaseUrl("/admin/gfx/no_avatar.png");
    }

    public function setAvatar(): void {
        throw new NotImplementedException(
                "Avatar feature is not implemented yet."
        );
    }

    public function getSecondaryGroups(): array {
        return $this->secondary_groups;
    }

    public function setSecondaryGroups(array $val): void {
        $this->secondary_groups = $val;
    }

    public function getAllGroups() {
        $primaryGroup = [$this->getPrimaryGroup()];
        $secondaryGroups = $this->getSecondaryGroups();

        $groups = array_merge($primaryGroup, $secondaryGroups);
        $groups = array_filter($groups);
        return array_values($groups);
    }

    public function addSecondaryGroup($val): void {
        $this->secondary_groups[] = $val;
    }

    public function removeSecondaryGroup($val) {
        $filtered = [];
        foreach ($this->secondary_groups as $group) {
            if ($group->getID() != $val->getID()) {
                $filtered[] = $group;
            }
        }
        return $filtered;
    }

    public function getPermissionChecker(): PermissionChecker {
        return new PermissionChecker($this->getId());
    }

    public function hasPermission(string $permission): bool {
        return $this->getPermissionChecker()->hasPermission($permission);
    }

    private function loadGroups($user_id): void {
        $groups = [];

        $sql = "select `group_id` from `{prefix}user_groups` where user_id = ?";
        $args = array(
            intval($user_id)
        );
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $groups[] = new Group($row->group_id);
        }
        $this->setSecondaryGroups($groups);
    }

    private function saveGroups(): void {
        Database::pQuery(
                "delete from {prefix}user_groups where user_id = ?",
                [
                    $this->getId()
                ]
                , true);
        foreach ($this->secondary_groups as $group) {
            Database::pQuery("insert into {prefix}user_groups
                              (user_id, group_id)
                              VALUES
                              (?,?)", array(
                $this->getID(),
                $group->getID()
                    ), true);
        }
    }

}
