<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: mini.html.php 2760 2011-07-27 13:39:18Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aFriends)}
<div class="block_listing_inline">
	<ul>
{foreach from=$aFriends name=friend item=aFriend}
		<li>		
			{img user=$aFriend suffix='_50_square' max_width=32 max_height=32 class='js_hover_title'}		
		</li>	
{/foreach}
	</ul>
	<div class="clear"></div>
</div>
{else}
<div class="extra_info">
	No friends online
</div>
{/if}