<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: profile.html.php 2689 2011-06-23 12:10:46Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aFriends)}
{foreach from=$aFriends name=friend item=aFriend}
<div id="js_friend_{$aFriend.friend_id}" class="{if is_int($phpfox.iteration.friend/2)}row1{else}row2{/if}{if $phpfox.iteration.friend == 1} row_first{/if}">
	<div class="t_center" style="width:80px; float:left;">
		{img user=$aFriend suffix='_75_square' max_width=75 max_height=75}
	</div>
	<div style="margin-left:85px;">
		<span class="row_title_link">{$aFriend|user}</span> <span class="row_title_status">{$aFriend.status|clean|shorten:75:'...'}</span>
	</div>
	<div class="clear"></div>
</div>
{/foreach}

{pager}
{else}

{if $sFriendView == 'online'}
<div class="extra_info">
	{phrase var='friend.no_friends_online'}
</div>
{else}

{if $aUser.user_id == Phpfox::getUserId()}
<div class="extra_info">{phrase var='friend.you_have_not_added_any_friends_yet'}</div>
<ul class="action">
	<li><a href="{url link='friend.find'}">{phrase var='friend.search_for_friends'}</a></li>
	<li><a href="{url link='user.browse'}">{phrase var='friend.browse_members'}</a></li>
</ul>
{else}
<div class="extra_info">{phrase var='friend.user_link_has_not_added_any_friends' user=$aUser}</div>
<ul class="action">	
	<li><a href="{url link='user.browse'}">{phrase var='friend.browse_other_members'}</a></li>
</ul>
{/if}

{/if}

{/if}