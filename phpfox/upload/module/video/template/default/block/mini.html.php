<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: mini.html.php 3214 2011-09-30 12:05:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="{if isset($phpfox.iteration.minivideos)}{if is_int($phpfox.iteration.minivideos/2)}row1{else}row2{/if}{if $phpfox.iteration.minivideos == 1 && !isset($bIsLoadingMore)} row_first{/if}{else}row1 row_first row_no_border{/if}">
	<div style="float:left; width:120px;" class="t_center">
		<a href="{permalink module='video' id=$aMiniVideo.video_id title=$aMiniVideo.title}">{img server_id=$aMiniVideo.image_server_id path='video.url_image' file=$aMiniVideo.image_path suffix='_120' max_width=90 max_height=70 class='js_mp_fix_width' title=$aMiniVideo.title}</a>
	</div>
	<div style="margin-left:128px;">
		<a href="{permalink module='video' id=$aMiniVideo.video_id title=$aMiniVideo.title}" class="row_sub_link">{$aMiniVideo.title|clean}</a>
		<div class="extra_info_link">
			by {$aMiniVideo|user}<br />
			{if $aMiniVideo.total_view == 0}1 views{else}{$aMiniVideo.total_view|number_format} views{/if}
		</div>
	</div>
	<div class="clear"></div>
</div>