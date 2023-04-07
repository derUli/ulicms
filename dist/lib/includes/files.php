<?php

declare(strict_types=1);

use App\Utils\File;
use Nette\Utils\FileSystem;

/**
 * Deletes a directory including its content
 * @param string $dir
 * @param bool $deleteMe
 * @return void
 */
function sureRemoveDir(string $dir, bool $deleteMe = true): void
{
    File::sureRemoveDir($dir, $deleteMe);
}

/**
 * Recursively copy directory
 * @param string $src
 * @param string $dst
 * @return void
 */
function recurse_copy(string $src, string $dst): void
{
    FileSystem::copy($src, $dst);
}
