<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: mini.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
	<div id="js_comment_{$aComment.comment_id}" class="js_mini_feed_comment comment_mini js_mini_comment_item_{$aComment.item_id}">
		{if (Phpfox::getUserParam('comment.delete_own_comment') && Phpfox::getUserId() == $aComment.user_id) || Phpfox::getUserParam('comment.delete_user_comment') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId() && Phpfox::getUserParam('comment.can_delete_comments_posted_on_own_profile'))}
		<div class="feed_comment_delete_link"><a href="#" class="action_delete js_hover_title" onclick="$.ajaxCall('comment.InlineDelete', 'type_id={$aComment.type_id}&amp;comment_id={$aComment.comment_id}', 'GET'); return false;"><span class="js_hover_info">{phrase var='comment.delete_this_comment'}</span></a></div>
		{/if}
		<div class="comment_mini_image">
		{if Phpfox::isMobile()}
			{img user=$aComment suffix='_50_square' max_width=32 max_height=32}
		{else}
			{img user=$aComment suffix='_50_square' max_width=32 max_height=32}
		{/if}
		</div>
		<div class="comment_mini_content">
			{$aComment|user}<div id="js_comment_text_{$aComment.comment_id}" class="comment_mini_text {if $aComment.view_id == '1'}row_moderate{/if}">{$aComment.text|feed_strip|shorten:'300':'comment.view_more':true|split:30}</div>			
			<div class="comment_mini_action">
				<ul>
					<li class="comment_mini_entry_time_stamp">{$aComment.post_convert_time}</li>
					<li><span>&middot;</span></li>
					{if !Phpfox::isMobile()}
					{if (Phpfox::getUserParam('comment.edit_own_comment') && Phpfox::getUserId() == $aComment.user_id) || Phpfox::getUserParam('comment.edit_user_comment')}
					<li>
						<a href="inline#?type=text&amp;&amp;simple=true&amp;id=js_comment_text_{$aComment.comment_id}&amp;call=comment.updateText&amp;comment_id={$aComment.comment_id}&amp;data=comment.getText" class="quickEdit">{phrase var='comment.edit'}</a>
					</li>
					<li><span>&middot;</span></li>
					{/if}
					{/if}				
					
					{module name='like.link' like_type_id='feed_mini' like_item_id=$aComment.comment_id like_is_liked=$aComment.is_liked}
					<li class="js_like_link_holder"{if $aComment.total_like == 0} style="display:none;"{/if}><span>&middot;</span></li>
					<li class="js_like_link_holder"{if $aComment.total_like == 0} style="display:none;"{/if}><a href="#" onclick="return $Core.box('like.browse', 400, 'type_id=feed_mini&amp;item_id={$aComment.comment_id}');"><span class="js_like_link_holder_info">{if $aComment.total_like == 1}{phrase var='comment.1_person'}{else}{phrase var='comment.total_people' total=$aComment.total_like|number_format}{/if}</span></a></li>
					
					{if Phpfox::getUserParam('comment.can_moderate_comments') && $aComment.view_id == '1'}
					<li>
						<a href="#" onclick="$('#js_comment_text_{$aComment.comment_id}').removeClass('row_moderate'); $(this).remove(); $.ajaxCall('comment.moderateSpam', 'id={$aComment.comment_id}&amp;action=approve&amp;inacp=0'); return false;">{phrase var='comment.approve'}</a>					
					</li>					
					{/if}				
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	</div>