
jQuery(function ($) {
	if ( $(".delete-theme").length ) {
		$(".delete-theme").click(function (e) {
			if ( ! confirm( "Are you sure you want to delete this theme?" ) )
				e.preventDefault();
		});
	}

	if ( $("#verify-theme-slug").length ) {
		$("#verify-theme-slug").click(function (e) {
			e.preventDefault();

			var self = this;

			$(self).button("loading");

			var data = {
				_ajax_nonce : thememy.nonce,
				action      : "thememy-verify-theme-slug",
				theme_slug  : $("#theme-slug").val(),
				theme_id    : $("#theme-id").val()
			};
			$.post( thememy.ajaxUrl, data, function (r) {
				$(self).button(r).removeClass("btn-info").addClass(function () {
					return "available" === r ? "btn-success" : "btn-danger";
				});
			});
		});

		$("#theme-slug").focus(function (e) {
			$("#verify-theme-slug").button("reset")
				.removeClass("btn-success").removeClass("btn-danger").addClass("btn-info");
		});
	}

	if ( $(".thumbnail .delete-image").length ) {
		$(".thumbnail .delete-image").on("click", function (e) {
			e.preventDefault();

			var li = $(this).parentsUntil("ul.thumbnails", "li");
			li.hide();

			var data = {
				_ajax_nonce : thememy.nonce,
				action      : "thememy-delete-image",
				image_id    : $(this).attr("href"),
				theme_id    : $("#theme-id").val()
			};
			$.post( thememy.ajaxUrl, data, function (r) {
				if ( 'success' === r ) {
					li.remove();
				} else {
					li.show().addClass("alert-error");
					$("#errorlist").show().append("<li>An image could not be deleted</li>");
				}
			});
		});
	}
});
