// ajax form
// process the form
$(document).on('submit', 'form.ajax', function(event) {
	var form = $(this);
	$.ajax({
		type: form.attr('method'),
		url: form.attr('action'),
		dataType: 'html',
		data: form.serialize(),
		encode: true
	}).done(function(data) {
		form.parent().html(data);
	}).fail(function(data) {
		$(data).insertBefore(form);
	});
	event.preventDefault();
});