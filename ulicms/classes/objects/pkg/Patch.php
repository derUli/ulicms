<?php

declare(strict_types=1);

namespace UliCMS\Packages;

use UliCMS\Packages\PatchManager;

class Patch {

    public $name;
    public $description;
    public $url;
    public $hash;

    public function __construct(
            string $name,
            string $description,
            string $url,
            ?string $hash = null) {
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->hash = $hash;
    }

    public static function fromLine(string $line): Patch {
        $splittedLine = explode("|", $line);
        return new self(
                $splittedLine[0],
                $splittedLine[1],
                $splittedLine[2],
                $splittedLine[3]
        );
    }

    public function install(): bool {

        $patchManager = new PatchManager();
        return $patchManager->installPatch(
                        $this->name,
                        $this->description,
                        $this->url,
                        false,
                        $this->hash);
    }

    public function toLine(): string {
        $columns = [
            $this->name,
            $this->description,
            $this->url,
            $this->hash
        ];
        return implode("|", $columns);
    }

}
