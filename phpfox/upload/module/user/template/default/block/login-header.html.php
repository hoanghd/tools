<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_User
 * @version 		$Id: login-header.html.php 3089 2011-09-13 10:54:02Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
								{if Phpfox::getLib('module')->getFullControllerName() != 'user.login'}
								{plugin call='user.template.login_header_set_var'}
								<div id="header_menu_login">
									{if isset($bCustomLogin)}
									<div id="header_menu_login_holder">
									{/if}
										<form method="post" action="{url link='user.login'}">
											<div class="header_menu_login_left">
												<div class="header_menu_login_label">{if Phpfox::getParam('user.login_type') == 'user_name'}{phrase var='user.user_name'}{elseif Phpfox::getParam('user.login_type') == 'email'}{phrase var='user.email'}{else}{phrase var='user.login'}{/if}:</div>
												<div><input type="text" name="val[login]" value="" class="header_menu_login_input" tabindex="1" /></div>
												<div class="header_menu_login_sub">
													<label><input type="checkbox" name="val[remember_me]" value="" checked="checked" tabindex="4" /> Keep me logged in</label>
												</div>
											</div>
											<div class="header_menu_login_right">
												<div class="header_menu_login_label">Password:</div>
												<div><input type="password" name="val[password]" value="" class="header_menu_login_input" tabindex="2" /></div>
												<div class="header_menu_login_sub">
													<a href="{url link='user.password.request'}">Forgot your password?</a>
												</div>
											</div>
											<div class="header_menu_login_button">
												<input type="submit" value="Login" tabindex="3" />
											</div>
										</form>
									{if isset($bCustomLogin)}
									</div>									
									<div id="header_menu_login_custom">										
										or login with:										
										{if Phpfox::isModule('facebook') && Phpfox::getParam('facebook.enable_facebook_connect')}
										<div class="header_login_block">
											<fb:login-button perms="publish_stream,email,user_birthday" v="2"></fb:login-button>
										</div>
										{/if}
										{if Phpfox::isModule('janrain') && Phpfox::getParam('janrain.enable_janrain_login')}
										<div class="header_login_block">
											<a class="rpxnow" onclick="return false;" href="{$sJanrainUrl}">{img theme='layout/janrain-icons.png'}</a>
										</div>
										{/if}
										{plugin call='user.template.login_header_custom'}
									</div>
									{/if}
								</div>
								<script type="text/javascript">
								{literal}
									$Behavior.focusOnLogin = function()
									{
										$('.header_menu_login_input:first').focus();
									}
								{/literal}
								</script>
								{/if}