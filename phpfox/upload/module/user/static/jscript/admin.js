
$(function() 
{
	$('#user_group_id').change(function()
	{
		$('#js_custom_field_holder').html($.ajaxProcess(oTranslations['user.loading_custom_fields'], 'large'));
		$.ajaxCall('user.loadCustomField', 'user_group_id=' + this.value + '&user_id=' + $('#js_user_id').val());
	});
});