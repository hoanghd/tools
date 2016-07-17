jQuery(document).ready(function($) {
	$.fn.editable.defaults.mode = 'inline';
	$("table.editable [data-type='text']").editable({
		success: function(response, newValue) {
			console.dir([response, newValue, $(this).data()]);
			return true;
		}
	});
	
	$("table.editable [data-type='select']").editable({
        source: [
              {value: 'string', text: 'String'},
              {value: 'number', text: 'Number'},
              {value: 'text', text: 'Text'}
           ]
    });
});