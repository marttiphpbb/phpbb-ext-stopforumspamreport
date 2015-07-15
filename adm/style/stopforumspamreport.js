;(function($, window, document) {
	$('document').ready(function () {
		var dl = $('#stopforumspamreport_dl');
		dl.insertAfter($('#user_delete fieldset dl').eq(0));
		dl.css('display', 'inherit');
	});
})(jQuery, window, document);
