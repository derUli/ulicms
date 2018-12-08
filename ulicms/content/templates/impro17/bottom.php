<?php Template::comments();?>
<div class="advertisement">
<?php random_banner();?>
</div>
</main>
</div>
<footer class="footer">
	<div class="row">
		<div class="col-sm-6">
			<p>&copy;
    <?php if(date("Y") > 2016){?>
    2016 -
    <?php
    }
    ?>
    <?php year();?> by <?php homepage_owner();?>
	</p>
		</div>
		<div class="col-sm-6">
			<!--
			<p class="imprint-right">

				<a href="#"><?php translate("imprint");?></a>
			</p>
			-->
		</div>
	</div>
</footer>
<?php Template::footer();?>
</div>
<?php
$translation = new JSTranslation();
$translation->addKey("menu");
$translation->renderJS();
?>
<script type="text/javascript"
	src="<?php echo getTemplateDirPath(get_theme());?>main.js"></script>
</body>
</html>
<!--  <?php echo date("r"); ?>-->
