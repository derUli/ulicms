<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

// Page that has assigned a module
class Module_Page extends Page
{
    public $type = "module";
    public $module = null;
    public $text_position = 'after';

    protected function fillVars($result = null)
    {
        parent::fillVars($result);
        $this->module = $result->module;
        $this->text_position = $result->text_position;
    }

    public function save()
    {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
            $this->update();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    public function update()
    {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = "update {prefix}content set module = ?, text_position = ? "
                . "where id = ?";
        $args = array(
            $this->module,
            $this->text_position,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function containsModule(?string $module = null): bool
    {
        $retval = false;

        if (parent::containsModule($module)) {
            $retval = true;
        }
        if ($this->module !== null && !empty($this->module)) {
            if (($module && $this->module == $module) || !$module) {
                $retval = true;
            }
        }
        return $retval;
    }

    public function getEmbeddedModules(): array
    {
        $result = parent::getEmbeddedModules();
        if (!empty($this->module) && !in_array($this->module, $result)) {
            $result[] = $this->module;
        }
        return $result;
    }

     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return "fas fa-puzzle-piece";
    }
}
