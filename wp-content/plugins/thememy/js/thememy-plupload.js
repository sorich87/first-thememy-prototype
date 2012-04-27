
jQuery(function ($) {
	var uploader = new plupload.Uploader(thememyPlupload);
	var errors = [];
	var browseButton = "#" + thememyPlupload.browse_button;

	uploader.init();

	uploader.bind("Error", function (up, error) {
		errors[errors.length] = error;
		up.refresh();
	});

	uploader.bind("FilesAdded", function (up, files) {
		$(browseButton).button("loading");

		up.refresh();
		up.start();
	});

	uploader.bind("FileUploaded", function (up, files, r) {
		$("#slideshow-images").show().append("<li class='span3'><span class='thumbnail'>" +
																				 r.response + "</span></li>");
		$("#no-image").hide();
	});

	uploader.bind("UploadComplete", function (up, files) {
		if ( errors.length ) {
			$(browseButton).button("error").addClass("btn-danger");
			$("#errorlist").show();
			$.each(errors, function (i, error) {
				$("<li>" + error.message +
					" (" + error.message + ")"
					(error.file ? " on " + error.file.name : "") +
					"</li>").appendTo("#errorlist ul");
			});
		} else {
			$(browseButton).button("complete").addClass("btn-success");
		}

		setTimeout(function () {
			$(browseButton).removeClass("btn-success btn-danger").button('reset');
		}, 2000);
	});
});

