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
     
     
    $from_text = mb_convert_encoding($from_text, 'HTML-ENTITIES', 'UTF-8');
    $to_text = mb_convert_encoding($to_text, 'HTML-ENTITIES', 'UTF-8');
    $opcodes = FineDiff::getDiffOpcodes($from_text, $to_text, FineDiff::$wordGranularity);
    
     $html = FineDiff::renderDiffToHTMLFromOpcodes($from_text, $opcodes);

  ?>
<h1><?php translate("diff");?></h1>
<div class="diff">
<?php echo nl2br($html);?>
</div>
     <?php
    } else {
      noperms();    
    }
    
    }
?>