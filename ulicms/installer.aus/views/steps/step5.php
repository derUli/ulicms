<form role="form" method="post" action="index.php?submit_form=Demodata">

	<div class="form-group">
		<div class="checkbox">
			<label><input type="checkbox" value="yes" name="install_demodata"
				id="install_demodata"
				<?php
				if (! empty ( $_SESSION ["install_demodata"] )) {
					echo "checked";
				}
				?>><?php echo TRANSLATION_INSTALL_DEMO_DATA;?></label>
		</div>


		<div class="checkbox">
			<label><input type="checkbox" value="yes" name="add_fk" id="add_fk"
				<?php
				if (! empty ( $_SESSION ["add_fk"] )) {
					echo "checked";
				}
				?>><?php echo TRANSLATION_ADD_FK;?></label>
		</div>



		<div class="checkbox">
			<label><input type="checkbox" value="true" name="fast_mode"
				id="fast_mode"
				<?php
				if ($_SESSION ["fast_mode"]) {
					echo "checked";
				}
				?>><?php echo TRANSLATION_ENABLE_FAST_MODE;?></label>
		</div>
	</div>
	<button type="submit" class="btn btn-default"><?php echo TRANSLATION_APPLY;?></button>
</form>