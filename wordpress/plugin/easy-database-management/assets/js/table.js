
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