<?php

declare(strict_types=1);

use UliCMS\Models\Content\Language;

class Group
{
    private $id = null;
    private $name = "";
    private $permissions = [];
    private $languages = [];
    private $allowable_tags = null;

    public function __construct($id = null)
    {
        $acl = new ACL();
        $this->permissions = $acl->getDefaultACLAsJSON(false, true);
        if ($id !== null) {
            $this->loadById(intval($id));
        }
    }

    // get the primary group id of the current user
    public static function getCurrentGroupId(): ?int
    {
        return isset($_SESSION["group_id"]) ?
                intval($_SESSION["group_id"]) : null;
    }

    // get the primary group of the current user
    public static function getCurrentGroup(): ?Group
    {
        if (self::getCurrentGroupId()) {
            return new self(self::getCurrentGroupId());
        }
        return null;
    }

    // Get the id of the default group
    public static function getDefaultPrimaryGroupId(): ?int
    {
        return Settings::get("default_acl_group") ?
                intval(Settings::get("default_acl_group")) : null;
    }

    // get the default group
    public static function getDefaultPrimaryGroup(): ?Group
    {
        if (self::getDefaultPrimaryGroupId()) {
            return new self(self::getDefaultPrimaryGroupId());
        }
        return null;
    }

    public static function getAll(): array
    {
        $datasets = [];
        $sql = "select id from `{prefix}groups` order by id";
        $result = Database::query($sql, true);
        while ($row = Database::fetchobject($result)) {
            $datasets[] = new Group($row->id);
        }
        return $datasets;
    }

    public function loadById(int $id): void
    {
        $sql = "select * from `{prefix}groups` where id = ?";
        $args = array(
            intval($id)
        );
        $result = Database::pQuery($sql, $args, true);
        if (Database::any($result)) {
            $dataset = Database::fetchObject($result);
            $this->id = intval($dataset->id);
            $this->name = $dataset->name;
            $this->permissions = json_decode($dataset->permissions, true);
            $this->allowable_tags = $dataset->allowable_tags;
            $acl = new ACL();
            $allPermissions = $acl->getDefaultACLAsJSON(false, true);
            foreach ($allPermissions as $name => $value) {
                if (!isset($this->permissions[$name])) {
                    $this->addPermission($name, $value);
                }
            }
        }
        $this->languages = [];
        $sql = "select `language_id` from `{prefix}group_languages` "
                . "where `group_id` = ?";
        $args = array(
            $this->getId()
        );
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchobject($result)) {
            $lang = new Language();
            $lang->loadById($row->language_id);
            if (!is_null($lang->getID())) {
                $this->languages[] = $lang;
            }
        }
    }

    public function save(): void
    {
        if ($this->id) {
            $this->update();
            return;
        }
        $this->insert();
    }

    protected function saveLanguages(): void
    {
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

    protected function insert(): void
    {
        $sql = "insert into `{prefix}groups` "
                . "(name, permissions, allowable_tags) values (?,?,?)";
        $args = array(
            $this->getName(),
            json_encode(
                $this->getPermissions(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ),
            $this->getAllowableTags()
        );
        $result = Database::pQuery($sql, $args, true);
        if ($result) {
            $id = Database::getInsertID();
            $this->id = $id;
            $this->saveLanguages();
        }
    }

    protected function update(): void
    {
        $sql = "update `{prefix}groups`set name = ?, permissions = ?, "
                . "allowable_tags = ? where id = ?";
        $args = array(
            $this->getName(),
            json_encode($this->getPermissions()),
            $this->getAllowableTags(),
            $this->id
        );
        Database::pQuery($sql, $args, true);
        $this->saveLanguages();
    }

    public function delete(): void
    {
        if (is_null($this->id)) {
            return;
        }
        $sql = "delete from `{prefix}groups` where id = ?";
        $args = array(
            $this->id
        );
        $result = Database::pQuery($sql, $args, true);
        if ($result) {
            $this->id = null;
        }
    }

    public function getId(): ?int
    {
        return !is_null($this->id) ? intval($this->id) : null;
    }

    public function setId(?int $id): void
    {
        $this->id = $id !== null ? $id : null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = !is_null($name) ? strval($name) : null;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function addPermission(string $name, bool $value = false): void
    {
        $this->permissions[$name] = $value;
    }

    public function hasPermission(string $name): bool
    {
        return (
                isset($this->permissions[$name]) and
                $this->permissions[$name]
                );
    }

    public function removePermission(string $name): void
    {
        if (isset($this->permissions[$name])) {
            unset($this->permissions[$name]);
        }
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function setLanguages(array $val): void
    {
        $this->languages = $val;
    }

    public function getAllowableTags(): ?string
    {
        return $this->allowable_tags;
    }

    public function setAllowableTags(?string $val): void
    {
        $this->allowable_tags = Stringhelper::isNotNullOrWhitespace($val) ?
                strval($val) : null;
    }

    // get all users in this group
    public function getUsers(string $order = "id"): array
    {
        $manager = new UserManager();
        return $manager->getUsersByGroupId($this->getId(), $order);
    }
}
