<?php
defined('ULICMS_ROOT') || exit('No direct script access allowed');
?>
<script
    src="<?php
    echo getModulePath('slicknav');
?>dist/jquery.slicknav.min.js?version=<?php echo getModuleMeta('slicknav', 'version'); ?>"></script>
<link type="text/css"
      href="<?php
  echo getModulePath('slicknav');
?>dist/slicknav.min.css?version=<?php echo getModuleMeta('slicknav', 'version'); ?>"
      rel="stylesheet" />
