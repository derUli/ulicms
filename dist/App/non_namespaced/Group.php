<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Models\Content\Language;

class Group
{
    private $id = null;

    private $name = '';

    private $permissions = [];

    private $languages = [];

    private $allowable_tags = null;

    /**
     * Constructor
     * @param type $id
     */
    public function __construct($id = null)
    {
        $acl = new ACL();
        $this->permissions = $acl->getDefaultACLAsJSON(false, true);
        if ($id !== null) {
            $this->loadById((int)$id);
        }
    }

    public function loadById(int $id): void
    {
        $sql = 'select * from `{prefix}groups` where id = ?';
        $args = [
            (int)$id
        ];
        $result = Database::pQuery($sql, $args, true);
        if (Database::any($result)) {
            $dataset = Database::fetchObject($result);
            $this->id = (int)$dataset->id;
            $this->name = $dataset->name;
            $this->permissions = json_decode($dataset->permissions, true);
            $this->allowable_tags = $dataset->allowable_tags;
            $acl = new ACL();
            $allPermissions = $acl->getDefaultACLAsJSON(false, true);
            foreach ($allPermissions as $name => $value) {
                if (! isset($this->permissions[$name])) {
                    $this->addPermission($name, $value);
                }
            }
        }
        $this->languages = [];
        $sql = 'select `language_id` from `{prefix}group_languages` '
                . 'where `group_id` = ?';
        $args = [
            $this->getId()
        ];

        $result = Database::pQuery($sql, $args, true);

        while ($row = Database::fetchobject($result)) {
            $lang = new Language();
            $lang->loadById($row->language_id);
            if ($lang->getID() !== null) {
                $this->languages[] = $lang;
            }
        }
    }

   /**
    * Get the primary group id of the current user
    * @return int|null
    */
    public static function getCurrentGroupId(): ?int
    {
        return $_SESSION['group_id'] ?? null;
    }

    /**
     * Get the primary group of the current user
     * @return Group|null
     */
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
        return Settings::get('default_acl_group') ?
                (int)Settings::get('default_acl_group') : null;
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
        $sql = 'select id from `{prefix}groups` order by id';
        $result = Database::query($sql, true);
        while ($row = Database::fetchobject($result)) {
            $datasets[] = new Group($row->id);
        }
        return $datasets;
    }

    public function save(): void
    {
        if ($this->id) {
            $this->update();
            return;
        }
        $this->insert();
    }

    public function delete(): void
    {
        if ($this->id === null) {
            return;
        }
        $sql = 'delete from `{prefix}groups` where id = ?';
        $args = [
            $this->id
        ];
        $result = Database::pQuery($sql, $args, true);
        if ($result) {
            $this->id = null;
        }
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set id
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get permissions
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Set permissions
     * @param array $permissions
     * @return void
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * Add permission
     * @param string $name
     * @param bool $value
     * @return void
     */
    public function addPermission(string $name, bool $value = false): void
    {
        $this->permissions[$name] = $value;
    }

    /**
     * Has permission
     * @param string $name
     * @return bool
     */
    public function hasPermission(string $name): bool
    {
        return
            isset($this->permissions[$name]) &&
            $this->permissions[$name];
    }

    /**
     * Remove permission
     * @param string $name
     * @return void
     */
    public function removePermission(string $name): void
    {
        if (isset($this->permissions[$name])) {
            unset($this->permissions[$name]);
        }
    }

    /**
     * Get languages
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * Set languages
     * @param array $val
     * @return void
     */
    public function setLanguages(array $val): void
    {
        $this->languages = $val;
    }

    /**
     * Get allowed HTML tags
     * @return string|null
     */
    public function getAllowableTags(): ?string
    {
        return $this->allowable_tags;
    }

    /**
     * Set allowed HTML tags
     * @param string|null $val
     * @return void
     */
    public function setAllowableTags(?string $val): void
    {
        $this->allowable_tags = ! empty($val) ?
                (string)$val : null;
    }

    /**
     * Get all users in this group
     * @param string $order
     * @return array
     */
    public function getUsers(string $order = 'id'): array
    {
        $manager = new UserManager();
        return $manager->getUsersByGroupId($this->getId(), $order);
    }

    protected function saveLanguages(): void
    {
        $sql = 'delete from `{prefix}group_languages` where `group_id` = ?';
        $args = [
            $this->getId()
        ];

        Database::pQuery($sql, $args, true);
        foreach ($this->languages as $lang) {
            $sql = 'insert into `{prefix}group_languages` (`group_id`,
 `language_id`) values(?, ?)';
            $args = [
                $this->getId(),
                $lang->getID()
            ];
            Database::pQuery($sql, $args, true);
        }
    }

    protected function insert(): void
    {
        $sql = 'insert into `{prefix}groups` '
                . '(name, permissions, allowable_tags) values (?,?,?)';
        $args = [
            $this->getName(),
            json_encode(
                $this->getPermissions(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ),
            $this->getAllowableTags()
        ];
        $result = Database::pQuery($sql, $args, true);
        if ($result) {
            $id = Database::getInsertID();
            $this->id = $id;
            $this->saveLanguages();
        }
    }

    protected function update(): void
    {
        $sql = 'update `{prefix}groups`set name = ?, permissions = ?, '
                . 'allowable_tags = ? where id = ?';
        $args = [
            $this->getName(),
            json_encode($this->getPermissions()),
            $this->getAllowableTags(),
            $this->id
        ];
        Database::pQuery($sql, $args, true);
        $this->saveLanguages();
    }
}
