<?php

declare(strict_types=1);

namespace App\Packages;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use App\Packages\PatchManager;

/**
 * Installable Patch
 */
class Patch
{
    public string $name;
    public string $description;
    public string $url;
    public string $hash;

    /**
     * Constructor
     * @param string $name
     * @param string $description
     * @param string $url
     * @param string|null $hash
     */
    public function __construct(
        string $name,
        string $description,
        string $url,
        ?string $hash = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->hash = $hash;
    }

    /**
     * Patch list file entry to model
     * @param string $line
     * @return Patch
     */
    public static function fromLine(string $line): Patch
    {
        $splittedLine = explode("|", $line);
        return new self(
            $splittedLine[0],
            $splittedLine[1],
            $splittedLine[2],
            $splittedLine[3]
        );
    }

    /**
     * Install patch
     * @return bool
     */
    public function install(): bool
    {
        $patchManager = new PatchManager();
        return $patchManager->installPatch(
            $this->name,
            $this->description,
            $this->url,
            false,
            $this->hash
        );
    }

    /**
     * Model to patch list file entry
     * @return string
     */
    public function toLine(): string
    {
        $columns = [
            $this->name,
            $this->description,
            $this->url,
            $this->hash
        ];
        return implode("|", $columns);
    }
}
