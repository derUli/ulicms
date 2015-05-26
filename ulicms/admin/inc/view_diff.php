<?php
include_once ULICMS_ROOT."/classes/finediff.php";
include_once ULICMS_ROOT."/classes/vcs.php";
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ("pages")){
     $history_id = intval($_GET["history_id"]);
     $content_id = intval($_GET["content_id"]);
     
     $current_version = getPageByID($content_id);
     $old_version = VCS::getRevisionByID($history_id);
     
     $from_text = $current_version->content;
     $to_text = $old_version->content;     
     
     $current_version_date = date("Y-m-d H:i:s", $current_version->lastmodified);
     $old_version_date = $old_version->date;
     
     
    $from_text = mb_convert_encoding($from_text, 'HTML-ENTITIES', 'UTF-8');
    $to_text = mb_convert_encoding($to_text, 'HTML-ENTITIES', 'UTF-8');
    $opcodes = FineDiff::getDiffOpcodes($from_text, $to_text, FineDiff::$wordGranularity);
    
     $html = FineDiff::renderDiffToHTMLFromOpcodes($from_text, $opcodes);

  ?>
<h1><?php translate("diff");?></h1>
<p><?php translate("COMPARE_VERSION_FROM_TO", array("%current%" => $current_version_date, "%old_version%" => $old_version_date));?></p>

<div class="diff">
<?php echo nl2br($html);?>
</div>

<p><a href="index.php?action?pages&do_restore_version=<?php echo $history_id;?>" onclick="return confirm('<?php translate("ask_for_restore");?>');"><?php translate("restore");?></a></p>
     <?php
    } else {
      noperms();    
    }
    
    }
?>