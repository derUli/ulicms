<?php

declare(strict_types=1);

namespace UliCMS\Constants;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

// Targets for HTML Links
class LinkTarget {

    const TARGET_BLANK = "_blank";
    const TARGET_SELF = "_self";
    const TARGET_PARENT = "_parent";
    const TARGET_TOP = "_top";

}
