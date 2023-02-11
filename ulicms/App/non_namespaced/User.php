<?php

declare(strict_types=1);

use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use App\Security\PermissionChecker;
use App\Security\Encryption;
use App\Models\Users\GroupCollection;

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

    public function loadById($id): void {
        $sql = "select * from {prefix}users where id = ?";
        $args = array(
            (int) $id
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
            UliCMS\Utils\Session\sessionStart();
        }

        $_SESSION["logged_in"] = true;

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
        $args = [
            $name
        ];
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function loadByEmail(string $email): void {
        $sql = "select * from {prefix}users where email "
                . "COLLATE utf8mb4_general_ci = ?";

        $args = [
            $email
        ];

        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function save(): void {
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
        $subject = get_translation(
                "new_user_account_at_site",
                array("%domain%" => get_domain())
        );
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

    public function fillVars($result = null): void {
        if (Database::any($result)) {
            $result = Database::fetchAssoc($result);
            foreach ($result as $key => $value) {
                if (isset($this->$key) || property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            if ($this->group_id !== NULL) {
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
        $result = Database::pQuery($sql, $args, true);
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

    public function setId($id): void {
        $this->id = $id !== null ? (int) $id : null;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void {
        $this->lastname = $lastname;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void {
        $this->firstname = $firstname;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function delete() {
        if ($this->id === NULL) {
            return false;
        }

        $sql = "delete from {prefix}users where id = ?";
        $args = array(
            $this->id
        );
        $result = Database::pQuery($sql, $args, true);

        $this->removeAvatar();

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
    public function getPasswordChanged(): ?string {
        return $this->password_changed;
    }

    // reset password for this user
    public function resetPassword(): void {
        $passwordReset = new PasswordReset();
        $token = $passwordReset->addToken($this->getId());
        $passwordReset->sendMail(
                $token,
                $this->getEmail(),
                get_ip(),
                $this->getFirstname(),
                $this->getLastname()
        );
    }

    public function getFullName(): string {
        return trim("{$this->firstname} {$this->lastname}");
    }

    public function getDisplayName(): string {
        $name = !empty($this->getFullName()) ? $this->getFullName() : $this->getUsername();
        return $name ?? "";
    }

    public function checkPassword(string $password): bool {
        return Encryption::hashPassword($password) == $this->getPassword();
    }

    public function getAboutMe(): ?string {
        return $this->about_me;
    }

    public function setAboutMe(?string $text): void {
        $this->about_me = $text;
    }

    public function getLastAction(): int {
        $lastAction = 0;
        if ($this->id !== NULL) {
            $sql = "select last_action from {prefix}users where id = ?";
            $args = array(
                $this->id
            );
            $result = Database::pQuery($sql, $args, true);
            if (Database::any($result)) {
                $data = Database::fetchObject($result);
                $lastAction = intval($data->last_action);
            }
        }
        return $lastAction;
    }

    public function setLastAction(?int $time): void {
        if ($this->id === NULL) {
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

    public function setPrimaryGroupId($gid): void {
        $this->group_id = $gid;
        $this->group = $gid;
    }

    public function setGroupId($gid): void {
        $this->setPrimaryGroupId($gid);
    }

    public function getPrimaryGroup() {
        return $this->group;
    }

    public function getGroup() {
        return $this->getPrimaryGroup();
    }

    public function setPrimaryGroup($group): void {
        $this->group = $group;
        $this->group_id = $group ? $group->getId() : null;
    }

    public function setGroup($group): void {
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

        if (!in_array($editor, $allowedEditors)) {
            $editor = "ckeditor";
        }

        $this->html_editor = $editor;
    }

    public function getRequirePasswordChange(): bool {
        return (bool) $this->require_password_change;
    }

    public function setRequirePasswordChange($val): void {
        $this->require_password_change = (bool) $val;
    }

    public function isAdmin(): bool {
        return (bool) $this->admin;
    }

    public function setAdmin($val): void {
        $this->admin = (bool) $val;
    }

    public function isLocked(): bool {
        return (bool) $this->locked;
    }

    public function setLocked($val) {
        $this->locked = (bool) $val;
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function setLastLogin($val): void {
        $this->last_login = $val;
    }

    public function getFailedLogins(): int {
        $failedLogins = 0;
        if ($this->id !== NULL) {
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
        if ($this->id === NULL) {
            return;
        }
        $sql = "update {prefix}users set failed_logins = failed_logins + 1 "
                . "where id = ?";
        $args = array(
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function resetFailedLogins(): void {
        if ($this->id === NULL) {
            return;
        }
        $sql = "update {prefix}users set failed_logins = ? "
                . "where id = ?";
        $args = array(
            0,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function setFailedLogins($amount): void {
        if ($this->id === NULL) {
            return;
        }
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
        $this->homepage = (string) $val;
    }

    public function getDefaultLanguage(): ?string {
        return $this->default_language;
    }

    public function setDefaultLanguage(?string $val): void {
        $this->default_language = StringHelper::isNotNullOrWhitespace($val) ?
                (string) $val : null;
    }

    // Since 2020.1:
    // generates an avatar based of the capitals of the
    // users name. if the user isn't logged in, returns the default
    // no avatar pic
    public function getAvatar(): ?string {
        $avatarUrl = ModuleHelper::getBaseUrl(
                        !is_admin_dir() ?
                        "/admin/gfx/no_avatar.png" : "/gfx/no_avatar.png"
        );

        $userAvatarDirectory = Path::resolve("ULICMS_CONTENT/avatars");

        if (!is_dir($userAvatarDirectory)) {
            mkdir($userAvatarDirectory, 0777, true);
        }

        if (is_dir($userAvatarDirectory) && $this->getDisplayName()) {
            $avatarImageFile1 = Path::Resolve("$userAvatarDirectory/user-" .
                            $this->getId() . ".png");
            $avatarImageFile2 = Path::Resolve("$userAvatarDirectory/" .
                            md5($this->getDisplayName()) . ".png");

            $url = !is_admin_dir() ?
                    "content/avatars/user-" . $this->getId() . ".png" :
                    "../content/avatars/user-" . $this->getId() . ".png";

            // generate initial letter avatar if it doesn't exist
            $avatarUrl = is_file($avatarImageFile1) ?
                    $url : $this->generateAvatar($avatarImageFile2);
        }
        return $avatarUrl;
    }

    // generates an avatar based on the the capitals
    // of the users first- and lastname
    // the file is cached for performance reasons
    protected function generateAvatar(string $avatarImageFile): string {
        if (!is_file($avatarImageFile)) {
            $avatar = new InitialAvatar();
            $image = $avatar->name($this->getDisplayName())->
                            rounded()->smooth()->
                            autoFont()->fontSize(0.35)->
                            size(40)->generate();
            $image->save($avatarImageFile);
        }

        $url = !is_admin_dir() ?
                "content/avatars/" . md5($this->getDisplayName()) . ".png" :
                "../content/avatars/" . md5($this->getDisplayName()) . ".png";

        $avatarUrl = $url;

        return $avatarUrl;
    }

    public function setAvatar($file): void {
        $this->processAvatar($file);
    }

    public function getSecondaryGroups(): array {
        return $this->secondary_groups;
    }

    public function setSecondaryGroups(array $val): void {
        $this->secondary_groups = $val;
    }

    public function getAllGroups(): array {
        $primaryGroup = [$this->getPrimaryGroup()];
        $secondaryGroups = $this->getSecondaryGroups();

        $groups = array_merge($primaryGroup, $secondaryGroups);
        $groups = array_filter($groups);
        return array_values($groups);
    }

    public function getGroupCollection(): GroupCollection {
        return new GroupCollection($this);
    }

    public function addSecondaryGroup($val): void {
        $this->secondary_groups[] = $val;
    }

    public function removeSecondaryGroup($val): void {
        $filtered = [];
        foreach ($this->secondary_groups as $group) {
            if ($group->getID() != $val->getID()) {
                $filtered[] = $group;
            }
        }
        $this->secondary_groups = $filtered;
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
                ],
                true
        );
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

    public function changeAvatar(array $upload): bool {
        $extension = pathinfo($upload["name"], PATHINFO_EXTENSION);
        $tmpFile = uniqid() . "." . $extension;
        $tmpFile = Path::resolve("ULICMS_TMP/$tmpFile");

        if (move_uploaded_file($upload["tmp_name"], $tmpFile)) {
            $this->processAvatar($tmpFile);
            unlink($tmpFile);
            return true;
        }
        return false;
    }

    public function processAvatar(string $inputFile): void {
        $imagine = ImagineHelper::getImagine();

        $size = new Imagine\Image\Box(128, 218);
        $mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;

        $generatedAvatar = $this->getProcessedAvatarPath();

        $imagine->open($inputFile)
                ->thumbnail($size, $mode)
                ->save($generatedAvatar);
    }

    protected function getProcessedAvatarPath(): ?string {
        return $this->isPersistent() ? Path::resolve(
                        "ULICMS_ROOT/content/avatars/user-" .
                        $this->getId() . ".png"
                ) : null;
    }

    public function removeAvatar(): bool {
        $generatedAvatar = $this->getProcessedAvatarPath();
        if ($generatedAvatar && is_file($generatedAvatar)) {
            return unlink($generatedAvatar);
        }
        return false;
    }

    public function hasProcessedAvatar(): bool {
        return (
                $this->getProcessedAvatarPath() &&
                is_file($this->getProcessedAvatarPath())
                );
    }

    public function isOnline(): bool {
        $onlineUsers = self::getOnlineUsers();

        foreach ($onlineUsers as $user) {
            if ($user->getUserName() == $this->getUsername()) {
                return true;
            }
        }
        return false;
    }

    public function isCurrent(): bool {
        return $this->getId() && $this->getId() == get_user_id();
    }

    public static function getOnlineUsers(): array {
        $query = Database::selectAll(
                        "users",
                        ["id"],
                        "last_action > " . (time() - 300) . " ORDER BY username"
        );

        $users = [];
        while ($row = Database::fetchObject($query)) {
            $users[] = new self($row->id);
        }
        return $users;
    }

}
