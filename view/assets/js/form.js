PopovForm = {
	body: $('body'),
	onlyOnce: {},

	attachEvents: function() {
		this.attachOnAddGroup();
		this.attachOnRemoveGroup();
	},

	// Add Group Elements
	attachOnAddGroup: function() {
		// Remove handler from existing elements
		this.body.off('click', '.add-field-group', this.addGroup);

    // Re-add event handler for all matching elements
		this.body.on('click', '.add-field-group', this.addGroup);
	},

	// Remove Group Elements
	attachOnRemoveGroup: function() {
		// Remove handler from existing elements
		this.body.off('click', '.remove-field-group', this.removeGroup);

    // Re-add event handler for all matching elements
		this.body.on('click', '.remove-field-group', this.removeGroup);
	},

  addGroup: function () {
		var self = PopovForm;
    var fieldset = $('#' + $(this).data('group-id'));
    var fieldGroups = self.getFieldGroups(fieldset);

    // Determine general count on first add action and than relay on only this value.
    // We cannot relay on number of element such as we don't know which element will be removed
    var numGroupKey = fieldset.attr('id') + '-form-group-length';
    var numGroup = self.body.data(numGroupKey) || fieldGroups.length;

    var template = $(fieldset.next('span[data-template]').data('template').replace(/__index__/g, numGroup));
    var fieldGroupTemp = fieldGroups.last().clone();

    fieldGroupTemp.children('.has-error').removeClass('has-error').end().find('.list-error').remove();
    fieldGroupTemp.find('input, button, select, textarea, label').each(function () {
      var elm = $(this);
      var attrName = elm.is('label') ? 'for' : 'name';
      var name = elm.attr(attrName).replace(/\d/, numGroup); // with many levels of fieldset may be problem with number replace

      if (elm.is('label')) {
        elm.attr(attrName, name);
      } else if (!elm.children().length) { // skip <label /> wrapper on elements and so on
        var selector = '[' + attrName + '="' + name + '"][type="' + elm.attr('type') + '"]';
        var tempElm = template.find(selector);
        if (tempElm.length) { // element can be added after $form->prepare() this element isn't present in template
          elm.replaceWith(tempElm);
        }
      }
    });

    fieldGroupTemp.appendTo(fieldset);
    numGroup++;
    self.body.data(numGroupKey, numGroup);

    return false;
	},

  removeGroup: function () {
    var self = PopovForm;
    var elm = $(this);
    var toRemoveGroup = self.getRemoveFieldGroup(elm);
    var fieldset = elm.closest('fieldset');
    var fieldGroups = self.getFieldGroups(fieldset);
    if (fieldGroups.length === 1) { // if we remove last group on page than add to cache
      self.body.data(fieldset.attr('id') + '-form-group-template', toRemoveGroup.detach());
    } else {
      toRemoveGroup.remove();
    }

    return false;
	},

	getFieldGroups: function(fieldset) {
    var fieldGroups = fieldset.find('.field-group'); //fieldset.find('.field-group');
    if (!fieldGroups.length) {
      fieldGroups = fieldset.find('.form-group');
      if (!fieldGroups.length) { // get last detached form-group element from cache
        fieldGroups = this.body.data(fieldset.attr('id') + '-form-group-template');
      }
    }
    return fieldGroups;
  },

  getRemoveFieldGroup: function (button) {
    var toRemoveGroup = button.closest('.field-group');
    if (!toRemoveGroup.length) {
      toRemoveGroup = button.closest('.form-group');
    }
    return toRemoveGroup;
  }
};

jQuery(document).ready(function ($) {
	PopovForm.attachEvents();
});