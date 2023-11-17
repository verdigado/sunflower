jQuery( document ).ready(function() {
	jQuery.datetimepicker.setLocale('de');

	jQuery('input.datetimepicker[name="_sunflower_event_from"]').datetimepicker({
		timepicker: true,
		dayOfWeekStart: 1,
		format:'d.m.Y H:i',
	});

    jQuery('input.datetimepicker[name="_sunflower_event_until"]').datetimepicker({
		timepicker: true,
		dayOfWeekStart: 1,
		format:'d.m.Y H:i',
        onShow:function( ct ){
            this.setOptions({
                value:jQuery('input.datetimepicker[name="_sunflower_event_until"]').val()?jQuery('input.datetimepicker[name="_sunflower_event_until"]').val():jQuery('input.datetimepicker[name="_sunflower_event_from"]').val()
            });
        },
    });

});
