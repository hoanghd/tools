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
{if count($aInstalledApps)}
<div class="global_apps_title">
	{phrase var='apps.apps'}
</div>
<div class="sub_section_menu global_apps_title_padding">
	<ul>
	{foreach from=$aInstalledApps item=aInstalledApp}
		<li><a href="{permalink module='apps' id=$aInstalledApp.app_id title=$aInstalledApp.app_title}">{img server_id=0 path='app.url_image' file=$aInstalledApp.image_path suffix='_square' max_width=16 max_height=16 title=$aInstalledApp.app_title class='v_middle'} {$aInstalledApp.app_title|clean}</a></li>
	{/foreach}
	</ul>
</div>
{/if}

{if count($aInstalledPages)}
<div class="global_apps_title">
	{phrase var='pages.pages'}
</div>
<div class="sub_section_menu global_apps_title_padding">
	<ul>
	{foreach from=$aInstalledPages item=aInstalledPage}
		<li><a href="{$aInstalledPage.link}" title="{$aInstalledPage.title}">{img server_id=$aInstalledPage.user_server_id title=$aInstalledPage.title path='core.url_user' file=$aInstalledPage.user_image suffix='_50_square' max_width='16' max_height='16' class='v_middle'} {$aInstalledPage.title|clean|shorten:22:'...'}</a></li>
	{/foreach}
	</ul>
</div>
{/if}