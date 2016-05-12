<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: entry.html.php 3332 2011-10-20 12:50:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="row_feed_loop js_parent_feed_entry {if isset($aFeed.feed_mini)} row_mini {else}{if isset($bChildFeed)} row1{else}{if isset($phpfox.iteration.iFeed)}{if is_int($phpfox.iteration.iFeed/2)}row1{else}row2{/if}{if $phpfox.iteration.iFeed == 1 && !PHPFOX_IS_AJAX} row_first{/if}{else}row1{/if}{/if}{/if} js_user_feed" id="js_item_feed_{$aFeed.feed_id}">
	{if !Phpfox::isMobile() && ((defined('PHPFOX_FEED_CAN_DELETE')) || (Phpfox::getUserParam('feed.can_delete_own_feed') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_delete_other_feeds'))}
	<div class="feed_delete_link"><a href="#" class="action_delete js_hover_title" onclick="$.ajaxCall('feed.delete', 'id={$aFeed.feed_id}{if isset($aFeedCallback.module)}&amp;module={$aFeedCallback.module}&amp;item={$aFeedCallback.item_id}{/if}', 'GET'); return false;"><span class="js_hover_info">{phrase var='feed.delete_this_feed'}</span></a></div>
	{/if}
	{plugin call='feed.template_block_entry_1'}
	<div class="activity_feed_image">	
		{if !isset($aFeed.feed_mini)}
		{img user=$aFeed suffix='_50_square' max_width=50 max_height=50}
		{/if}
	</div><!-- // .activity_feed_image -->
	<div class="activity_feed_content">
		<div class="activity_feed_content_text">						
			{if !isset($aFeed.feed_mini)}
			<div class="activity_feed_content_info">{$aFeed|user|split:50}{if isset($aFeed.parent_user)} {img theme='layout/arrow.png' class='v_middle'} {$aFeed.parent_user|user:'parent_'} {/if}{if !empty($aFeed.feed_info)} {$aFeed.feed_info}{/if}</div>
			{/if}			
			{if !empty($aFeed.feed_mini_content)}
			<div class="activity_feed_content_status">
				<div class="activity_feed_content_status_left">
					<img src="{$aFeed.feed_icon}" alt="" class="v_middle" /> {$aFeed.feed_mini_content} 
				</div>
				<div class="activity_feed_content_status_right">
					{template file='feed.block.link'}
				</div>
				<div class="clear"></div>
			</div>
			{/if}

			{if !empty($aFeed.feed_status)}
			<div class="activity_feed_content_status">
				{$aFeed.feed_status|feed_strip|shorten:200:'feed.view_more':true|split:55}				
			</div>
			{/if}
			
			<div class="activity_feed_content_link">
				
				{if $aFeed.type_id == 'friend' && isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
				{foreach from=$aFeed.more_feed_rows item=aFriends}
				{$aFriends.feed_image}
				{/foreach}
				{$aFeed.feed_image}
				{else}
				{if !empty($aFeed.feed_image)}
				<div class="activity_feed_content_image"{if isset($aFeed.feed_custom_width)} style="width:{$aFeed.feed_custom_width};"{/if}>
					{if is_array($aFeed.feed_image)}
					<ul class="activity_feed_multiple_image">
					{foreach from=$aFeed.feed_image item=sFeedImage}
						<li>{$sFeedImage}</li>
					{/foreach}
					</ul>
					<div class="clear"></div>
					{else}
					<a href="{$aFeed.feed_link}" class="{if isset($aFeed.custom_css)} {$aFeed.custom_css} {/if}{if !empty($aFeed.feed_image_onclick)}{if !isset($aFeed.feed_image_onclick_no_image)}play_link {/if} no_ajax_link{/if}"{if !empty($aFeed.feed_image_onclick)} onclick="{$aFeed.feed_image_onclick}"{/if}{if !empty($aFeed.custom_rel)} rel="{$aFeed.custom_rel}"{/if}>{if !empty($aFeed.feed_image_onclick)}{if !isset($aFeed.feed_image_onclick_no_image)}<span class="play_link_img">{phrase var='feed.play'}</span>{/if}{/if}{$aFeed.feed_image}</a>
					{/if}
				</div>
				{/if}
				<div class="{if (!empty($aFeed.feed_content) || !empty($aFeed.feed_custom_html)) && empty($aFeed.feed_image)} activity_feed_content_no_image{/if}{if !empty($aFeed.feed_image)} activity_feed_content_float{/if}"{if isset($aFeed.feed_custom_width)} style="margin-left:{$aFeed.feed_custom_width};"{/if}>
					{if !empty($aFeed.feed_title)}
					<a href="{$aFeed.feed_link}" class="activity_feed_content_link_title">{$aFeed.feed_title|clean|split:30}</a>
					{if !empty($aFeed.feed_title_extra)}
					<div class="activity_feed_content_link_title_link">
						<a href="{$aFeed.feed_title_extra_link}">{$aFeed.feed_title_extra|clean}</a>
					</div>
					{/if}
					{/if}			
					{if !empty($aFeed.feed_content)}
					<div class="activity_feed_content_display">
						{$aFeed.feed_content|feed_strip|shorten:200:'...'|split:55}				
					</div>
					{/if}
					{if !empty($aFeed.feed_custom_html)}
					<div class="activity_feed_content_display_custom">
						{$aFeed.feed_custom_html}
					</div>
					{/if}
				</div>	
				{if !empty($aFeed.feed_image)}
				<div class="clear"></div>
				{/if}		
				{/if}
			</div>
		</div><!-- // .activity_feed_content_text -->		

		{if isset($aFeed.feed_view_comment)}
		{module name='feed.comment'}
		{else}
		{template file='feed.block.comment'}		
		{/if}
		{if $aFeed.type_id != 'friend'}
		{if isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
		{if $iTotalExtraFeedsToShow = count($aFeed.more_feed_rows)}{/if}
		<a href="#" class="activity_feed_content_view_more" onclick="$(this).parents('.js_feed_view_more_entry_holder:first').find('.js_feed_view_more_entry').show(); $(this).remove(); return false;">{phrase var='feed.see_total_more_posts_from_full_name' total=$iTotalExtraFeedsToShow full_name=$aFeed.full_name|first_name}</a>			
		{/if}			
		{/if}
	</div><!-- // .activity_feed_content -->
	{plugin call='feed.template_block_entry_3'}	
</div><!-- // #js_item_feed_{$aFeed.feed_id} -->