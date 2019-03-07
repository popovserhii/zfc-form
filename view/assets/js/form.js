PopovForm = {
  body: $('body'),
  onlyOnce: {},

  attachEvents: function () {
    this.attachAjaxForm();
    this.attachOnAddGroup();
    this.attachOnRemoveGroup();
  },

  // Ajax Form
  attachAjaxForm: function () {
    // Remove handler from existing elements
    this.body.off('submit', 'form.ajax', this.ajaxForm);

    // Re-add event handler for all matching elements
    this.body.on('submit', 'form.ajax', this.ajaxForm);
  },

  // Add Group Elements
  attachOnAddGroup: function () {
    // Remove handler from existing elements
    this.body.off('click', '.add-field-group', this.addGroup);

    // Re-add event handler for all matching elements
    this.body.on('click', '.add-field-group', this.addGroup);
  },

  // Remove Group Elements
  attachOnRemoveGroup: function () {
    // Remove handler from existing elements
    this.body.off('click', '.remove-field-group', this.removeGroup);

    // Re-add event handler for all matching elements
    this.body.on('click', '.remove-field-group', this.removeGroup);
  },

  ajaxForm: function (e) {
    // ajax form
    // process the form
    var form = $(this);
    var wrapped = form.closest('.block');

    StagemPreloader.load(wrapped);

    $.ajax({
      type: form.attr('method'),
      url: form.attr('action'),
      dataType: 'html',
      data: form.serialize(),
      encode: true
    }).done(function (data) {
      // if 'data-refresh' set than that element will be replaced with content returned after form process
      StagemPreloader.hide(wrapped);
      if (form.data('refresh')) {
        var elm = $(form.data('refresh'));
        elm.replaceWith(data);
      } else {
        form.parent().html(data);
      }
    }).fail(function (data) {
      StagemPreloader.load(wrapped);
      $(data).insertBefore(form);
    });
    e.preventDefault();
  },

  addGroup: function () {
    var self = PopovForm;
    var fieldset = $('#' + $(this).data('group-id'));
    var fieldGroups = self.getFieldGroup(fieldset);

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
      var name = elm.attr(attrName).replace(/\d/, numGroup); // with many levels of fieldset may have problem with number of replace

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

    var fieldset = elm.closest('fieldset');
    var fieldGroup = self.getFieldGroup(fieldset);
    var fieldGroups = fieldGroup.siblings('.form-group').add(fieldGroup); // Here might be also '.field-group'

    if (fieldGroups.length === 1) { // if we remove the last group on a page than add it to cache
      self.body.data(fieldset.attr('id') + '-form-group-template', fieldGroups.first().detach());
    } else {
      fieldGroups.each(function (i, group) {
        var fieldGroup = $(group);
        // Remove the fieldGroup if button which has been clicked is found
        var clickedButton = fieldGroup.find(elm);
        if (clickedButton.length) {
          fieldGroup.remove();

          return false; // Break loop
        }
      });
      //console.log(toRemoveGroup)
      //toRemoveGroup.remove();
    }

    return false;
  },

  getFieldGroup: function (fieldset) {
    var fieldGroups = fieldset.closestDescendent('.field-group');
    if (!fieldGroups.length) {
      fieldGroups = fieldset.closestDescendent('.form-group');
    }
    if (!fieldGroups.length) { // get last detached form-group element from cache
      fieldGroups = this.body.data(fieldset.attr('id') + '-form-group-template');
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

/**
 * Closest Descendent filter
 *
 * Get the nearest children element in hierarchy by selector
 *
 * @link https://stackoverflow.com/a/8962023/1335142
 */
(function($) {
  $.fn.closestDescendent = function(filter) {
    var found = $(),
      currentSet = this; // Current place
    while (currentSet.length) {
      found = currentSet.filter(filter);
      if (found.length) break;  // At least one match: break loop
      // Get all children of the current set
      currentSet = currentSet.children();
    }
    return found.first(); // Return first match of the collection
  }
})(jQuery);