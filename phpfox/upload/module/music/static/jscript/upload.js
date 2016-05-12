
$Core.music =
{
	upload: function(bForm)
	{
		if (bForm)
		{			
			/*
			if (empty($('#js_upload_mp3').val()))
			{
				$('#core_js_messages').html('<div class="error_message">' + oTranslations['music.select_an_mp3'] + '</div>');
				
				return false;
			}
			*/
			
			$('#js_music_upload_song').parent().prepend('<div class="t_center" style="height:' + $('#js_music_upload_song').height() + 'px;">' + $.ajaxProcess('Uploading', 'large') + '</div>');
			$('#js_music_upload_song').hide();
			
			return true;
		}
		
		return false;
	},
	
	setName: function(iSong)
	{
		if ($("#title").val() != '')
		{
			$.ajaxCall('music.setName', 'sTitle='+$("#title").val() + '&iSong='+ iSong);
		}
	}
}