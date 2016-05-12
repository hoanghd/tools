<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: featured-album.html.php 2569 2011-04-27 19:03:20Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="block_listing">
{foreach from=$aFeaturedAlbums item=aAlbum}
	<li>
		<div class="block_listing_image">
			<a href="{permalink module='music.album' id=$aAlbum.album_id title=$aAlbum.name}">{img server_id=$aAlbum.server_id path='music.url_image' file=$aAlbum.image_path suffix='_50_square' max_width='32' max_height='32'}</a>
		</div>
		<div class="block_listing_title" style="padding-left:38px;">
			<a href="{permalink module='music.album' id=$aAlbum.album_id title=$aAlbum.name}">{$aAlbum.name|clean}</a>
			<div class="extra_info">
				<ul class="extra_info_middot"><li>{if $aAlbum.total_track == 1}1 track{else}{$aAlbum.total_track|number_format} tracks{/if}</li><li>&middot;</li><li>{if $aAlbum.total_play == 1}1 play{else}{$aAlbum.total_play|number_format} plays{/if}</li></ul>
			</div>
		</div>
		<div class="clear"></div>	
	</li>
{/foreach}
</ul>