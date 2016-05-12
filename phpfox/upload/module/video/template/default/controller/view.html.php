<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_view">
	<div id="js_video_edit_form_outer" style="display:none;">
		<form method="post" action="#" onsubmit="$(this).ajaxCall('video.viewUpdate'); return false;">
			<div><input type="hidden" name="val[is_inline]" value="true" /></div>
			<div id="js_video_edit_form"></div>
			<div class="table_clear">
				<ul class="table_clear_button">
					<li><input type="submit" value="{phrase var='video.update'}" class="button" /></li>
					<li><a href="#" id="js_video_go_advanced" class="button_off_link">{phrase var='video.go_advanced_uppercase'}</a></li>
					<li><a href="#" onclick="$('#js_video_edit_form_outer').hide(); $('#js_video_outer_body').show(); return false;" class="button_off_link">{phrase var='video.cancel_uppercase'}</a></li>
				</ul>
				<div class="clear"></div>
			</div>
		</form>
	</div>
	
	<div id="js_video_outer_body">	
	
		{if $aVideo.in_process > 0}
		<div class="message">
			{phrase var='video.video_is_being_processed'}
		</div>
		{else}
		{if $aVideo.view_id == 2}
		<div class="message js_moderation_off" id="js_approve_video_message">
			{phrase var='video.video_is_pending_approval'}
		</div>
		{/if}
		{/if}	
	
		{if (($aVideo.user_id == Phpfox::getUserId() && Phpfox::getUserParam('video.can_edit_own_video')) || Phpfox::getUserParam('video.can_edit_other_video'))
			|| (($aVideo.user_id == Phpfox::getUserId() && Phpfox::getUserParam('video.can_delete_own_video')) || Phpfox::getUserParam('video.can_delete_other_video'))
			|| (Phpfox::getUserParam('video.can_sponsor_video') && !defined('PHPFOX_IS_GROUP_VIEW'))
			|| (Phpfox::getUserParam('video.can_approve_videos') && $aVideo.view_id == 2)
		}
		<div class="item_bar">
			<div class="item_bar_action_holder">
			{if (Phpfox::getUserParam('video.can_approve_videos') && $aVideo.view_id == 2)}
				<a href="#" class="item_bar_approve item_bar_approve_image" onclick="return false;" style="display:none;" id="js_item_bar_approve_image">{img theme='ajax/add.gif'}</a>			
				<a href="#" class="item_bar_approve" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('video.approve', 'inline=true&amp;video_id={$aVideo.video_id}'); return false;">{phrase var='video.approve'}</a>
			{/if}
				<a href="#" class="item_bar_action"><span>{phrase var='video.actions'}</span></a>	
				<ul>
					{template file='video.block.menu'}
				</ul>			
			</div>
		</div>	
		{/if}
	
		<div class="t_center">
		{if $aVideo.is_stream}
			{$aVideo.embed_code}
		{else}
		<div id="js_video_player" style="width:640px; height:390px; margin:auto;{if $aVideo.in_process > 0} display:none;{/if}"></div>
		{/if}
		</div>
		
		{module name='video.detail'}	
		<div {if $aVideo.view_id}style="display:none;" class="js_moderation_on"{/if}>
			
		<div class="video_rate_body">
			<div class="video_rate_display">
				{module name='rate.display'}
			</div>
			<a href="#" class="video_view_embed">{phrase var='video.embed'}</a>
			<div class="video_view_embed_holder">
				<input name="#" value="{$aVideo.embed}" type="text" size="22" onfocus="this.select();" style="width:490px;" />	
			</div>
		</div>				
		
		{plugin call='video.template_default_controller_view_extra_info'}
		
		{module name='feed.comment'}
		</div>
	</div>
</div>