</section>

</div>
</div>
</div>
</div>
<div id="footer-wrapper">
	<div class="container">
		<div class="row">
			<div class="8u 12u(mobile)">

				<section>
					<h2>How about a truckload of links?</h2>
					<div>
						<div class="row">
							<div class="3u 12u(mobile)">
								<div class="link-list">
											<?php menu("link-list-1");?>
											</div>
								<div class="3u 12u(mobile)">
									<div class="link-list">
											<?php menu("link-list-2");?>	
											</div>
								</div>
								<div class="3u 12u(mobile)">
									<div class="link-list">
											<?php menu("link-list-3");?>
												</div>
								</div>
								<div class="3u 12u(mobile)">
									<div class="link-list">
											<?php menu("link-list-4");?>
												</div>
								</div>
							</div>
						</div>
				
				</section>

			</div>
			<div class="4u 12u(mobile)">
				<section>
					<h2>Something of interest</h2>
					<p><?php motto();?></p>
					<footer class="controls">
						<a href="#" class="button">Oh, please continue ....</a>
					</footer>
				</section>

			</div>
		</div>
		<div class="row">
			<div class="12u">

				<div id="copyright">
								&copy; <?php homepage_owner();?>. All rights reserved. | Design: <a
						href="http://html5up.net">HTML5 UP</a>
				</div>

			</div>
		</div>
	</div>
</div>
</div>

<!-- Scripts -->
<script
	src="<?php echo getTemplateDirPath("minimaxing");?>assets/js/jquery.min.js"></script>
<script
	src="<?php echo getTemplateDirPath("minimaxing");?>assets/js/skel.min.js"></script>
<script
	src="<?php echo getTemplateDirPath("minimaxing");?>assets/js/skel-viewport.min.js"></script>
<script
	src="<?php echo getTemplateDirPath("minimaxing");?>assets/js/util.js"></script>
<!--[if lte IE 8]><script src="<?php echo getTemplateDirPath("minimaxing");?>assets/js/ie/respond.min.js"></script><![endif]-->
<script
	src="<?php echo getTemplateDirPath("minimaxing");?>assets/js/main.js"></script>
<?php Template::footer();?>
</body>
</html>