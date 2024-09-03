/* eslint-disable no-undef */
jQuery( function ( $ ) {
	$.datetimepicker.setLocale( 'de' );

	const isWholeDay = $( 'input[name="_sunflower_event_whole_day"]' ).is(
		':checked'
	);

	if ( isWholeDay === true ) {
		$(
			'input.datetimepicker[name="_sunflower_event_from"]'
		).datetimepicker( {
			timepicker: false,
			dayOfWeekStart: 1,
			format: 'd.m.Y',
		} );
		$(
			'input.datetimepicker[name="_sunflower_event_until"]'
		).datetimepicker( {
			timepicker: false,
			dayOfWeekStart: 1,
			format: 'd.m.Y',
			onShow() {
				this.setOptions( {
					value: $(
						'input.datetimepicker[name="_sunflower_event_until"]'
					).val()
						? $(
								'input.datetimepicker[name="_sunflower_event_until"]'
						  ).val()
						: $(
								'input.datetimepicker[name="_sunflower_event_from"]'
						  ).val(),
				} );
			},
		} );
	} else {
		$(
			'input.datetimepicker[name="_sunflower_event_from"]'
		).datetimepicker( {
			timepicker: true,
			dayOfWeekStart: 1,
			format: 'd.m.Y H:i',
		} );
		$(
			'input.datetimepicker[name="_sunflower_event_until"]'
		).datetimepicker( {
			timepicker: true,
			dayOfWeekStart: 1,
			format: 'd.m.Y H:i',
			onShow() {
				this.setOptions( {
					value: $(
						'input.datetimepicker[name="_sunflower_event_until"]'
					).val()
						? $(
								'input.datetimepicker[name="_sunflower_event_until"]'
						  ).val()
						: $(
								'input.datetimepicker[name="_sunflower_event_from"]'
						  ).val(),
				} );
			},
		} );
	}

	$( 'input[name="_sunflower_event_whole_day"]' ).change( function () {
		if ( this.checked ) {
			$(
				'input.datetimepicker[name="_sunflower_event_from"]'
			).datetimepicker( {
				timepicker: false,
				dayOfWeekStart: 1,
				format: 'd.m.Y',
			} );
			$(
				'input.datetimepicker[name="_sunflower_event_until"]'
			).datetimepicker( {
				timepicker: false,
				dayOfWeekStart: 1,
				format: 'd.m.Y',
			} );

			const from = $(
				'input.datetimepicker[name="_sunflower_event_from"]'
			)
				.val()
				.substring( 0, 10 );
			const until = $(
				'input.datetimepicker[name="_sunflower_event_until"]'
			)
				.val()
				.substring( 0, 10 );

			$( 'input.datetimepicker[name="_sunflower_event_from"]' ).val(
				from
			);
			$( 'input.datetimepicker[name="_sunflower_event_until"]' ).val(
				until
			);
		} else {
			$(
				'input.datetimepicker[name="_sunflower_event_from"]'
			).datetimepicker( {
				timepicker: true,
				dayOfWeekStart: 1,
				format: 'd.m.Y H:i',
			} );
			$(
				'input.datetimepicker[name="_sunflower_event_until"]'
			).datetimepicker( {
				timepicker: true,
				dayOfWeekStart: 1,
				format: 'd.m.Y H:i',
			} );
		}
	} );
} );
/* eslint-enable no-undef */
