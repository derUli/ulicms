<?php

declare(strict_types=1);

namespace App\Models\Media;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use function _esc;
use App\Models\Content\Category;
use App\Utils\File;
use Database;
use function get_translation;

use Model;
use Path;

// html5 format support of browser are different
// UliCMS allows *.mp3 and *.ogg file uploads for audio
// *.ogg is used by browsers which are not allowed to include
// a *.mp3 codec due legal reasons
class Audio extends Model
{
    public const AUDIO_DIR = 'content/audio/';

    private $name = null;

    private $mp3_file = null;

    private $ogg_file = null;

    private $category_id = null;

    private $category = null;

    private $created;

    private $updated;

    public function __construct($id = null)
    {
        if ($id !== null) {
            $this->loadById($id);
        } else {
            $this->created = time();
            $this->updated = time();
        }
    }

    public static function getAll(string $order = 'id'): array
    {
        $datasets = [];
        $sql = "SELECT id FROM {prefix}audio ORDER BY $order";
        $result = Database::query($sql, true);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = new self((int)$row->id);
        }
        return $datasets;
    }

    public function loadById($id)
    {
        $result = Database::pQuery('select * from `{prefix}audio` '
                        . 'where id = ?', [
                            (int)$id
                        ], true);
        if (! Database::any($result)) {
            $result = null;
        }
        $this->fillVars($result);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMP3File(): ?string
    {
        return $this->mp3_file;
    }

    public function getOggFile(): ?string
    {
        return $this->ogg_file;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function getUpdated(): ?int
    {
        return $this->updated;
    }

    public function setName(?string $val): void
    {
        $this->name = ! empty($val) ?
                (string)$val : null;
    }

    public function setMP3File(?string $val): void
    {
        $this->mp3_file = ! empty($val) ?
                (string)$val : null;
    }

    public function setOGGFile(?string $val): void
    {
        $this->ogg_file = ! empty($val) ?
                (string)$val : null;
    }

    public function setCategoryId(?int $val): void
    {
        $this->category_id = is_numeric($val) ? (int)$val : null;
        $this->category = is_numeric($val) ? new Category($val) : null;
    }

    public function setCategory(?Category $val)
    {
        $this->category = $val instanceof Category ? $val : null;
        $this->category_id = $val instanceof Category ? $val->getID() : null;
    }

    public function delete(bool$deletePhysical = true): void
    {
        if ($this->getId()) {
            if ($deletePhysical) {
                if ($this->getMP3File()) {
                    $file = Path::resolve(
                        'ULICMS_ROOT/content/audio/' .
                                    basename($this->getMP3File())
                    );
                    File::deleteIfExists($file);
                }
                if ($this->getOggFile()) {
                    $file = Path::resolve(
                        'ULICMS_ROOT/content/audio/' .
                                    basename($this->getOggFile())
                    );
                    File::deleteIfExists($file);
                }
            }
            Database::pQuery('delete from `{prefix}audio` where id = ?', [
                $this->getID()
            ], true);
            $this->fillVars(null);
        }
    }

    // render HTML5 <audio> tag
    public function render(): string
    {
        $audioDir = $this->getAudioDir();

        $html = '<audio controls>';

        if (! empty($this->mp3_file)) {
            $html .= '<source src="' . $audioDir . _esc($this->mp3_file) .
                    '" type="audio/mp3">';
        }

        if (! empty($this->ogg_file)) {
            $html .= '<source src="' . $audioDir . _esc($this->ogg_file) .
                    '" type="audio/ogg">';
        }

        $html .= get_translation('no_html5');
        if (! empty($this->mp3_file) || ! empty($this->ogg_file)) {
            $preferred = ! empty($this->mp3_file) ?
                    $this->mp3_file : $this->ogg_file;
            $html .= '<br/><a href="' . self::AUDIO_DIR . $preferred . '">' .
                    get_translation('DOWNLOAD_AUDIO_INSTEAD') . '</a>';
        }

        $html .= '</audio>';
        return $html;
    }

    protected function fillVars($result = null)
    {
        if ($result) {
            $result = Database::fetchSingle($result);
            $this->setID($result->id);
            $this->setName($result->name);
            $this->setMP3File($result->mp3_file);
            $this->setOGGFile($result->ogg_file);
            $this->setCategoryId(
                $result->category_id ? (int)$result->category_id : null
            );
            $this->created = $result->created ?
                    (int)$result->created : null;
            $this->updated = $result->updated ? (int)$result->updated : null;
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

    protected function insert(): void
    {
        $this->created = time();
        $this->updated = $this->created;
        $args = [
            $this->name,
            $this->mp3_file,
            $this->ogg_file,
            $this->category_id,
            $this->created,
            $this->updated
        ];
        $sql = 'insert into `{prefix}audio`
				(name, mp3_file, ogg_file, category_id,
                                created, updated)
				values (?, ?, ?, ?, ?, ?)';
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update(): void
    {
        $this->updated = time();
        $args = [
            $this->name,
            $this->mp3_file,
            $this->ogg_file,
            $this->category_id,
            $this->updated,
            $this->getID()
        ];
        $sql = 'update `{prefix}audio` set
				name = ?, mp3_file = ?, ogg_file = ?,
                                category_id = ?, updated = ?
				where id = ?';
        Database::pQuery($sql, $args, true);
    }

    protected function getAudioDir(): string
    {
        return self::AUDIO_DIR;
    }
}
