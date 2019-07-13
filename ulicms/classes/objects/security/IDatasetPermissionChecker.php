<?php

declare(strict_types=1);

namespace UliCMS\Security;

interface IDatasetPermissionChecker {

    public function __construct(int $user_id);

    public function canRead(int $dataset);

    public function canWrite(int $dataset);

    public function canDelete($dataset);
}
