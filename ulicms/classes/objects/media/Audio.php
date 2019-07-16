<?php

declare(strict_types=1);

namespace UliCMS\Models\Media;

use mysqli_result;
use UliCMS\Utils\File;
use UliCMS\Models\Content\Category;
use Database;
use Model;
use Path;
use StringHelper;
use function _esc;
use function get_translation;

class Audio extends Model {

    private $name = null;
    private $mp3_file = null;
    private $ogg_file = null;
    private $category_id = null;
    private $category = null;
    private $created;
    private $updated;

    const AUDIO_DIR = "content/audio/";

    public function __construct($id = null) {
        if (!is_null($id)) {
            $this->loadById($id);
        } else {
            $this->created = time();
            $this->updated = time();
        }
    }

    public function loadById($id) {
        $query = Database::pQuery("select * from `{prefix}audio` where id = ?", array(
                    intval($id)
                        ), true);
        if (!Database::any($query)) {
            $query = null;
        }
        $this->fillVars($query);
    }

    protected function fillVars($result = null) {
        if ($result) {
            $result = Database::fetchSingle($result);
            $this->setID($result->id);
            $this->setName($result->name);
            $this->setMP3File($result->mp3_file);
            $this->setOGGFile($result->ogg_file);
            $this->setCategoryId(
                    $result->category_id ? intval($result->category_id) : null
            );
            $this->created = $result->created ?
                    intval($result->created) : null;
            $this->updated = $result->updated ? intval($result->updated) : null;
        } else {
            $this->setID(null);
            $this->setName(null);
            $this->setMP3File(null);
            $this->setOGGFile(null);
            $this->setCategoryId(null);
            $this->created = null;
            $this->updated = null;
        }
    }

    protected function insert(): void {
        $this->created = time();
        $this->updated = $this->created;
        $args = array(
            $this->name,
            $this->mp3_file,
            $this->ogg_file,
            $this->category_id,
            $this->created,
            $this->updated
        );
        $sql = "insert into `{prefix}audio`
				(name, mp3_file, ogg_file, category_id, created, updated)
				values (?, ?, ?, ?, ?, ?)";
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update(): void {
        $this->updated = time();
        $args = array(
            $this->name,
            $this->mp3_file,
            $this->ogg_file,
            $this->category_id,
            $this->updated,
            $this->getID()
        );
        $sql = "update `{prefix}audio` set
				name = ?, mp3_file = ?, ogg_file = ?, category_id = ?, updated = ?
				where id = ?";
        Database::pQuery($sql, $args, true);
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getMP3File(): ?string {
        return $this->mp3_file;
    }

    public function getOggFile(): ?string {
        return $this->ogg_file;
    }

    public function getCategoryId(): ?int {
        return $this->category_id;
    }

    public function getCategory(): ?Category {
        return $this->category;
    }

    public function getCreated(): ?int {
        return $this->created;
    }

    public function getUpdated(): ?int {
        return $this->updated;
    }

    public function setName(?string $val): void {
        $this->name = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function setMP3File(?string $val): void {
        $this->mp3_file = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function setOGGFile(?string $val): void {
        $this->ogg_file = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function setCategoryId(?int $val): void {
        $this->category_id = is_numeric($val) ? intval($val) : null;
        $this->category = is_numeric($val) ? new Category($val) : null;
    }

    public function setCategory(?Category $val) {
        $this->category = $val instanceof Category ? $val : null;
        $this->category_id = $val instanceof Category ? $val->getID() : null;
    }

    public function delete(bool$deletePhysical = true): void {
        if ($this->getId()) {
            if ($deletePhysical) {
                if ($this->getMP3File()) {
                    $file = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/audio/" . basename($this->getMP3File()));
                    File::deleteIfExists($file);
                }
                if ($this->getOggFile()) {
                    $file = Path::resolve("ULICMS_DATA_STORAGE_ROOT/content/audio/" . basename($this->getOggFile()));
                    File::deleteIfExists($file);
                }
            }
            Database::pQuery("delete from `{prefix}audio` where id = ?", array(
                $this->getID()
                    ), true);
            $this->fillVars(null);
        }
    }

    public function getHtml(): string {
        $audio_dir = self::AUDIO_DIR;
        if (defined("ULICMS_DATA_STORAGE_URL")) {
            $audio_dir = Path::resolve("ULICMS_DATA_STORAGE_URL/$audio_dir") . "/";
        }
        $html = '<audio controls>';
        if (!empty($this->mp3_file)) {
            $html .= '<source src="' . $audio_dir . _esc($this->mp3_file) . '" type="audio/mp3">';
        }
        if (!empty($this->ogg_file)) {
            $html .= '<source src="' . $audio_dir . _esc($this->ogg_file) . '" type="audio/ogg">';
        }
        $html .= get_translation("no_html5");
        if (!empty($this->mp3_file) or ! empty($this->ogg_file)) {
            $preferred = !empty($this->mp3_file) ? $this->mp3_file : $this->ogg_file;
            $html .= '<br/><a href="' . self::AUDIO_DIR . $preferred . '">' . get_translation("DOWNLOAD_AUDIO_INSTEAD") . '</a>';
        }

        $html .= '</audio>';
        return $html;
    }

    public function html(): void {
        echo $this->getHtml();
    }

}
