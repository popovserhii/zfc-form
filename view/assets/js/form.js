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

$(document).on('click', '.add-field-group', function (e) {
    var fieldset = $('#' + $(this).data('group-id'));
    var fieldGroups = fieldset.find('.field-group');
    var template = $(fieldset.next('span').data('template').replace(/__index__/g, fieldGroups.length));
    var fieldGroupTemp = fieldGroups.last().clone();

    fieldGroupTemp.children('.has-error').removeClass('has-error').end().find('.list-error').remove();
    fieldGroupTemp.find('input, button, select, textarea, label').each(function () {
        var elm = $(this);
        var attrName = elm.is('label') ? 'for' : 'name';
        var name = elm.attr(attrName).replace(fieldGroups.length - 1, fieldGroups.length);
        elm.is('label') ? elm.attr(attrName, name) : elm.replaceWith(template.find('[' + attrName + '="' + name + '"]'));
    });
    fieldGroupTemp.appendTo(fieldset);

    return false;
});