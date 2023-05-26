<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\HtmlEditor;
use App\Helpers\ImagineHelper;
use App\Models\Users\GroupCollection;
use App\Models\Users\PasswordReset;
use App\Security\Hash;
use App\Security\Permissions\PermissionChecker;
use App\Utils\Mailer;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

/**
 * User model
 */
class User extends Model {
    protected $id = null;

    private $username = null;

    private $lastname = '';

    private $firstname = '';

    private $email = '';

    private $password = '';

    private $about_me = '';

    private $group_id = null;

    private $secondary_groups = [];

    private $group = null;

    private $html_editor = 'ckeditor';

    private $require_password_change = false;

    private $admin = false;

    private $password_changed = null;

    private $locked = false;

    private $last_login = null;

    private $homepage = '';

    private $default_language = null;

    /**
     * Constructor
     * @param ?ìnt $id
     */
    public function __construct(?int $id = null) {
        if ($id) {
            $this->loadById($id);
        }
    }

    /**
     * Load user by id
     * @param type $id
     * @return void
     */
    public function loadById($id): void {
        $sql = 'select * from {prefix}users where id = ?';
        $args = [
            (int)$id
        ];
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    /**
     * Load user by username
     * @param string $name
     * @return void
     */
    public function loadByUsername(string $name): void {
        $sql = 'select * from {prefix}users where username '
                . 'COLLATE utf8mb4_general_ci = ?';
        $args = [
            $name
        ];
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    /**
     * Load user by email
     * @param string $email
     * @return void
     */
    public function loadByEmail(string $email): void {
        $sql = 'select * from {prefix}users where email '
                . 'COLLATE utf8mb4_general_ci = ?';

        $args = [
            $email
        ];

        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    /**
     * Load user from session data
     * @return User|null
     */
    public static function fromSessionData(): ?User {
        return get_user_id() ? new self(get_user_id()) : null;
    }

    /**
     * Converts user to session data
     * @return array|null
     */
    public function toSessionData(): ?array {
        return $this->isPersistent() ? [
            'ulicms_login' => $this->getUsername(),
            'lastname' => $this->getLastname(),
            'firstname' => $this->getFirstname(),
            'email' => $this->getEmail(),
            'login_id' => $this->getId(),
            'require_password_change' => $this->getRequirePasswordChange(),
            'group_id' => $this->getPrimaryGroupId(),
            'logged_in' => true,
            'session_begin' => time()
        ] : null;
    }

    /**
     * Register a session from this user
     * @param bool $redirect Redirect after login
     * @throws BadMethodCallException
     * @return void
     */
    public function registerSession(bool $redirect = true): void {
        $sessionData = $this->toSessionData();

        if (! is_array($sessionData)) {
            throw new BadMethodCallException();
        }
        if (! session_id()) {
            App\Utils\Session\sessionStart();
        }

        $_SESSION['logged_in'] = true;

        foreach ($sessionData as $key => $value) {
            $_SESSION[$key] = $value;
        }

        $this->setLastLogin(time());
        $this->save();

        if (! $redirect || is_cli()) {
            return;
        }
        $login_url = apply_filter('index.php', 'login_url');

        if (isset($_REQUEST['go'])) {
            Response::safeRedirect($_REQUEST['go']);
        } else {
            $login_url = apply_filter('index.php', 'login_url');
            Response::redirect($login_url);
        }
    }

    /**
     * Save user
     * @return void
     */
    public function save(): void {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
        $this->saveGroups();
    }

    /**
     * Create a new user and send welcome mail
     * @param string $password
     * @return void
     */
    public function saveAndSendMail(string $password): bool {
        $this->save();
        return $this->sendWelcomeMail($password);
    }

    /**
     * Send welcome mail to user
     * @param string $password
     * @return bool
     */
    public function sendWelcomeMail(string $password): bool {
        $subject = get_translation(
            'new_user_account_at_site',
            [
                '%domain%' => get_domain()
            ]
        );
        $mailBody = $this->getWelcomeMailText($password);
        $headers = 'From: ' . Settings::get('email');

        return Mailer::send($this->getEmail(), $subject, $mailBody, $headers);
    }

    /**
     * Get text for welcome mail
     * @param string $password
     * @return string
     */
    public function getWelcomeMailText(string $password): string {
        \App\Storages\ViewBag::set('user', $this);
        \App\Storages\ViewBag::set('url', \App\Helpers\ModuleHelper::getBaseUrl());
        \App\Storages\ViewBag::set('password', $password);
        return Template::executeDefaultOrOwnTemplate('email/user_welcome.php');
    }

    /**
     * Fil vars from database
     * @param type $result
     * @return void
     */
    public function fillVars($result = null): void {
        if (Database::any($result)) {
            $result = Database::fetchAssoc($result);
            foreach ($result as $key => $value) {
                if (isset($this->{$key}) || property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
            if ($this->group_id !== null) {
                $this->group = new Group($this->group_id);
            } else {
                $this->group = null;
            }
            // load secondary groups
            $this->loadGroups($result['id']);
            $this->setId((int)$result['id']);
            return;
        }
        $this->setSecondaryGroups([]);
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Set id
     * @param int|null $id
     * @return void
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * Get username
     * @return string|null
     */
    public function getUsername(): ?string {
        return $this->username;
    }

    /**
     * Set username
     * @param string $username
     * @return void
     */
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    /**
     * Get lastname
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * Set lastname
     * @param string|null $lastname
     * @return void
     */
    public function setLastname(?string $lastname): void {
        $this->lastname = $lastname;
    }

    /**
     * Get firstname
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * Set firstname
     * @param string|null $firstname
     * @return void
     */
    public function setFirstname(?string $firstname): void {
        $this->firstname = $firstname;
    }

    /**
     * Get email address
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * Set email address
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    /**
     * Delete user
     * @return bool
     */
    public function delete() {
        if ($this->id === null) {
            return false;
        }

        $sql = 'delete from {prefix}users where id = ?';
        $args = [
            $this->id
        ];
        $result = Database::pQuery($sql, $args, true);

        $this->removeAvatar();

        if ($result) {
            $this->id = null;
        }

        return $result;
    }

    /**
     * Get hashed password
     * @return string|null
     */
    public function getPassword(): ?string {
        return $this->password;
    }

    /**
     * Set password
     * @param string|null $password
     * @return void
     */
    public function setPassword(string $password): void {
        $this->password = Hash::hashPassword($password);
        $this->password_changed = date('Y-m-d H:i:s');
    }

    /**
     * Get datetime of last password change
     * @return string|null
     */
    public function getPasswordChanged(): ?string {
        return $this->password_changed;
    }

    /**
     * Reset password
     * @return void
     */
    public function resetPassword(): void {
        // Create password reset model
        $passwordReset = new PasswordReset();

        // Generate token for this user
        $token = $passwordReset->addToken($this->getId());

        // Send confirmation mail
        $passwordReset->sendMail(
            $token,
            $this->getEmail(),
            get_ip(),
            $this->getFirstname(),
            $this->getLastname()
        );
    }

    /**
     * Get "Firstname lastname"
     * @return string
     */
    public function getFullName(): string {
        return trim("{$this->firstname} {$this->lastname}");
    }

    /**
     * Get full name or username
     * @return string
     */
    public function getDisplayName(): string {
        $name = ! empty($this->getFullName()) ? $this->getFullName() : $this->getUsername();
        return $name ?? '';
    }

    /**
     * Check password
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool {
        return Hash::hashPassword($password) == $this->getPassword();
    }

    /**
     * Get about me text
     * @return string|null
     */
    public function getAboutMe(): ?string {
        return $this->about_me;
    }

    /**
     * Set about me text
     * @param string|null $text
     * @return void
     */
    public function setAboutMe(?string $text): void {
        $this->about_me = $text;
    }

    /**
     * Get unix timestamp of last action
     * @return int
     */
    public function getLastAction(): int {
        $lastAction = 0;
        if ($this->id !== null) {
            $sql = 'select last_action from {prefix}users where id = ?';
            $args = [
                $this->id
            ];
            $result = Database::pQuery($sql, $args, true);
            if (Database::any($result)) {
                $data = Database::fetchObject($result);
                $lastAction = (int)$data->last_action;
            }
        }
        return $lastAction;
    }

    /**
     * Update last action of this user
     * @param int|null $time
     * @return void
     */
    public function setLastAction(int $time): void {
        if ($this->id === null) {
            return;
        }

        $sql = 'update {prefix}users set last_action = ? where id = ?';
        $args = [
            $time,
            $this->id
        ];
        Database::pQuery($sql, $args, true);
    }

    /**
     * Get primary group
     *
     * @return ?int
     */
    public function getPrimaryGroupId() {
        return $this->group_id;
    }

    /**
     * Set primary group
     * @param type $gid
     * @return void
     */
    public function setPrimaryGroupId($gid): void {
        $this->group_id = $gid;
        $this->group = $gid;
    }

    /**
     * Get primary group
     * @return ?Group
     */
    public function getPrimaryGroup() {
        return $this->group;
    }

    /**
     * Set primary group
     * @param type $group
     * @return void
     */
    public function setPrimaryGroup($group): void {
        $this->group = $group;
        $this->group_id = $group ? $group->getId() : null;
    }

    /**
     * Get html editor
     * @return string|null
     */
    public function getHTMLEditor(): ?string {
        return $this->html_editor;
    }

    /**
     * Set html editor
     * @param string $editor
     * @return void
     */
    public function setHTMLEditor(string $editor): void {
        $allowedEditors = [
            HtmlEditor::CKEDITOR,
            HtmlEditor::CODEMIRROR,
        ];

        if (! in_array($editor, $allowedEditors)) {
            throw new InvalidArgumentException("Value {$editor} not allowed");
        }

        $this->html_editor = $editor;
    }

    /**
     * Check if password change is required
     * @return bool
     */
    public function getRequirePasswordChange(): bool {
        return (bool)$this->require_password_change;
    }

    /**
     * Set require_password_change
     * @param type $val
     * @return void
     */
    public function setRequirePasswordChange($val): void {
        $this->require_password_change = (bool)$val;
    }

    /**
     * Check if the "admin" flag is set
     * The "admin" flag enables unlimited acccess to the system
     * @return bool
     */
    public function isAdmin(): bool {
        return (bool)$this->admin;
    }

    /**
     * Set the "admin" flag which enables unlimited access to the system
     * @param type $val
     * @return void
     */
    public function setAdmin($val): void {
        $this->admin = (bool)$val;
    }

    /**
     * If account is locked
     * @return bool
     */
    public function isLocked(): bool {
        return (bool)$this->locked;
    }

    /**
     * Set if account is locked
     * @param type $val
     */
    public function setLocked($val): void {
        $this->locked = (bool)$val;
    }

    /**
     * Get datetime of last login
     * @return type
     */
    public function getLastLogin() {
        return $this->last_login;
    }

    /**
     * Set datetime of last login
     * @param type $val
     * @return void
     */
    public function setLastLogin($val): void {
        $this->last_login = $val;
    }

    /**
     * Get failed logins
     * @return int
     */
    public function getFailedLogins(): int {
        $failedLogins = 0;
        if ($this->id !== null) {
            $sql = 'select failed_logins from {prefix}users where id = ?';
            $args = [
                $this->id
            ];
            $result = Database::pQuery($sql, $args, true);
            if (Database::any($result)) {
                $data = Database::fetchObject($result);
                $failedLogins = (int)$data->failed_logins;
            }
        }
        return $failedLogins;
    }

    /**
     * Increase failed logins
     * @return void
     */
    public function increaseFailedLogins(): void {
        if ($this->id === null) {
            return;
        }

        $sql = 'update {prefix}users set failed_logins = failed_logins + 1 '
                . 'where id = ?';
        $args = [
            $this->id
        ];

        Database::pQuery($sql, $args, true);
    }

    /**
     * Set failed logins to 0
     * @return bool
     */
    public function resetFailedLogins(): bool {
        return $this->setFailedLogins(0);
    }

    /**
     * Set failed logins
     * @param int $amount
     * @return bool
     */
    public function setFailedLogins(int $amount): bool {
        if ($this->id === null) {
            return false;
        }

        $sql = 'update {prefix}users set failed_logins = ? where id = ?';
        $args = [
            $amount,
            $this->id
        ];
        Database::pQuery($sql, $args, true);

        return Database::getAffectedRows($amount) > 0;
    }

    /**
     * Get homepage
     * @return string|null
     */
    public function getHomepage(): ?string {
        return $this->homepage;
    }

    /**
     * Set homepage
     * @param string|null $val
     * @return void
     */
    public function setHomepage(?string $val): void {
        $this->homepage = (string)$val;
    }

    /**
     * Get default language of this user
     * @return string|null
     */
    public function getDefaultLanguage(): ?string {
        return $this->default_language;
    }

    /**
     * Set default language
     * @param string|null $val
     * @return void
     */
    public function setDefaultLanguage(?string $val): void {
        $this->default_language = ! empty($val) ? (string)$val : null;
    }

    /**
     *
     * Get avatar for the current user
     * @return string|null
     */
    public function getAvatar(): ?string {
        // Fallback "No Avatar" picture
        $avatarUrl = \App\Helpers\ModuleHelper::getBaseUrl(
            ! is_admin_dir() ?
            '/admin/gfx/no_avatar.png' : '/gfx/no_avatar.png'
        );

        // Avatar directory
        $userAvatarDirectory = Path::resolve('ULICMS_CONTENT/avatars');

        // Create avatar directory if not exists
        if (! is_dir($userAvatarDirectory)) {
            mkdir($userAvatarDirectory, 0777, true);
        }

        // If there is a display name (Firstname Lastname)
        if (is_dir($userAvatarDirectory) && $this->getDisplayName()) {
            // Custom avatar
            $avatarImageFile1 = Path::Resolve("{$userAvatarDirectory}/user-" .
                            $this->getId() . '.png');
            // Auto generated avatar based on the name of the user
            $avatarImageFile2 = Path::Resolve("{$userAvatarDirectory}/" .
                            md5($this->getDisplayName()) . '.png');

            // relative URL to file
            $url = ! is_admin_dir() ?
                    'content/avatars/user-' . $this->getId() . '.png' :
                    '../content/avatars/user-' . $this->getId() . '.png';

            // Generate initial letter avatar if it doesn't exist
            $avatarUrl = is_file($avatarImageFile1) ?
                    $url : $this->generateAvatar($avatarImageFile2);
        }

        return $avatarUrl;
    }

    /**
     * Set avatar
     * @param type $file
     * @return void
     */
    public function setAvatar($file): void {
        $this->processAvatar($file);
    }

    /**
     * Get secondary groups of a user
     * @return array
     */
    public function getSecondaryGroups(): array {
        return $this->secondary_groups;
    }

    /**
     * Set secondary groups of a user
     * @param array $val
     * @return void
     */
    public function setSecondaryGroups(array $val): void {
        $this->secondary_groups = $val;
    }

    /**
     * Get all groups including primary and secondary
     * @return array
     */
    public function getAllGroups(): array {
        $primaryGroup = [$this->getPrimaryGroup()];
        $secondaryGroups = $this->getSecondaryGroups();

        $groups = array_merge($primaryGroup, $secondaryGroups);
        $groups = array_filter($groups);
        return array_values($groups);
    }

    /**
     * Get Group collection
     * @return GroupCollection
     */
    public function getGroupCollection(): GroupCollection {
        return new GroupCollection($this);
    }

    /**
     * Add secondary group
     * @param type $val
     * @return void
     */
    public function addSecondaryGroup($val): void {
        $this->secondary_groups[] = $val;
    }

    /**
     * Remove secondary group
     * @param type $val
     * @return void
     */
    public function removeSecondaryGroup($val): void {
        $filtered = [];
        foreach ($this->secondary_groups as $group) {
            if ($group->getID() != $val->getID()) {
                $filtered[] = $group;
            }
        }
        $this->secondary_groups = $filtered;
    }

    /**
     * Get PermissionChecker for this user
     * @return PermissionChecker
     */
    public function getPermissionChecker(): PermissionChecker {
        return new PermissionChecker($this->getId());
    }

    /**
     * Check if the user has a permission
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool {
        return $this->getPermissionChecker()->hasPermission($permission);
    }

    /**
     * Change avatar image from upload
     * @param array $upload
     * @return bool
     */
    public function changeAvatar(array $upload): bool {
        $extension = pathinfo($upload['name'], PATHINFO_EXTENSION);
        $tmpFile = uniqid() . '.' . $extension;
        $tmpFile = Path::resolve("ULICMS_TMP/{$tmpFile}");

        if (move_uploaded_file($upload['tmp_name'], $tmpFile)) {
            $this->processAvatar($tmpFile);
            unlink($tmpFile);
            return true;
        }
        return false;
    }

    /**
     * Resize / convert avatar
     * @param string $inputFile
     * @return void
     */
    public function processAvatar(string $inputFile): void {
        $imagine = ImagineHelper::getImagine();

        $size = new Imagine\Image\Box(128, 218);
        $mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;

        $generatedAvatar = $this->getProcessedAvatarPath();

        $imagine->open($inputFile)
            ->thumbnail($size, $mode)
            ->save($generatedAvatar);
    }

    /**
     * Remove avatar of this user
     * @return bool
     */
    public function removeAvatar(): bool {
        $generatedAvatar = $this->getProcessedAvatarPath();
        if ($generatedAvatar && is_file($generatedAvatar)) {
            return unlink($generatedAvatar);
        }
        return false;
    }

    /**
     * Check if the user has a processed avatar
     * @return bool
     */
    public function hasProcessedAvatar(): bool {
        return
            $this->getProcessedAvatarPath() &&
            is_file($this->getProcessedAvatarPath());
    }

    /**
     * Check if user is current online
     * @return bool
     */
    public function isOnline(): bool {
        $onlineUsers = self::getOnlineUsers();

        foreach ($onlineUsers as $user) {
            if ($user->getUserName() == $this->getUsername()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if this user is current online
     * @return bool
     */
    public function isCurrent(): bool {
        return $this->getId() && $this->getId() == get_user_id();
    }

    /**
     * Get online users
     * @return array
     */
    public static function getOnlineUsers(): array {
        $query = Database::selectAll(
            'users',
            ['id'],
            'last_action > ' . (time() - 300) . ' ORDER BY username'
        );

        $users = [];
        while ($row = Database::fetchObject($query)) {
            $users[] = new self((int)$row->id);
        }
        return $users;
    }

    /**
     * Insert user
     * @return void
     */
    protected function insert(): void {
        $sql = 'insert into {prefix}users (username, lastname, firstname,
                email, password, about_me, group_id, html_editor,
                require_password_change, admin,
                password_changed, locked, last_login,
                homepage, default_language) values
                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $args = [
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
        ];
        Database::pQuery($sql, $args, true);
        $this->id = Database::getLastInsertID();
    }

    /**
     * Update user
     * @return void
     */
    protected function update(): void {
        $sql = 'update {prefix}users set username = ?, lastname = ?,
            firstname = ?, email = ?, password = ?, about_me = ?,
            group_id = ?, html_editor = ?,
            require_password_change = ?, admin = ?, password_changed = ?,
            locked = ?, last_login = ?,
            homepage = ?, default_language = ? where id = ?';
        $args = [
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
        ];
        Database::pQuery($sql, $args, true);
    }

    /**
     * Generates an avatar based on the the capitals of the users
     * firstname and lastname.
     *  The file is cached for performance reasons
     * @param string $avatarImageFile
     * @return string
     */
    protected function generateAvatar(string $avatarImageFile): string {
        if (! is_file($avatarImageFile)) {
            $avatar = new InitialAvatar();
            $image = $avatar->name($this->getDisplayName())->
                            rounded()->smooth()->
                            autoFont()->fontSize(0.35)->
                            size(40)->generate();
            $image->save($avatarImageFile);
        }

        $url = ! is_admin_dir() ?
                'content/avatars/' . md5($this->getDisplayName()) . '.png' :
                '../content/avatars/' . md5($this->getDisplayName()) . '.png';

        $avatarUrl = $url;

        return $avatarUrl;
    }

    /**
     * Load groups secondary groups of this user
     * @param type $user_id
     * @return void
     */
    protected function loadGroups($user_id): void {
        $groups = [];

        $sql = 'select `group_id` from `{prefix}user_groups` where user_id = ?';
        $args = [
            (int)$user_id
        ];
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $groups[] = new Group($row->group_id);
        }
        $this->setSecondaryGroups($groups);
    }

    /**
     * Save secondary groups of this user
     * @return void
     */
    protected function saveGroups(): void {
        Database::pQuery(
            'delete from {prefix}user_groups where user_id = ?',
            [
                $this->getId()
            ],
            true
        );
        foreach ($this->secondary_groups as $group) {
            Database::pQuery(
                'insert into {prefix}user_groups
                              (user_id, group_id)
                              VALUES
                              (?,?)',
                [
                    $this->getID(),
                    $group->getID()
                ],
                true
            );
        }
    }

    /**
     * Get path of processed avatar
     * @return string|null
     */
    protected function getProcessedAvatarPath(): ?string {
        return $this->isPersistent() ? Path::resolve(
            'ULICMS_ROOT/content/avatars/user-' .
            $this->getId() . '.png'
        ) : null;
    }
}
