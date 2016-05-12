<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: share.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="global_attachment_holder_section" id="global_attachment_music">	
	<div><input type="hidden" name="val[iframe]" value="1" /></div>
	<div><input type="hidden" name="val[method]" value="simple" /></div>	
	<div class="table">
		<div class="table_left">
			{phrase var='music.title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[music_title]" style="width:90%;" id="js_form_music_title" />
		</div>
	</div>
	<div class="table">
		<div class="table_left">
			{phrase var='music.mp3'}:
		</div>
		<div class="table_right">	
			<div><input type="file" name="mp3" id="global_attachment_music_file_input" value="" onchange="$bButtonSubmitActive = true; $('.activity_feed_form_button .button').removeClass('button_not_active'); $Core.resetActivityFeedErrorMessage();" /></div>
			<div class="extra_info">
				{phrase var='music.select_a_song_to_attach'}
			</div>
		</div>
	</div>
</div>
{literal}
<script type="text/javascript">
$ActivityFeedCompleted.resetMusicForm = function()
{
	$('#js_form_music_title').val('');
	$('#global_attachment_music_file_input').val('');
}
</script>
{/literal}