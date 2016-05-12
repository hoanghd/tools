<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: friend.html.php 2621 2011-05-22 20:09:22Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !$bNoCustomDiv}
<div id="js_custom_friend_list">
{/if}
	{if count($aLists)}
	<div class="error_message" style="display:none;" id="js_temp_privacy_error_message">
		Select a custom friends' list if you want to add privacy to your item.	
	</div>	
	{if $iNewListId > 0}
	<div class="message">
		Custom friends list successfully created.
	</div>
	{/if}
	<div id="js_custom_list_actual_holder">
		<form method="post" action="#" onsubmit="return $Core.updateCustomList(this);">
			Select from your custom friends list.
			<div class="p_top_4">
				<select name="custom_list[]" multiple="multiple" size="6" style="width:300px;" class="js_custom_list_option">
				{foreach from=$aLists item=aList}
					<option id="pcl_{$aList.list_id}" value="{$aList.list_id}"{if $iNewListId > 0 && $iNewListId == $aList.list_id} selected="selected"{/if}>{$aList.name|clean}</option>
				{/foreach}
				</select>
			</div>
			<div class="p_top_4">
				<input type="submit" class="button" value="Save" /> <a href="#" onclick="$('#js_create_custom_friend_list_holder').show(); $('#js_custom_list_actual_holder').hide(); return false;">or create a new list</a>
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
		var sCustomPrivacyId = '#js_custom_privacy_input_holder';			
		var sPrivacyArray = '{$sPrivacyArray}';		
		{if !empty($sCustomPrivacyId)}
		sCustomPrivacyId = '#{$sCustomPrivacyId}';
		{/if}
	{literal}
	
	$(sCustomPrivacyId + ' .privacy_list_array').each(function()
	{
		if ($('.js_custom_list_option #pcl_' + this.value).length > 0)
		{
			$('.js_custom_list_option #pcl_' + this.value).attr('selected', 'selected');
		}
	});
	
	$Core.updateCustomList = function($oObj)
	{	
		$('#js_temp_privacy_error_message').hide();
		var $iCnt = 0;
		$('.js_custom_list_option option').each(function()
		{
			if (this.selected)
			{
				$iCnt++;
			}
		});
		
		if (!$iCnt)
		{
			$('#js_temp_privacy_error_message').show();
		}
		else
		{
			$(sCustomPrivacyId).html('');
			$($oObj).find('.js_custom_list_option option').each(function()	
			{
				if (this.selected)
				{
					$(sCustomPrivacyId).append('<div><input type="hidden" name="val' + (empty(sPrivacyArray) ? '' : '[' + sPrivacyArray + ']') + '[privacy_list][]" value="' + this.value + '" class="privacy_list_array" /></div>');
				}
			});
			
			tb_remove();
		}
		
		return false;
	}
	</script>
	{/literal}
	{/if}
	
	<div id="js_create_custom_friend_list_holder"{if count($aLists)} style="display:none;"{/if}>
		<div id="js_create_custom_friend_list">
			{if !count($aLists)}
			You have not created a custom friends' list yet. Create one below to control your custom privacy settings.
			{else}
			Create a new friends list to fully control your contents privacy.
			{/if}
			<div style="margin-top:10px;">
				<div id="js_custom_search_friend_holder"></div>
				<form method="post" action="#" onsubmit="return false;">
					<div><input type="text" name="name" value="{phrase var='friend.create_new_list'}" maxlength="255" size="15" onclick="if (this.value == '{phrase var='friend.create_new_list' phpfox_squote=true}') this.value = '';" onblur="if (this.value == '') this.value = '{phrase var='friend.create_new_list' phpfox_squote=true}';" id="js_add_new_list" style="vertical-align:middle;" /> <input type="submit" value="{phrase var='friend.add'}" class="button" onclick="if ($('#js_add_new_list').val() != '') $('#js_add_new_list').ajaxCall('friend.addList', 'custom=true'); " /></div>
				</form>		
			</div>
		</div>
		
		<div id="js_add_friends_to_list" style="display:none;">
			Add friends' to your custom list below.
			<div style="margin-top:10px;">
				<form method="post" action="#" onsubmit="return false;">
					<div><input type="hidden" name="list_id" value="" id="js_custom_friend_list_id" /></div>
					<div class="go_left" style="margin-right:5px;">
						<div id="js_custom_search_friend"></div>
					</div>
					<div>
						<div id="js_custom_search_friend_placement"></div>
						<div id="js_custom_search_submit_button" class="p_top_4 t_right" style="display:none;">
							<input type="button" class="button" value="Save" onclick="$(this).parents('form').ajaxCall('friend.addFriendsToList');" />
						</div>
					</div>
					<div class="clear"></div>
				</form>
			</div>
		</div>
		
		{literal}
		<script type="text/javascript">
			$Core.searchFriends({
				'id': '#js_custom_search_friend',
				'placement': '#js_custom_search_friend_placement',
				'width': '300px',
				'max_search': 10,
				'input_name': 'friends',
				'default_value': 'Search friends by their name...',
				'onclick': function()
				{
					$('#js_custom_search_submit_button').show();
					
					return false;
				}
			});		
		</script>
		{/literal}
	</div>
	
{if !$bNoCustomDiv}
</div>
{/if}