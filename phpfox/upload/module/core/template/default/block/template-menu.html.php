<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: template-menu.html.php 3197 2011-09-27 12:37:51Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
						{if Phpfox::getUserBy('profile_page_id') <= 0}
						<ul>		
							{foreach from=$aMenus key=iKey item=aMenu name=menu}
								<li {if ($aMenu.url == 'apps' && count($aInstalledApps)) || (isset($aMenu.children) && count($aMenu.children))}class="explore"{/if}><a href="{url link=$aMenu.url}" class="ajax_link">
									{phrase var=$aMenu.module'.'$aMenu.var_name}{if isset($aMenu.suffix)}{$aMenu.suffix}{/if}</a>	
									{if isset($aMenu.children) && count($aMenu.children)}
									<ul>
										{foreach from=$aMenu.children item=aChild name=child_menu}
										<li{if $phpfox.iteration.child_menu == 1} class="first"{/if}><a href="{url link=$aChild.url}">{phrase var=$aChild.module'.'$aChild.var_name}</a></li>
										{/foreach}
									</ul>
									{else}								
									{if $aMenu.url == 'apps' && count($aInstalledApps)}
									<ul>
										{foreach from=$aInstalledApps item=aInstalledApp}
										<li><a href="{permalink module='apps' id=$aInstalledApp.app_id title=$aInstalledApp.app_title}">{img server_id=0 path='app.url_image' file=$aInstalledApp.image_path suffix='_square' max_width=16 max_height=16 title=$aInstalledApp.app_title class='v_middle'} {$aInstalledApp.app_title|clean}</a></li>
										{/foreach}
									</ul>
									{/if}
									{/if}
								</li>
							{/foreach}	
							{unset var=$aMenus var=$aMenu}
							{if count($aAppMenus)}
							<li class="explore"><a href="#" onclick="return false;">{phrase var='core.explore'} {img theme='layout/header_menu_explore_drop.png' class='v_middle'}</a>
								<ul>
								{foreach from=$aAppMenus key=iAppKey item=aAppMenu name=app_menu}
									<li><a href="{url link=$aAppMenu.url}" class="ajax_link">{phrase var=$aAppMenu.module'.'$aAppMenu.var_name}</a></li>
								{/foreach}
								</ul>
							</li>
							{/if}
							{unset var=$aAppMenus}							
						</ul>
						{/if}