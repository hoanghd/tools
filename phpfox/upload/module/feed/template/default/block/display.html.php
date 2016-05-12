<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: display.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::isUser() && !PHPFOX_IS_AJAX && $sCustomViewType === null}
{if (Phpfox::getUserBy('profile_page_id') > 0 && defined('PHPFOX_IS_USER_PROFILE')) || (isset($aFeedCallback.disable_share) && $aFeedCallback.disable_share) || (defined('PHPFOX_IS_USER_PROFILE') && !Phpfox::getService('user.privacy')->hasAccess('' . $aUser.user_id . '', 'feed.share_on_wall'))}

{else}

<div class="activity_feed_form_share">
	<div class="activity_feed_form_share_process">{img theme='ajax/add.gif' class='v_middle'}</div>
	<ul class="activity_feed_form_attach">
		<li class="share">{phrase var='feed.share'}:</li>
		{if isset($aFeedCallback.module)}
		<li><a href="#" style="background:url('{img theme='misc/comment_add.png' return_url=true}') no-repeat center left;" rel="global_attachment_status" class="active"><div>{phrase var='feed.post'}<span class="activity_feed_link_form_ajax">{$aFeedCallback.ajax_request}</span></div><div class="drop"></div></a></li>
		{elseif !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId()))}
		<li><a href="#" style="background:url('{img theme='misc/application_add.png' return_url=true}') no-repeat center left;" rel="global_attachment_status" class="active"><div>{phrase var='feed.status'}<span class="activity_feed_link_form_ajax">user.updateStatus</span></div><div class="drop"></div></a></li>
		{else}
		<li><a href="#" style="background:url('{img theme='misc/comment_add.png' return_url=true}') no-repeat center left;" rel="global_attachment_status" class="active"><div>{phrase var='feed.post'}<span class="activity_feed_link_form_ajax">feed.addComment</span></div><div class="drop"></div></a></li>
		{/if}
		
		{foreach from=$aFeedStatusLinks item=aFeedStatusLink}
		{if isset($aFeedCallback.module) && $aFeedStatusLink.no_profile}
		{else}
		{if ($aFeedStatusLink.no_profile && !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId()))) || !$aFeedStatusLink.no_profile}
		<li>
			<a href="#" style="background:url('{img theme='feed/'$aFeedStatusLink.icon'' return_url=true}') no-repeat center left;" rel="global_attachment_{$aFeedStatusLink.module_id}"{if $aFeedStatusLink.no_input} class="no_text_input"{/if}>
				<div>
					{$aFeedStatusLink.title|convert}
					{if $aFeedStatusLink.is_frame}
					<span class="activity_feed_link_form">{url link=''$aFeedStatusLink.module_id'.frame'}</span>
					{else}
					<span class="activity_feed_link_form_ajax">{$aFeedStatusLink.module_id}.{$aFeedStatusLink.ajax_request}</span>
					{/if}
					<span class="activity_feed_extra_info">{$aFeedStatusLink.description|convert}</span>
				</div>
				<div class="drop"></div>
			</a>
		</li>
		{/if}
		{/if}
		{/foreach}		
	</ul>
	<div class="clear"></div>
</div>	

<div class="activity_feed_form">
	<form method="post" action="#" id="js_activity_feed_form" enctype="multipart/form-data">
	{if isset($aFeedCallback.module)}
		<div><input type="hidden" name="val[callback_item_id]" value="{$aFeedCallback.item_id}" /></div>
		<div><input type="hidden" name="val[callback_module]" value="{$aFeedCallback.module}" /></div>
		<div><input type="hidden" name="val[parent_user_id]" value="{$aFeedCallback.item_id}" /></div>
	{/if}
	{if isset($bFeedIsParentItem)}
		<div><input type="hidden" name="val[parent_table_change]" value="{$sFeedIsParentItemModule}" /></div>
	{/if}
		{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id != Phpfox::getUserId()}
		<div><input type="hidden" name="val[parent_user_id]" value="{$aUser.user_id}" /></div>
		{/if}	
		<div class="activity_feed_form_holder">		
		
			<div id="activity_feed_upload_error" style="display:none;"><div class="error_message" id="activity_feed_upload_error_message"></div></div>
			
			<div class="global_attachment_holder_section" id="global_attachment_status" style="display:block;">
				<div id="global_attachment_status_value" style="display:none;">{if isset($aFeedCallback.module) || defined('PHPFOX_IS_USER_PROFILE')}{phrase var='feed.write_something'}{else}{phrase var='feed.what_s_on_your_mind'}{/if}</div>
				<textarea cols="60" rows="8" name="val[user_status]">{if isset($aFeedCallback.module) || defined('PHPFOX_IS_USER_PROFILE')}{phrase var='feed.write_something'}{else}{phrase var='feed.what_s_on_your_mind'}{/if}</textarea>
			</div>
			
			{foreach from=$aFeedStatusLinks item=aFeedStatusLink}
			{if !empty($aFeedStatusLink.module_block)}
			{module name=$aFeedStatusLink.module_block}			
			{/if}
			{/foreach}		
			{if Phpfox::isModule('egift')}
			{module name='egift.display'}
			{/if}
		</div>
		<div class="activity_feed_form_button">
			<div class="activity_feed_form_button_status_info">
				<textarea cols="60" rows="8" name="val[status_info]"></textarea>
			</div>
			<div class="activity_feed_form_button_position">
			
				{if defined('PHPFOX_IS_PAGES_VIEW') && $aPage.is_admin && $aPage.page_id != Phpfox::getUserBy('profile_page_id')}
				<div class="activity_feed_pages_post_as_page">
					{phrase var='feed.post_as'}: 
					<select name="custom_pages_post_as_page">
						<option value="{$aPage.page_id}">{$aPage.full_name|clean|shorten:20:'...'}</option>
						<option value="0">{$sGlobalUserFullName}</option>
					</select>
				</div>
				{else}
				{if !defined('PHPFOX_IS_USER_PROFILE') && (Phpfox::getParam('share.share_on_facebook') || Phpfox::getParam('share.share_on_twitter'))}
				<div id="activity_feed_share_this_one">					
					<a href="#" id="activity_feed_share_this_one_link">{phrase var='feed.share_this_on'}</a>
					<div class="feed_share_on_holder">						
						{if Phpfox::getParam('share.share_on_facebook')}
						<div class="feed_share_on_item"><a href="#" onclick="$(this).toggleClass('active'); $.ajaxCall('share.connect', 'connect-id=facebook', 'GET'); return false;">{img theme='layout/facebook.png' class='v_middle'} {phrase var='feed.facebook'}</a></div>
						{/if}
						{if Phpfox::getParam('share.share_on_twitter')}
						<div class="feed_share_on_item"><a href="#" onclick="$(this).toggleClass('active'); $.ajaxCall('share.connect', 'connect-id=twitter', 'GET'); return false;">{img theme='layout/twitter.png' class='v_middle'} {phrase var='feed.twitter'}</a></div>
						{/if}
						<div><input type="hidden" name="val[connection][facebook]" value="0" id="js_share_connection_facebook" class="js_share_connection" /></div>
						<div><input type="hidden" name="val[connection][twitter]" value="0" id="js_share_connection_twitter" class="js_share_connection" /></div>
					</div>					
				</div>
				{/if}
				{/if}
				
				<div class="activity_feed_form_button_position_button">
					<input type="submit" value="{phrase var='feed.share'}" class="button" />
				</div>				
				{if isset($aFeedCallback.module)}
				{else}
				{if !isset($bFeedIsParentItem) && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId()))}
				{module name='privacy.form' privacy_name='privacy' privacy_type='mini'}
				{/if}
				{/if}
				<div class="clear"></div>
			</div>
		</div>			
	</form>
	<div class="activity_feed_form_iframe"></div>
</div>
{/if}
{/if}

{if Phpfox::getParam('feed.refresh_activity_feed') > 0 && Phpfox::getLib('module')->getFullControllerName() == 'core.index-member'}
<div id="activity_feed_updates_link_holder">
    <a href="#" id="activity_feed_updates_link_single" class="activity_feed_updates_link" onclick="return $Core.loadMoreFeeds();">{phrase var='feed.1_new_update'}</a>
    <a href="#" id="activity_feed_updates_link_plural" class="activity_feed_updates_link" onclick="return $Core.loadMoreFeeds();">{phrase var='feed.span_id_js_new_update_view_span_new_updates'}</a>
</div>
{/if}

<div id="feed"><a name="feed"></a></div>
<div id="js_feed_content">
	{if $sCustomViewType !== null}
	<h2>{$sCustomViewType}</h2>
	{/if}
	<div id="js_new_feed_comment"></div>
	<div id="js_new_feed_update"></div>
	{foreach from=$aFeeds name=iFeed item=aFeed}
	
	{if isset($aFeed.feed_mini) && !isset($bHasRecentShow)}
	{if $bHasRecentShow = true}{/if}
	<div class="activity_recent_holder">
	<div class="activity_recent_title">
		{phrase var='feed.recent_activity'}
	</div>
	{/if}
	{if !isset($aFeed.feed_mini) && isset($bHasRecentShow)}
	</div>
	{unset var=$bHasRecentShow}
	{/if}
	
	<div class="js_feed_view_more_entry_holder">
		{template file='feed.block.entry'}
		{if isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
		{foreach from=$aFeed.more_feed_rows item=aFeed}
		{if $bChildFeed = true}{/if}
		<div class="js_feed_view_more_entry" style="display:none;">
			{template file='feed.block.entry'}
		</div>
		{/foreach}
		{unset var=$bChildFeed}
		{/if}
	</div>
	{/foreach}
	
	{if isset($bHasRecentShow)}
	</div>
	{/if}	
	{if $sCustomViewType === null}
	{if defined('PHPFOX_IN_DESIGN_MODE')}
	
	{else}
	{if count($aFeeds)}
	<div id="feed_view_more">
		<div id="js_feed_pass_info" style="display:none;">page={$iFeedNextPage}{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={$aUser.user_id}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}</div>
		<div id="feed_view_more_loader">{img theme='ajax/add.gif'}</div>
		<a href="{if Phpfox::getLib('module')->getFullControllerName() == 'core.index-visitor'}{url link='core.index-visitor' page=$iFeedNextPage}{else}{url link='current' page=$iFeedNextPage}{/if}" onclick="$(this).hide(); $('#feed_view_more_loader').show(); $.ajaxCall('feed.viewMore', 'page={$iFeedNextPage}{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={$aUser.user_id}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}', 'GET'); return false;" class="global_view_more no_ajax_link">{phrase var='feed.view_more'}</a>
	</div>
	{else}
	<br />
	<div class="message js_no_feed_to_show">{phrase var='feed.there_are_no_new_feeds_to_view_at_this_time'}</div>
	{/if}
	{/if}
	{/if}
{if !PHPFOX_IS_AJAX || (PHPFOX_IS_AJAX && count($aFeedVals))}
</div>
{/if}
{if Phpfox::getParam('feed.refresh_activity_feed') > 0 && Phpfox::getLib('module')->getFullControllerName() == 'core.index-member'}
<script type="text/javascript">
	$Core.reloadActivityFeed();
</script>
{/if} 