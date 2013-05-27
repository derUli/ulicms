<?php if(containsModule(get_requested_pagename(), "fullcalendar")){
$scriptPath = getModulePath("fullcalendar")."script/";
?>
<link href='<?php echo $scriptPath; ?>fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo $scriptPath; ?>fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo $scriptPath; ?>jquery/jquery-1.9.1.min.js'></script>
<script src='<?php echo $scriptPath; ?>jquery/jquery-ui-1.10.2.custom.min.js'></script>
<script src='<?php echo $scriptPath; ?>fullcalendar/fullcalendar.min.js'></script>
<script type="text/javascript">

	$(document).ready(function() {
	
		$('#fullcalendar').fullCalendar({
		
			editable: true,
			
			events: "<?php echo $scriptPath;?>/demos/json-events.php",
			
			eventDrop: function(event, delta) {
				alert(event.title + ' was moved ' + delta + ' days\n' +
					'(should probably update your database)');
			},
			
			loading: function(bool) {
				if (bool) $('#loading').show();
				else $('#loading').hide();
			}
			
		});
		
	});

</script>
<style>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}
		
	#loading {
		position: absolute;
		top: 5px;
		right: 5px;
		}

	#calendar {
		width: 900px;
		margin: 0 auto;
		}

</style>

<?php }?>