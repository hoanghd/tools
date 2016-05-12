<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: template-menuaccount.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
									{if Phpfox::getUserBy('profile_page_id') > 0}								
									<ul>
										<li>
											<a href="#" class="has_drop_down">Account</a>
											<ul>
												<li class="header_menu_user_link">
													<div id="header_menu_user_image">
														{img user=$aGlobalUser suffix='_50_square' max_width=50 max_height=50}
													</div>
													<div id="header_menu_user_profile">
														{$aGlobalUser|user|split:10|shorten:20:'...'}
													</div>
												</li>		
												<li class="header_menu_user_link_page">
													<a href="#" onclick="$.ajaxCall('pages.logBackIn'); return false;">
														<div class="page_profile_image">
															{img user=$aGlobalProfilePageLogin suffix='_50_square' max_width=32 max_height=32 no_link=true}
														</div>
														<div class="page_profile_user">
															{phrase var='core.log_back_in_as_global_full_name' global_full_name=$aGlobalProfilePageLogin.full_name|clean}														
														</div>
													</a>
												</li>
												<li><a href="{url link='pages.add' id=$iGlobalProfilePageId}">{phrase var='core.edit_page'}</a></li>
											</ul>
										</li>										
									</ul>
									{else}
									<ul>										
									{foreach from=$aRightMenus key=iKey item=aMenu}
										<li><a href="{url link=$aMenu.url}"{if isset($aMenu.children) && count($aMenu.children) && is_array($aMenu.children)} class="has_drop_down no_ajax_link"{/if}>{phrase var=$aMenu.module'.'$aMenu.var_name}{if isset($aMenu.suffix)}{$aMenu.suffix}{/if} </a>					
											{if isset($aMenu.children) && count($aMenu.children) && is_array($aMenu.children)}
											<ul>
												{if Phpfox::isUser(true) && $aMenu.url == 'user.setting'}
												<li class="header_menu_user_link">
													<div id="header_menu_user_image">
														{img user=$aGlobalUser suffix='_50_square' max_width=50 max_height=50}
													</div>
													<div id="header_menu_user_profile">
														{$aGlobalUser|user}
													</div>
												</li>
												<li><a href="#" onclick="$Core.box('pages.login', 400); return false;">{phrase var='core.login_as_page'}</a></li>
												{/if}
												{foreach from=$aMenu.children item=aChild name=child_menu}
												<li{if $phpfox.iteration.child_menu == 1} class="first"{/if}><a {if $aChild.url == 'pages.login'}id="js_login_as_page"{/if} href="{url link=$aChild.url}"{if $aChild.url == 'profile.designer' || $aChild.url == 'pages.login'} class="no_ajax_link"{/if}>{phrase var=$aChild.module'.'$aChild.var_name}</a></li>
												{/foreach}
											</ul>
											{/if}
										</li>
									{/foreach}
									{unset var=$aRightMenus var1=$aMenu}
									</ul>
									{/if}