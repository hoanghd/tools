<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: menu.html.php 3332 2011-10-20 12:50:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="pages_view_sub_menu">
	<ul>
		{if $aPage.is_admin}
		<li><a href="{url link='pages.add' id=$aPage.page_id}">{phrase var='pages.edit_page'}</a></li>		
		{/if}
		{module name='share.link' type='pages' url=$aPage.link title=$aPage.title display='menu'}
		{if !Phpfox::getUserBy('profile_page_id')}
		<li id="js_add_pages_unlike" {if !$aPage.is_liked} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#pages_like_join_position').show(); $.ajaxCall('like.delete', 'type_id=pages&amp;item_id={$aPage.page_id}'); return false;">{if $aPage.page_type == '1'}{phrase var='pages.remove_membership'}{else}{phrase var='pages.unlike'}{/if}</a></li>		
		{/if}
	</ul>
</div>
	