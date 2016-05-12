
$Behavior.photoView = function()
{	
	$('#js_update_photo_form').submit(function()
	{
		$('#js_updating_photo').html($.ajaxProcess(oTranslations['photo.updating_photo']));
		
		$(this).ajaxCall('photo.updatePhoto');
		
		$('#js_photo_edit_form').hide();
		$('#js_photo_outer_content').show();		

		return false;
	});
	
	$('#js_photo_cancel_edit').click(function()
	{
		$('#js_photo_edit_form').hide();
		$('#js_photo_outer_content').show();
		
		return false;
	});		
}
var bLoadedKeyBrowser = false;
$Behavior.eventKeyboard = function()
{
	if (bLoadedKeyBrowser == true)
	{
		return;
	}
	
	/*
	37 => left
	38 => up
	39 => right 
	40 => down
	*/
	$(document).keydown(function(e){
		if (e.keyCode == 37)
		{
			$('#js_photo_view_holder .previous a:first').click();
		}
		else if (e.keyCode == 39)
		{
			$('#js_photo_view_holder .next a:first').click();
		}	
	});
	bLoadedKeyBrowser = true;
}