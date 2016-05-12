<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: battle.html.php 2633 2011-05-30 13:57:44Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !isset($aPhotos.two)}
<div class="extra_info">
	No photos found.
</div>
{else}
<div {if $bFullMode} id="photo_battle_full_mode"{/if}>
	
	<div id="photo_battl_full_close">
		{if $bFullMode}
		<a href="{url link='photo.battle'}">Close Full Mode</a>
		{else}
		<a href="{url link='photo.battle' mode='full'}">Open Full Mode</a>
		{/if}
	</div>	
	<div class="photo_battle_holder">
		<div class="photo_battle_left">
			<a href="{$aPhotos.one.link}">{img server_id=$aPhotos.one.server_id path='photo.url_photo' file=$aPhotos.one.destination suffix='_'$sImageHeight'' max_width=$sMaxImageHeight max_height=$sMaxImageHeight}</a>
			<div class="extra_info">
				Added by {$aPhotos.one|user}<br /> on {$aPhotos.one.time_stamp|convert_time}
			</div>			
		</div>
		<div class="photo_battle_center">VS</div>	
		<div class="photo_battle_right">
			<a href="{$aPhotos.two.link}">{img server_id=$aPhotos.two.server_id path='photo.url_photo' file=$aPhotos.two.destination suffix='_'$sImageHeight'' max_width=$sMaxImageHeight max_height=$sMaxImageHeight}</a>
			<div class="extra_info">
				Added by {$aPhotos.two|user}<br /> on {$aPhotos.two.time_stamp|convert_time}
			</div>		
		</div>
	</div>
</div>
{/if}