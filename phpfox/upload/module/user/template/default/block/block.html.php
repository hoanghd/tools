<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: block.html.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsBlocked}
{phrase var='user.you_have_already_blocked_this_user'}
<br />
{phrase var='user.would_you_like_to_unblock_user_info' user_info=$aUser|user}
<ul class="action">
	<li><a href="#" onclick="$.ajaxCall('user.unBlock', 'user_id={$aUser.user_id}'); return false;">{phrase var='user.yes_unblock_this_user'}</a></li>
	<li><a href="#" onclick="tb_remove(); return false;">{phrase var='user.no_do_not_unblock_this_user'}</a></li>
</ul>
{else}
{phrase var='user.are_you_sure_you_want_to_block_user_info' user_info=$aUser|user}
<ul class="action">
	<li><a href="#" onclick="$.ajaxCall('user.processBlock', 'user_id={$aUser.user_id}'); return false;">{phrase var='user.yes_block_this_user'}</a></li>
	<li><a href="#" onclick="tb_remove(); return false;">{phrase var='user.no_do_not_block_this_user'}</a></li>
</ul>
{/if}