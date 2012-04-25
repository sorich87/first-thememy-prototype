
jQuery(function ($) {
	if ( $(".delete-theme").length ) {
		$(".delete-theme").click(function (e) {
			if ( ! confirm( "Are you sure you want to delete this theme?" ) )
				e.preventDefault();
		});
	}

	if ( $("#save-theme-slug").length ) {
		$("#save-theme-slug").click(function (e) {
			e.preventDefault();

			var self = this;

			$(self).button("loading");

			var data = {
				_ajax_nonce: thememy.nonce,
				action     : "thememy-save-theme-slug",
				theme_slug : $("#theme-slug").val(),
				theme_id   : $("#theme-id").val()
			};
			$.post( thememy.ajaxUrl, data, function (r) {
				$(self).button(r).addClass(function () {
					return "complete" === r ? "btn-success" : "btn-danger";
				});
			});
		});

		$("#theme-slug").focus(function (e) {
			$("#save-theme-slug").button("reset").removeClass("btn-success").removeClass("btn-danger");
		});
	}
});
