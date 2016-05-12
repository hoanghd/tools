<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: search.html.php 2860 2011-08-20 19:17:52Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !$bSearch}
<script type="text/javascript">
	var sPrivacyInputName = '{$sPrivacyInputName}';
	var sSearchByValue = '';
{literal}
	$Behavior.searchFriendBlock = function()
	{
		sSearchByValue = $('.js_is_enter').val();		

		if ($.browser.mozilla) 
		{
			$('.js_is_enter').keypress(checkForEnter);
		} 
		else 
		{
			$('.js_is_enter').keydown(checkForEnter);
		}		
	};

	updateCheckBoxes();
	
	function updateFriendsList()
	{		
		updateCheckBoxes();
	}
	
	function removeFromSelectList(sId)
	{
		$('.js_cached_friend_id_' + sId + '').remove();
		$('#js_friends_checkbox_' + sId).attr('checked', false);
		$('#js_friend_input_' + sId).remove();
		$('.js_cached_friend_id_' + sId).remove(); return false;		
		
		return false;
	}
	
	function addFriendToSelectList(oObject, sId)
	{		
		if (oObject.checked)
		{
			iCnt = 0;
			$('.js_cached_friend_name').each(function()
			{			
				iCnt++;
			});			

			if (function_exists('plugin_addFriendToSelectList'))
			{
				plugin_addFriendToSelectList(sId);
			}
			{/literal}
			$('#js_selected_friends').append('<div class="js_cached_friend_name row1 js_cached_friend_id_' + sId + '' + (iCnt ? '' : ' row_first') + '"><span style="display:none;">' + sId + '</span><input type="hidden" name="val[' + sPrivacyInputName + '][]" value="' + sId + '" /><a href="#" onclick="return removeFromSelectList(' + sId + ');">{img theme='misc/delete.gif' class="delete_hover v_middle"}</a> ' + $('#js_friend_' + sId + '').html() + '</div>');			
			{literal}
		}
		else
		{
			if (function_exists('plugin_removeFriendToSelectList'))
			{
				plugin_removeFriendToSelectList(sId);
			}			
			
			$('.js_cached_friend_id_' + sId).remove();
			$('#js_friend_input_' + sId).remove();
		}
	}
	
	function cancelFriendSelection()
	{
		if (function_exists('plugin_cancelFriendSelection'))
		{
			plugin_cancelFriendSelection();
		}			
		
		$('#js_selected_friends').html('');	
		$Core.loadInit(); 
		tb_remove();
	}
	
	function updateCheckBoxes()
	{
		iCnt = 0;
		$('.js_cached_friend_name').each(function()
		{			
			iCnt++;
			$('#js_friends_checkbox_' + $(this).find('span').html()).attr('checked', true);
		});
		
		$('#js_selected_count').html((iCnt / 2));
	}
	
	function showLoader()
	{
		$('#js_friend_search_content').html($.ajaxProcess(oTranslations['friend.loading'], 'large'));
	}	
	
	function checkForEnter(event)
	{
		if (event.keyCode == 13) 
		{
			showLoader(); 
			
			$.ajaxCall('friend.searchAjax', 'find=' + $('#js_find_friend').val() + '&amp;input=' + sPrivacyInputName + '');
		
			return false;	
		}
	}
{/literal}
</script>
<div id="js_friend_loader_info"></div>
<div id="js_friend_loader">
{if $sFriendType != 'mail'}
	<div class="p_4">
		<div class="go_left">
			{phrase var='friend.view'}:&nbsp;<select name="view" onchange="showLoader(); $(this).ajaxCall('friend.searchAjax', 'input={$sPrivacyInputName}'); return false;">
				<option value="all">{phrase var='friend.all_friends'}</option>
				<option value="online"{if $sView == 'online'} selected="selected"{/if}>{phrase var='friend.online_friends'}</option>
				{if count($aLists)}
				<optgroup label="{phrase var='friend.friends_list'}">
				{foreach from=$aLists item=aList}
					<option value="{$aList.list_id}"{if $sView == $aList.list_id} selected="selected"{/if}>{$aList.name|clean|split:30}</option>
				{/foreach}
				</optgroup>
				{/if}
			</select>
		</div>
		<div class="t_right">
			<input type="text" class="js_is_enter v_middle default_value" name="find" value="{phrase var='friend.search_by_email_full_name_or_user_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_friend" size="30" />
			<input type="button" value="{phrase var='friend.find'}" onclick="showLoader(); $.ajaxCall('friend.searchAjax', 'friend_module_id={$sFriendModuleId}&amp;friend_item_id={$sFriendItemId}&amp;find=' + $('#js_find_friend').val() + '&amp;input={$sPrivacyInputName}'); return false;" class="button v_middle" />
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="main_break"></div>
	<div class="separate"></div>
	
{else}	
			<input type="text" class="js_is_enter v_middle default_value" name="find" value="{phrase var='friend.search_by_email_full_name_or_user_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_friend" size="30" />
			<input type="button" value="{phrase var='friend.find'}" onclick="showLoader(); $.ajaxCall('friend.searchAjax', 'friend_module_id={$sFriendModuleId}&amp;friend_item_id={$sFriendItemId}&amp;find=' + $('#js_find_friend').val() + '&amp;input={$sPrivacyInputName}&amp;type={$sFriendType}'); return false;" class="button v_middle" />	

	<div class="main_break"></div>
	<div class="separate"></div>		
{/if}
	
	<div class="t_center">
		{foreach from=$aLetters item=sLetter}<span style="padding-right:5px;"><a href="#" onclick="showLoader(); $.ajaxCall('friend.searchAjax', 'letter={$sLetter}&amp;input={$sPrivacyInputName}&amp;type={$sFriendType}'); return false;"{if $sActualLetter == $sLetter} style="text-decoration:underline;"{/if}>{$sLetter}</a></span>{/foreach}
	</div>
	<div class="main_break"></div>
	
	<div class="separate"></div>

{/if}
	<div id="js_friend_search_content">
		{pager}
		<div class="main_break"></div>
		<div class="label_flow" style="height:200px;">
			{foreach from=$aFriends name=friend item=aFriend}
			<div class="{if is_int($phpfox.iteration.friend/2)}row1{else}row2{/if}{if $phpfox.iteration.friend == 1} row_first{/if}{if isset($aFriend.is_active)} row_moderate{/if}">
			{if !isset($aFriend.is_active)}
				<span><input type="checkbox" class="checkbox" name="friend[]" class="js_friends_checkbox" id="js_friends_checkbox_{$aFriend.user_id}" value="{$aFriend.user_id}" {if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false}DISABLED {else} onclick="addFriendToSelectList(this, '{$aFriend.user_id}');"{/if} style="vertical-align:middle;" /></span>
			{/if}
				<span id="js_friend_{$aFriend.user_id}">{$aFriend|user}{if isset($aFriend.is_active)} <em>({$aFriend.is_active})</em>{/if}{if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false} {phrase var='friend.cannot_select_this_user'}{/if}</span>
			</div>
			{foreachelse}
			<div class="extra_info">
			{if $sFriendType == 'mail'}
				{phrase var='user.sorry_no_members_found'}
			{else}
				{phrase var='friend.sorry_no_friends_were_found'}
			{/if}
			</div>
			{/foreach}
		</div>
	</div>
{if !$bSearch}
	{if $bIsForShare}
	
	{else}
	{if $sPrivacyInputName != 'invite'}
	<div class="main_break t_right">		
		<input type="button" name="submit" value="{phrase var='friend.use_selected'}" onclick="{literal}if (function_exists('plugin_selectSearchFriends')) { plugin_selectSearchFriends(); } else { $Core.loadInit(); tb_remove(); }{/literal}" class="button" />&nbsp;<input type="button" name="cancel" value="{phrase var='friend.cancel'}" onclick="{literal}if (function_exists('plugin_cancelSearchFriends')) { plugin_cancelSearchFriends(); } else { cancelFriendSelection(); }{/literal}" class="button" />		
	</div>
	{/if}
	{/if}
</div>
{/if}