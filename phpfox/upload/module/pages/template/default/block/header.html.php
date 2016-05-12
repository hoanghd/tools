<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="profile_header">
	<div id="section_menu">	
	{if isset($bIsPagesViewSection)}		
		<ul>
			{foreach from=$aSubPageMenus item=aSubPageMenu}
			<li><a href="{$aSubPageMenu.url}">{$aSubPageMenu.phrase}</a></li>
			{/foreach}
		</ul>			
	{else}
		{if $aPage.is_app || !$aPage.is_admin}
		<ul>
			{if $aPage.is_app}
			<li><a href="{permalink module='apps' id=$aPage.app_id title=$aPage.title}">{phrase var='pages.go_to_app'}</a></li>
			{/if}
			{if !$aPage.is_admin}
			<li><a href="{url link='pages.add'}">{phrase var='pages.create_a_page'}</a></li>		
			{/if}
		</ul>
		{/if}
	{/if}	
	</div>
	
	<h1><a href="{$aPage.link}" title="{$aPage.title|clean}">{$aPage.title|clean|split:50|shorten:40:'...'}</a>{if !Phpfox::getUserBy('profile_page_id')}{if $aPage.reg_method == '2' && !isset($aPage.is_invited) && $aPage.page_type == '1'}{else}{if isset($aPage.is_reg) && $aPage.is_reg}{else}<span id="pages_like_join_position"{if $aPage.is_liked} style="display:none;"{/if}><a href="#" id="pages_like_join" onclick="$(this).parent().hide(); $('#js_add_pages_unlike').show(); {if $aPage.page_type == '1' && $aPage.reg_method == '1'} $.ajaxCall('pages.signup', 'page_id={$aPage.page_id}'); {else}$.ajaxCall('like.add', 'type_id=pages&amp;item_id={$aPage.page_id}');{/if} return false;">{if $aPage.page_type == '1'}{phrase var='pages.join'}{else}{phrase var='pages.like'}{/if}</a></span>{/if}{/if}{/if}</h1>
	<div class="profile_info">
		{$aPage.category_name|convert}
	</div>
</div>