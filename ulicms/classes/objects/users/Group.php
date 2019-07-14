<?php

declare(strict_types=1);

use UliCMS\Models\Content\Language;

class Group {

    private $id = null;
    private $name = "";
    private $permissions = [];
    private $languages = [];
    private $allowable_tags = null;

    public function __construct($id = null) {
        $acl = new ACL();
        $this->permissions = $acl->getDefaultACLAsJSON(false, true);
        if (!is_null($id)) {
            $this->loadById(intval($id));
        }
    }

    public static function getCurrentGroupId(): ?int {
        return isset($_SESSION["group_id"]) ? intval($_SESSION["group_id"]) : null;
    }

    public static function getCurrentGroup(): ?Group {
        if (self::getCurrentGroupId()) {
            return new self(self::getCurrentGroupId());
        }
        return null;
    }

    public static function getDefaultPrimaryGroupId(): ?int {
        return Settings::get("default_acl_group") ? intval(Settings::get("default_acl_group")) : null;
    }

    public static function getDefaultPrimaryGroup(): ?Group {
        if (self::getDefaultPrimaryGroupId()) {
            return new self(self::getDefaultPrimaryGroupId());
        }
        return null;
    }

    public static function getAll(): array {
        $data = [];
        $sql = "select id from `{prefix}groups` order by id";
        $query = Database::query($sql, true);
        while ($row = Database::fetchobject($query)) {
            $data[] = new Group($row->id);
        }
        return $data;
    }

    public function loadById(int $id): void {
        $sql = "select * from `{prefix}groups` where id = ?";
        $args = array(
            intval($id)
        );
        $query = Database::pQuery($sql, $args, true);
        if (Database::any($query)) {
            $result = Database::fetchObject($query);
            $this->id = intval($result->id);
            $this->name = $result->name;
            $this->permissions = json_decode($result->permissions, true);
            $this->allowable_tags = $result->allowable_tags;
            $acl = new ACL();
            $allPermissions = $acl->getDefaultACLAsJSON(false, true);
            foreach ($allPermissions as $name => $value) {
                if (!isset($this->permissions[$name])) {
                    $this->addPermission($name, $value);
                }
            }
        }
        $this->languages = [];
        $sql = "select `language_id` from `{prefix}group_languages` where `group_id` = ?";
        $args = array(
            $this->getId()
        );
        $query = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchobject($query)) {
            $lang = new Language();
            $lang->loadById($row->language_id);
            if (!is_null($lang->getID())) {
                $this->languages[] = $lang;
            }
        }
    }

    public function save(): void {
        if ($this->id) {
            $this->update();
            return;
        }
        $this->insert();
    }

    protected Function saveLanguages(): void {
        $sql = "delete from `{prefix}group_languages` where `group_id` = ?";
        $args = array(
            $this->getId()
        );
        Database::pQuery($sql, $args, true);
        foreach ($this->languages as $lang) {
            $sql = "insert into `{prefix}group_languages` (`group_id`,
 `language_id`) values(?, ?)";
            $args = array(
                $this->getId(),
                $lang->getID()
            );
            Database::pQuery($sql, $args, true);
        }
    }

    protected function insert(): void {
        $sql = "insert into `{prefix}groups` (name, permissions, allowable_tags) values (?,?,?)";
        $args = array(
            $this->getName(),
            json_encode($this->getPermissions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            $this->getAllowableTags()
        );
        $query = Database::pQuery($sql, $args, true);
        if ($query) {
            $id = Database::getInsertID();
            $this->id = $id;
            $this->saveLanguages();
        }
    }

    protected function update(): void {
        $sql = "update `{prefix}groups` set name = ?, permissions = ?, allowable_tags = ? where id = ?";
        $args = array(
            $this->getName(),
            json_encode($this->getPermissions()),
            $this->getAllowableTags(),
            $this->id
        );
        Database::pQuery($sql, $args, true);
        $this->saveLanguages();
    }

    public function delete(): void {
        if (is_null($this->id)) {
            return;
        }
        $sql = "delete from `{prefix}groups` where id = ?";
        $args = array(
            $this->id
        );
        $query = Database::pQuery($sql, $args, true);
        if ($query) {
            $this->id = null;
        }
    }

    public function getId(): ?int {
        return !is_null($this->id) ? intval($this->id) : null;
    }

    public function setId(?int $id): void {
        $this->id = !is_null($id) ? $id : null;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = !is_null($name) ? strval($name) : null;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void {
        $this->permissions = $permissions;
    }

    public function addPermission(string $name, bool $value = false): void {
        $this->permissions[$name] = $value;
    }

    public function hasPermission(string $name): bool {
        return (in_array($name, $this->permissions) and $this->permissions[$name]);
    }

    public function removePermission(string $name): void {
        if (isset($this->permissions[$name])) {
            unset($this->permissions[$name]);
        }
    }

    public function getLanguages(): array {
        return $this->languages;
    }

    public function setLanguages(array $val): void {
        $this->languages = $val;
    }

    public function getAllowableTags(): ?string {
        return $this->allowable_tags;
    }

    public function setAllowableTags(?string $val): void {
        $this->allowable_tags = Stringhelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getUsers(string $order = "id"): array {
        $manager = new UserManager();
        return $manager->getUsersByGroupId($this->getId(), $order);
    }

}
