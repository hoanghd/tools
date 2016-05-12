<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_User
 * @version 		$Id: login-block.html.php 3089 2011-09-13 10:54:02Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{plugin call='user.template_controller_login_block__start'}
<form method="post" action="{url link="user.login"}">
	<div class="p_top_4">
		<label for="js_email">{if Phpfox::getParam('user.login_type') == 'user_name'}{phrase var='user.user_name'}{elseif Phpfox::getParam('user.login_type') == 'email'}{phrase var='user.email'}{else}{phrase var='user.login'}{/if}</label>:
		<div class="p_4">
			<input type="text" name="val[login]" id="js_email" value="" size="30" style="width:90%;" />
		</div>
	</div>
	
	<div class="p_top_4">
		<label for="js_password">{phrase var='user.password'}:</label> 
		<div class="p_4">
			<input type="password" name="val[password]" id="js_password" value="" size="30" style="width:90%;" />
			<div class="p_top_8">
				<label><input type="checkbox" name="val[remember_me]" value="" class="checkbox" /> {phrase var='user.remember'}</label>
			</div>
		</div>
	</div>
	
	<div class="p_top_8">
		<input type="submit" value="{phrase var='user.login_button'}" class="button" />
	</div>
</form>
{if (Phpfox::isModule('facebook') && Phpfox::getParam('facebook.enable_facebook_connect')) || (Phpfox::isModule('janrain') && Phpfox::getParam('janrain.enable_janrain_login'))}
<div class="p_top_8">	
	or login with:
	{if Phpfox::isModule('facebook') && Phpfox::getParam('facebook.enable_facebook_connect')}
	<div class="header_login_block"><div class="fbconnect_button"><fb:login-button perms="publish_stream,email,user_birthday" v="2"></fb:login-button></div></div>
	{/if}
	{if Phpfox::isModule('janrain') && Phpfox::getParam('janrain.enable_janrain_login')}
	<div class="header_login_block">
		<a class="rpxnow" onclick="return false;" href="{$sJanrainUrl}">{img theme='layout/janrain-icons.png'}</a>
	</div>
	{/if}
</div>
{/if}
{plugin call='user.template_controller_login_block__end'}