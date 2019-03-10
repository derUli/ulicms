<?php

use UliCMS\Exceptions\NotImplementedException;
use UliCMS\Security\PermissionChecker;

class User {

    private $id = null;
    private $username = null;
    private $lastname = "";
    private $firstname = "";
    private $email = "";
    private $password = "";
    private $old_encryption = false;
    private $about_me = "";
    private $group_id = null;
    private $secondary_groups = array();
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
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    public function loadByUsername($name) {
        $sql = "select * from {prefix}users where username = ?";
        $args = array(
            strval($name)
        );
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    public function save() {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
        $this->saveGroups();
    }

    public function saveAndSendMail() {
        $this->save();
        // TODO: Send Mail to new user
    }

    public function fillVars($query) {
        if (Database::any($query)) {
            $result = Database::fetchAssoc($query);
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
        } else {
            $this->setSecondaryGroups(array());
        }
    }

    protected function insert() {
        $sql = "insert into {prefix}users (username, lastname, firstname, email, password,
				old_encryption, about_me, group_id, html_editor,
				require_password_change, admin, password_changed, locked, last_login,
				homepage, default_language) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $args = array(
            $this->username,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->password,
            $this->old_encryption,
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
        $sql = "update {prefix}users set username = ?, lastname = ?, firstname = ?, email = ?, password = ?,
				old_encryption = ?, about_me = ?, group_id = ?, html_editor = ?,
				require_password_change = ?, admin = ?, password_changed = ?, locked = ?, last_login = ?,
				homepage = ?, default_language = ? where id = ?";
        $args = array(
            $this->username,
            $this->lastname,
            $this->firstname,
            $this->email,
            $this->password,
            $this->old_encryption,
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
        Database::pQuery($sql, $args, true) or die(Database::getError());
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = !is_null($id) ? intval($id) : null;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = !is_null($username) ? strval($username) : null;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setLastname($lastname) {
        $this->lastname = !is_null($lastname) ? strval($lastname) : null;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function setFirstname($firstname) {
        $this->firstname = !is_null($firstname) ? strval($firstname) : null;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
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

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = Encryption::hashPassword($password);
        $this->old_encryption = false;
        $this->password_changed = date("Y-m-d H:i:s");
    }

    public function getPasswordChanged() {
        return $this->password_changed;
    }

    public function resetPassword() {
        $passwordReset = new PasswordReset();
        $token = $passwordReset->addToken($this->getId());
        $passwordReset->sendMail($token, $this->getEmail(), "xxx.xxx.xxx.xxx", $this->getFirstname(), $this->getLastname());
    }

    public function getOldEncryption() {
        return $this->old_encryption;
    }

    public function setOldEncryption($value) {
        $this->old_encryption = boolval($value);
    }

    public function getAboutMe() {
        return $this->about_me;
    }

    public function setAboutMe($text) {
        $this->about_me = !is_null($text) ? strval($text) : null;
    }

    public function getLastAction() {
        $result = 0;
        if (!is_null($this->id)) {

            $sql = "select last_action from {prefix}users where id = ?";
            $args = array(
                $this->id
            );
            $query = Database::pQuery($sql, $args, true);
            if (Database::any($query)) {
                $data = Database::fetchObject($query);
                $result = $data->last_action;
            }
        }
        return $result;
    }

    public function setLastAction($time) {
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

    public function setGroupid($gid) {
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

    public function getHTMLEditor() {
        return $this->html_editor;
    }

    public function setHTMLEditor($editor) {
        $allowedEditors = array(
            "ckeditor",
            "codemirror"
        );
        if (!faster_in_array($editor, $allowedEditors)) {
            $editor = "ckeditor";
        }
        $this->html_editor = $editor;
    }

    public function getRequirePasswordChange() {
        return boolval($this->require_password_change);
    }

    public function setRequirePasswordChange($val) {
        $this->require_password_change = boolval($val);
    }

    public function getAdmin() {
        return boolval($this->admin);
    }

    public function setAdmin($val) {
        $this->admin = boolval($val);
    }

    public function getLocked() {
        return boolval($this->locked);
    }

    public function setLocked($val) {
        $this->locked = boolval($val);
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function setLastLogin($val) {
        $this->last_login = !is_null($val) ? intval($val) : null;
    }

    public function getFailedLogins() {
        $result = 0;
        if (!is_null($this->id)) {

            $sql = "select failed_logins from {prefix}users where id = ?";
            $args = array(
                $this->id
            );
            $query = Database::pQuery($sql, $args, true);
            if (Database::any($query)) {
                $data = Database::fetchObject($query);
                $result = $data->failed_logins;
            }
        }
        return $result;
    }

    public function increaseFailedLogins() {
        if (is_null($this->id)) {
            return;
        }
        $time = intval($time);
        $sql = "update {prefix}users set failed_logins = failed_logins + 1 where id = ?";
        $args = array(
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function resetFailedLogins() {
        if (is_null($this->id)) {
            return;
        }
        $time = intval($time);
        $sql = "update {prefix}users set failed_logins = ? where id = ?";
        $args = array(
            0,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function setFailedLogins($amount) {
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

    public function getHomepage() {
        return $this->homepage;
    }

    public function setHomepage($val) {
        $this->homepage = strval($val);
    }

    public function getDefaultLanguage() {
        return $this->default_language;
    }

    public function setDefaultLanguage($val) {
        $this->default_language = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getAvatar() {
        return ModuleHelper::getBaseUrl("/admin/gfx/no_avatar.png");
    }

    public function setAvatar() {
        throw new NotImplementedException("Avatar feature is not implemented yet.");
    }

    public function getSecondaryGroups() {
        return $this->secondary_groups;
    }

    public function setSecondaryGroups($val) {
        $this->secondary_groups = $val;
    }

    public function addSecondaryGroup($val) {
        $this->secondary_groups[] = $val;
    }

    public function removeSecondaryGroup($val) {
        $filtered = array();
        foreach ($this->secondary_groups as $group) {
            if ($group->getID() != $val->getID()) {
                $filtered[] = $group;
            }
        }
        return $filtered;
    }

    public function getPermissionChecker() {
        return new PermissionChecker($this->getId());
    }

    public function hasPermission($permission) {
        return $this->getPermissionChecker()->hasPermission($permission);
    }

    private function loadGroups($user_id) {
        $groups = array();
        $sql = "select `group_id` from `{prefix}user_groups` where user_id = ?";
        $args = array(
            $user_id
        );
        $query = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($query)) {
            $groups[] = new Group($row->group_id);
        }
        $this->setSecondaryGroups($groups);
    }

    private function saveGroups() {
        Database::pQuery("delete from {prefix}user_groups where user_id = ?", array(
            $this->getId()
                ), true);
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
