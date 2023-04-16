<?php

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Available HTML Editors
 */
class HtmlEditor
{
    // See https://ckeditor.com
    public const CKEDITOR = 'ckeditor';

    // See https://codemirror.net
    public const CODEMIRROR = 'codemirror';
}
