<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: view.html.php 3348 2011-10-25 07:27:46Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aForms.view_id) && $aForms.view_id == 1}
<div class="message js_moderation_off">
	{phrase var='photo.image_is_pending_approval'}
</div>
{/if}
<div class="item_view photo_item_view" {if $bIsTheater} id="photo_view_theater_mode"{/if}>
	<div id="js_album_outer_content">
		
		{if !$bIsTheater}
	    <div class="item_info">
			{phrase var='photo.time_stamp_by_full_name' time_stamp=$aForms.time_stamp|convert_time full_name=$aForms|user} {if !empty($aForms.album_id)} {phrase var='photo.in'} <a href="{$aForms.album_url}">{$aForms.album_title|clean|split:45|shorten:75:'...'}</a>{/if}
	    </div>
	    {/if}
	    {if (Phpfox::getUserParam('photo.can_edit_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_edit_other_photo')
	    	|| (Phpfox::getUserParam('photo.can_delete_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_delete_other_photos')
	    }
		<div class="item_bar">
			<div class="item_bar_action_holder">
				{if $aForms.view_id == '1' && Phpfox::getUserParam('photo.can_approve_photos')}
					<a href="#" class="item_bar_approve item_bar_approve_image" onclick="return false;" style="display:none;" id="js_item_bar_approve_image">{img theme='ajax/add.gif'}</a>
					<a href="#" class="item_bar_approve" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('photo.approve', 'inline=true&amp;id={$aForms.photo_id}'); return false;">{phrase var='photo.approve'}</a>
				{/if}
				<a href="#" class="item_bar_action"><span>{phrase var='photo.actions'}</span></a>		
				<ul>
					{template file='photo.block.menu'}
				</ul>			
			</div>		
		</div>	    
		{/if}
		{if !$bIsTheater}
		{if $aPhotoStream.total > 1}
	    <div class="photo_next_previous">
			<ul>
			<li class="photo_stream_info">{phrase var='photo.photo_current_of_total' current=$aPhotoStream.current total=$aPhotoStream.total}</li>
			{if isset($aPhotoStream.previous.photo_id)}
			<li class="previous"><a href="{$aPhotoStream.previous.link}">{phrase var='photo.previous'}</a></li>
			{/if}	
		
			{if isset($aPhotoStream.next.photo_id)}
			<li class="next"><a href="{$aPhotoStream.next.link}">{phrase var='photo.next'}</a></li>
			{/if}
			</ul>
			<div class="clear"></div>
		</div>
		{/if}			
		{/if}
	
		<div class="t_center" id="js_photo_view_holder_process"></div>
		<div class="t_center" id="js_photo_view_holder">
		
		{if $aPhotoStream.total > 1 && $bIsTheater}
	    <div class="photo_next_previous">
			<ul>
			{if isset($aPhotoStream.previous.photo_id)}
			<li class="previous"><a href="{$aPhotoStream.previous.link}"{if $bIsTheater} class="thickbox photo_holder_image" rel="{$aPhotoStream.previous.photo_id}"{/if}>{phrase var='photo.previous'}</a></li>
			{/if}	
		
			{if isset($aPhotoStream.next.photo_id)}
			<li class="next"><a href="{$aPhotoStream.next.link}"{if $bIsTheater} class="thickbox photo_holder_image" rel="{$aPhotoStream.next.photo_id}"{/if}>{phrase var='photo.next'}</a></li>
			{/if}
			</ul>
			<div class="clear"></div>
		</div>
		{/if}		
	
		
			{if (Phpfox::getUserParam('photo.can_edit_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_edit_other_photo')}
			<div class="photo_rotate">
				<ul>
					<li><a href="#" onclick="$('#menu').remove(); $('#noteform').hide(); $('#js_photo_view_image').imgAreaSelect({left_curly} hide: true {right_curly}); $('#js_photo_view_holder').hide(); $('#js_photo_view_holder_process').html($.ajaxProcess('', 'large')).height($('#js_photo_view_holder').height()).show(); $.ajaxCall('photo.rotate', 'photo_id={$aForms.photo_id}&amp;photo_cmd=left'); return false;" class="left js_hover_title"><span class="js_hover_info">{phrase var='photo.rotate_right'}</span></a></li>
					<li><a href="#" onclick="$('#menu').remove(); $('#noteform').hide(); $('#js_photo_view_image').imgAreaSelect({left_curly} hide: true {right_curly}); $('#js_photo_view_holder').hide(); $('#js_photo_view_holder_process').html($.ajaxProcess('', 'large')).height($('#js_photo_view_holder').height()).show(); $.ajaxCall('photo.rotate', 'photo_id={$aForms.photo_id}&amp;photo_cmd=right'); return false;" class="right js_hover_title"><span class="js_hover_info">{phrase var='photo.rotate_left'}</span></a></li>
				</ul>
				<div class="clear"></div>
			</div>
			{/if}			
		
			{if isset($aPhotoStream.next.photo_id)}
			<a href="{$aPhotoStream.next.link}"{if $bIsTheater} class="thickbox photo_holder_image" rel="{$aPhotoStream.next.photo_id}"{/if}>
			{/if}
			{if Phpfox::isMobile()}
				{if $aForms.user_id == Phpfox::getUserId()}
					{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' file=$aForms.destination suffix='_500' max_width=285 max_height=300 title=$aForms.title time_stamp=true onmouseover="$('.photo_next_previous .next a').addClass('is_hover_active');" onmouseout="$('.photo_next_previous .next a').removeClass('is_hover_active');"}
				{else}
					{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' file=$aForms.destination suffix='_500' max_width=285 max_height=300 title=$aForms.title onmouseover="$('.photo_next_previous .next a').addClass('is_hover_active');" onmouseout="$('.photo_next_previous .next a').removeClass('is_hover_active');"}
				{/if}
			{else}
				{if $aForms.user_id == Phpfox::getUserId()}
					{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' file=$aForms.destination suffix='_500' max_width=500 max_height=500 title=$aForms.title time_stamp=true onmouseover="$('.photo_next_previous .next a').addClass('is_hover_active');" onmouseout="$('.photo_next_previous .next a').removeClass('is_hover_active');"}
				{else}
					{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' file=$aForms.destination suffix='_500' max_width=500 max_height=500 title=$aForms.title onmouseover="$('.photo_next_previous .next a').addClass('is_hover_active');" onmouseout="$('.photo_next_previous .next a').removeClass('is_hover_active');"}
				{/if}
			{/if}
			
			{if isset($aPhotoStream.next.photo_id)}
			</a>
			{/if}
		
		</div>

		{if $bIsTheater}
		<div class="photo_view_ad">
			{module name='ad.display' block_id='photo_theater'}
		</div>
		
		<div class="photo_view_detail">
			<div class="photo_view_detail_content">
				{if $bIsTheater}
					{if $aPhotoStream.total > 1}
					<div class="extra_info">
						{phrase var='photo.photo_current_of_total' current=$aPhotoStream.current total=$aPhotoStream.total}
					</div>
					{/if}
				{/if}
				<div class="extra_info">
					{if !empty($aForms.album_id)} In the album <a href="{$aForms.album_url}">{$aForms.album_title|clean|split:45|shorten:75:'...'}</a>{/if}
					{if isset($aCallback.theater_mode)}<div class="p_top_4">{$aCallback.theater_mode}</div>{/if}
					<div class="p_top_4">
						{phrase var='photo.by_full_name_lowercase' full_name=$aForms|user}
					</div>
				</div>
			</div>
			
			{module name='photo.detail'}
		</div>	
		
		<div class="photo_view_comment">
		{/if}		
			{if $aForms.description}
			<div id="js_photo_description_{$aForms.photo_id}">
				{$aForms.description|clean}
			</div>
			{/if}
			
			<div class="extra_info" style="display:none;">
				<b>{phrase var='photo.in_this_photo'}:</b> <span id="js_photo_in_this_photo"></span>
			</div>		
		
			{if Phpfox::isModule('tag') && isset($aForms.tag_list)}
			{module name='tag.item' sType='photo' sTags=$aForms.tag_list iItemId=$aForms.photo_id iUserId=$aForms.user_id}
			{/if}	
			
			{plugin call='photo.template_default_controller_view_extra_info'}
			
			<div {if $aForms.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
				{module name='feed.comment'}
			</div>	
		{if $bIsTheater}
		</div>	
		
		<div class="clear"></div>
		{/if}
	</div>
</div>
<script type="text/javascript">$Behavior.tagPhoto = function() {l} $Core.photo_tag.init({l}{$sPhotoJsContent}{r}); {r}</script>