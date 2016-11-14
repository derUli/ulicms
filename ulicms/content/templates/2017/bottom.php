</main>
</div>
<footer class="footer">
	<p>&copy; <?php year();?> by <?php homepage_owner();?>
	</p>
</footer>
<?php Template::footer();?>
</div>
<?php
$translation = new JSTranslation ();
$translation->addKey ( "menu" );
$translation->renderJS ();
?>
<script type="text/javascript"
	src="<?php echo getTemplateDirPath(get_theme());?>main.js"></script>

</body>
</html>