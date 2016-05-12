<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: mobile.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="mobile_profile_header">
	<div id="mobile_profile_photo">
		<div id="mobile_profile_photo_image">
			{$sProfileImage}
		</div>
		<div id="mobile_profile_photo_name">
			{$aUser.full_name|clean|split:50}
			<ul>
			{if Phpfox::getUserId() != $aUser.user_id}
				{if Phpfox::isModule('mail') && Phpfox::getService('user.privacy')->hasAccess('' . $aUser.user_id . '', 'mail.send_message')}
					<li><a href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aUser.user_id}{right_curly}); return false;">{phrase var='profile.message'}</a></li>
				{/if}
				{if Phpfox::isModule('friend') && !$aUser.is_friend}
					<li id="js_add_friend_on_profile"><a href="#" onclick="return $Core.addAsFriend('{$aUser.user_id}');" title="{phrase var='profile.add_to_friends'}">{phrase var='profile.add_to_friends'}</a></li>
				{/if}
				{if $bCanPoke && Phpfox::getService('user.privacy')->hasAccess('' . $aUser.user_id . '', 'poke.can_send_poke')}
					<li id="liPoke">
						<a href="#" id="section_poke" onclick="$Core.box('poke.poke', 400, 'user_id={$aUser.user_id}'); return false;">{phrase var='poke.poke' full_name=''}</a>
					</li>
				{/if}			
			{/if}
			</ul>
			<div class="clear"></div>				
		</div>			
	</div>
	<ul class="mobile_profile_header_menu">
		<li><a href="{url link=$aUser.user_name}"{if !$bIsInfo} class="active"{/if}>{phrase var='profile.wall'}</a></li>			
		<li><a href="{url link=$aUser.user_name'.info'}"{if $bIsInfo} class="active"{/if}>{phrase var='profile.info'}</a></li>
	</ul>
	<div class="clear"></div>
</div>