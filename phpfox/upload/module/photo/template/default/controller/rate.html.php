<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: rate.html.php 2633 2011-05-30 13:57:44Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !isset($aPhoto.photo_id)}
<div class="extra_info">
	No available images to rate.
</div>
{else}
<div class="main_break"></div>
<div class="t_center">
	<ul id="rate_bar">
		{$sRatingBar}		
	</ul>	
	{img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_500' max_width=500 max_height=500}
	<div class="extra_info">
		Added by {$aPhoto|user} on {$aPhoto.time_stamp|convert_time}
	</div>
	{plugin call='photo.template_controller_rate'}
</div>
{/if}