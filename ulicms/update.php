<?php

// Move modules folder to content/
@rename("modules", "content/modules");

// @unlink("update.php");
ulicms_redirect("admin/");