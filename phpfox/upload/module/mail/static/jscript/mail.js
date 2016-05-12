
$Behavior.mailView = function()
{
	if ($Core.exists('#js_mail_textarea')){
		$('#js_mail_textarea #message').keydown(function(){$Core.resizeTextarea($(this));});
	}	
}