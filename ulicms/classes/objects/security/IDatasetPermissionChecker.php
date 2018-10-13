<?php

interface IDatasetPermissionChecker
{

    public function __construct($user_id);

    public function canRead($dataset);

    public function canWrite($dataset);

    public function canDelete($dataset);
}