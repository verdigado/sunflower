jQuery( document ).ready(function() {
	jQuery.datetimepicker.setLocale('de');

	jQuery('.datetimepicker').datetimepicker({
		i18n:{
		de:{
			months:[
				'Januar','Februar','März','April',
				'Mai','Juni','Juli','August',
				'September','Oktober','November','Dezember',
			],
			dayOfWeek:[
				"So", "Mo", "Di", "Mi", 
				"Do", "Fr", "Sa.",
			]
		}
		},
		timepicker: true,
		dayOfWeekStart: 1,
		format:'d.m.Y H:i'
	});
});

