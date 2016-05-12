<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: track-entry.html.php 3199 2011-09-27 13:03:33Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<li class="js_music_store_album_holder"{if isset($phpfox.iteration.songs) && $phpfox.iteration.songs > 10} style="display:none;"{/if}>
	<div class="block_listing_image">
		<a href="#" onclick="$.ajaxCall('music.playInFeed', 'id={$aSong.song_id}&amp;track=js_block_track_player'); return false;">{img theme='misc/play_button.png'}</a>
	</div>
	<div class="block_listing_title" style="padding-left:38px;">
		<a href="{permalink module='music' id=$aSong.song_id title=$aSong.title}" title="{$aSong.title|clean}">{$aSong.title|clean|shorten:50:'...'|split:50}</a>
		<div class="extra_info">
			{$aSong.time_stamp|convert_time}
		</div>
	</div>
	<div class="clear"></div>
</li>