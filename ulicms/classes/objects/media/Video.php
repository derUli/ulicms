<?php

declare(strict_types=1);

namespace UliCMS\Models\Media;

use UliCMS\Utils\File;
use UliCMS\Models\Content\Category;
use Database;
use Model;
use Path;
use StringHelper;
use function _esc;
use function get_translation;

// html5 format support of browser are different
// UliCMS allows *.mp4, *.ogv and *.webm file uploads for video
class Video extends Model
{
    private $name = null;
    private $mp4_file = null;
    private $ogg_file = null;
    private $webm_file = null;
    private $category_id = null;
    private $category = null;
    private $created;
    private $updated;
    private $width = null;
    private $height = null;

    const VIDEO_DIR = "content/videos/";

    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->loadById($id);
        } else {
            $this->created = time();
            $this->updated = time();
        }
    }

    public static function getAll(string $order = "id"): array
    {
        $datasets = [];
        $sql = "SELECT id FROM {prefix}videos ORDER BY $order";
        $result = Database::query($sql, true);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = new self(intval($row->id));
        }
        return $datasets;
    }

    public function loadById($id): void
    {
        $result = Database::pQuery("select * from `{prefix}videos` "
                        . "where id = ?", array(
                    intval($id)
                        ), true);
        if (!Database::any($result)) {
            $result = null;
        }
        $this->fillVars($result);
    }

    protected function fillVars($result = null): void
    {
        if ($result) {
            $result = Database::fetchSingle($result);
            $this->setID(intval($result->id));
            $this->setName($result->name);
            $this->mp4_file = $result->mp4_file;
            $this->ogg_file = $result->ogg_file;
            $this->webm_file = $result->webm_file;
            $this->setCategoryId($result->category_id ?
                            intval($result->category_id) : null);
            $this->created = intval($result->created);
            $this->updated = intval($result->updated);
            $this->width = intval($result->width);
            $this->height = intval($result->height);
        } else {
            $this->setID(null);
            $this->setName(null);
            $this->mp4_file = null;
            $this->ogg_file = null;
            $this->webm_file = null;
            $this->setCategoryId(null);
            $this->created = null;
            $this->updated = null;
            $this->width = null;
            $this->height = null;
        }
    }

    protected function insert(): void
    {
        $this->created = time();
        $this->updated = $this->created;
        $args = array(
            $this->name,
            $this->mp4_file,
            $this->ogg_file,
            $this->webm_file,
            $this->category_id,
            $this->created,
            $this->updated,
            $this->width,
            $this->height
        );
        $sql = "insert into `{prefix}videos`
				(name, mp4_file, ogg_file, webm_file,
                                category_id, created, updated, width, height)
				values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update(): void
    {
        $this->updated = time();
        $args = array(
            $this->name,
            $this->mp4_file,
            $this->ogg_file,
            $this->webm_file,
            $this->category_id,
            $this->updated,
            $this->width,
            $this->height,
            $this->getID()
        );
        $sql = "update `{prefix}videos` set
				name = ?, mp4_file = ?, ogg_file = ?,
                                webm_file = ?, category_id = ?, updated = ?,
                                width = ?, height = ?
				where id = ?";
        Database::pQuery($sql, $args, true);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMp4File(): ?string
    {
        return $this->mp4_file;
    }

    public function getOggFile(): ?string
    {
        return $this->ogg_file;
    }

    public function getWebmFile(): ?string
    {
        return $this->webm_file;
    }

    public function setMp4File(?string $val): void
    {
        $this->mp4_file = is_string($val) ? $val : null;
    }

    public function setOggFile(?string $val): void
    {
        $this->ogg_file = is_string($val) ? $val : null;
    }

    public function setWebmFile(?string $val): void
    {
        $this->webm_file = is_string($val) ? $val : null;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCreated(): int
    {
        return intval($this->created);
    }

    public function getUpdated(): ?int
    {
        return !is_null($this->updated) ?
                intval($this->updated) : null;
    }

    public function setName(?string $val): void
    {
        $this->name = StringHelper::isNotNullOrWhitespace($val) ?
                strval($val) : null;
    }

    public function setCategoryId(?int $val): void
    {
        $this->category_id = is_numeric($val) ? intval($val) : null;
        $this->category = is_numeric($val) ? new Category($val) : null;
    }

    public function setCategory(?Category $val): void
    {
        $this->category = $val instanceof Category ? $val : null;
        $this->category_id = $val instanceof Category ? $val->getID() : null;
    }

    public function delete(bool $deletePhysical = true): void
    {
        if ($this->getId()) {
            if ($deletePhysical) {
                if ($this->getMp4File()) {
                    $file = Path::resolve(
                        "ULICMS_DATA_STORAGE_ROOT/content/videos/" .
                                    basename($this->getMP4File())
                    );
                    File::deleteIfExists($file);
                }
                if ($this->getOggFile()) {
                    $file = Path::resolve(
                        "ULICMS_DATA_STORAGE_ROOT/content/videos/" .
                                    basename($this->getOggFile())
                    );
                    File::deleteIfExists($file);
                }
                if ($this->getWebmFile()) {
                    $file = Path::resolve(
                        "ULICMS_DATA_STORAGE_ROOT/content/videos/" .
                                    basename($this->getWebmFile())
                    );
                    File::deleteIfExists($file);
                }
            }
            Database::pQuery(
                "delete from `{prefix}videos` where id = ?",
                [
                        $this->getID()
                    ],
                true
            );
            $this->fillVars(null);
        }
    }

    protected function getVideoDir(): string
    {
        $videoDir = self::VIDEO_DIR;

        $storageUrl = defined("ULICMS_DATA_STORAGE_URL") ?
                Path::resolve("ULICMS_DATA_STORAGE_URL/$videoDir") . "/" : null;

        return defined("ULICMS_DATA_STORAGE_URL") ? $storageUrl : $videoDir;
    }

    // render HTML5 <video> tag
    public function render(): string
    {
        $videoDir = $this->getVideoDir();

        $html = '<video width="' . $this->width . '" height="' .
                $this->height . '" controls>';

        if (!empty($this->mp4_file)) {
            $html .= '<source src="' . $videoDir . _esc($this->mp4_file) . '" type="video/mp4">';
        }

        if (!empty($this->ogg_file)) {
            $html .= '<source src="' . $videoDir . _esc($this->ogg_file) .
                    '" type="video/ogg">';
        }

        if (!empty($this->webm_file)) {
            $html .= '<source src="' . $videoDir . _esc($this->webm_file) .
                    '" type="video/webm">';
        }

        $html .= get_translation("no_html5");
        if (!empty($this->mp4_file) || !empty($this->ogg_file) || !empty($this->webm_file)) {
            $preferred = (!empty($this->mp4_file) ?
                    $this->mp4_file : (!empty($this->ogg_file) ?
                    $this->ogg_file : $this->webm_file));
            $html .= '<br/><a href="' . $videoDir . $preferred . '">' .
                    get_translation("DOWNLOAD_VIDEO_INSTEAD") . '</a>';
        }
        $html .= "</video>";
        return $html;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setWidth(?int $val): void
    {
        $this->width = $val;
    }

    public function setHeight(?int $val): void
    {
        $this->height = $val;
    }
}
