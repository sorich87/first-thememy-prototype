
jQuery(function ($) {
	if ( $('.delete-theme').length ) {
		$('.delete-theme').click(function (e) {
			if ( ! confirm( 'Are you sure you want to delete this theme?' ) )
				e.preventDefault();
		});
	}
});
