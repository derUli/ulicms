<form action="index.php" method="get">
    <input type="hidden" name="action" value="module_settings"> <input
        type="hidden" name="module" value="fortune2">
    <button type="submit" class="btn btn-default">Reset</button>
</form>
<br />

<form action="index.php" method="get">
    <input type="hidden" name="action" value="module_settings"> <input
        type="hidden" name="module" value="fortune2"> <input type="hidden"
        name="sClass" value="Fortune"> <input type="hidden" name="sMethod"
        value="doSomething">
    <button type="submit" class="btn btn-default">GET</button>
</form>
<br />
<form
    action="<?php Template::escape(ModuleHelper::buildAdminURL("fortune2", "sClass=Fortune&sMethod=doSomething")); ?>"
    method="post">
        <?php csrf_token_html(); ?>
    <button type="submit" class="btn btn-default">POST</button>
</form>
<br />
<code><?php if (ViewBag::get("sample_text")) { ?>
        <?php Template::escape(ViewBag::get("sample_text")); ?>
    <?php } ?></code>
