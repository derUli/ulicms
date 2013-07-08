<?php if(containsModule(get_requested_pagename(), "fullcalendar")){
$scriptPath = getModulePath("fullcalendar")."script/";
?>
<link href='<?php echo $scriptPath; ?>fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo $scriptPath; ?>fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo $scriptPath; ?>jquery/jquery-1.9.1.min.js'></script>
<script src='<?php echo $scriptPath; ?>jquery/jquery-ui-1.10.2.custom.min.js'></script>
<script src='<?php echo $scriptPath; ?>fullcalendar/fullcalendar.js'></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#fullcalendar').fullCalendar({
			editable: true,
                        <?php if($_SESSION["language"] === "de"){?>
                        buttonText: {
                        today: 'Heute',
                        month: 'Monat',
                        day: 'Tag',
                        week: 'Woche'
                        },
                        monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
                        monthNamesShort: ['Januar','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sept','Okt','Nov','Dez'],
                        dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
                        dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],


<?php }?>
			events: "json-events.php",
                        disableDragging: true,
			loading: function(bool) {
				if (bool) $('#loading').show();
				else $('#loading').hide();
			}
			
		});
		
	});

</script>
<style>

		

	#fullcalendar {
		width: 97%;
		margin: 0 auto;
		}

</style>

<?php }?>