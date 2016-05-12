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
<div class="profile_image">
    <div class="profile_image_holder">
		{if $aPage.is_app}
		{img server_id=0 path='app.url_image' file=$aPage.aApp.image_path suffix='_200' max_width='175' max_height='300' title=$aPage.aApp.app_title}
		{else}
		{img thickbox=true server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200' max_width='175' max_height='300'}
		{/if}
	</div>
</div>
{if $bCanViewPage}
<div class="sub_section_menu">
	<ul>		
		{foreach from=$aPageLinks item=aPageLink}
			<li class="{if isset($aPageLink.is_selected)} active{/if}">
				<a href="{url link=$aPageLink.url}" class="ajax_link"{if isset($aPageLink.icon)} style="background-image:url('{img theme=$aPageLink.icon' return_url=true}');"{/if}>{$aPageLink.phrase}{if isset($aPageLink.total)}<span>({$aPageLink.total|number_format})</span>{/if}</a>				
			</li>
		{/foreach}
	</ul>
    <div class="clear"></div>
</div>
{/if}