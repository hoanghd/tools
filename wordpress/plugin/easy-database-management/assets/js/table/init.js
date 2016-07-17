(function ($) {
    "use strict";	
	$("script[type='text/template'][name]").each(function(index, element){
		PageManager.Elements[$(this).attr('name')] = $(this).attr('container');
		PageManager.Templates[$(this).attr('name')] = _.template($(element).html());
	});
	
	PageManager.Data.formList = new PageManager.Collections.FormList();
	PageManager.Data.params = new PageManager.Models.Params();
	
	(new PageManager.Views.Main());
	(new PageManager.Views.Forms());
	(new PageManager.Views.FormElements({}));
	(new PageManager.Views.DatabaseElements({}));	
}(window.jQuery));

jQuery(document).ready(function($) {
	$.fn.editable.defaults.mode = 'inline';
	$(".editable [data-type='text']").editable({
		success: function(response, newValue) {
			console.dir([response, newValue, $(this).data()]);
			return true;
		}
	});	
	$(".editable .form[data-type='select']").editable({
        source: [
              {value: 'string', text: 'String'},
              {value: 'number', text: 'Number'},
              {value: 'text', text: 'Text'}
           ]
    });	
	$(".editable .db[data-type='select']").editable({
        source: [
              {value: 'string', text: 'String'},
              {value: 'number', text: 'Number'},
              {value: 'text', text: 'Text'}
           ]
    });
});