<?php

declare(strict_types=1);

namespace UliCMS\Security;

// permission checker interface for objects with read, write and delete
// permission
interface IDatasetPermissionChecker
{
    public function __construct(int $user_id);

    public function canRead(int $dataset): bool;

    public function canWrite(int $dataset): bool;

    public function canDelete($dataset): bool;
}
