<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: breadcrumb.html.php 2845 2011-08-18 08:06:52Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aBreadCrumbs)}
<div id="breadcrumb_holder"{if !$bIsUsersProfilePage && count($aSubMenus)} class="has_section_menu"{/if}>
	<div id="breadcrumb_content">
		{if empty($aBreadCrumbTitle)}
		{foreach from=$aBreadCrumbs key=sLink item=sCrumb name=link}
		{if $phpfox.iteration.link == 1}
		{if count($aBreadCrumbTitle)}<div class="h1">{else}<h1>{/if}{if !empty($sLink)}<a href="{$sLink}" class="ajax_link">{/if}{$sCrumb|clean}{if !empty($sLink)}</a>{/if}{if count($aBreadCrumbTitle)}</div>{else}</h1>{/if}
		{/if}
		{/foreach}			
		{/if}
		{breadcrumb_list}
	</div>	
	 {breadcrumb_menu}	
</div>
{/if}