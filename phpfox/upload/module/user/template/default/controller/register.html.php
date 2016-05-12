<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_User
 * @version 		$Id: register.html.php 3351 2011-10-25 09:45:20Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script type="text/javascript">
$Behavior.termsAndPrivacy = function()
{
	$('#js_terms_of_use').click(function()
	{
		{/literal}
		tb_show('{phrase var='user.terms_of_use' phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true}', $.ajaxBox('page.view', 'height=410&width=600&title=terms')); 
		{literal}
		return false;
	});
	
	$('#js_privacy_policy').click(function()
	{
		{/literal}
		tb_show('{phrase var='user.privacy_policy' phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true}', $.ajaxBox('page.view', 'height=410&width=600&title=policy')); 
		{literal}
		return false;
	});
}
</script>
{/literal}

{if Phpfox::getLib('module')->getFullControllerName() == 'user.register' && Phpfox::isModule('invite')}
<div id="main_registration_form">

	<h1>Sign Up for {param var='core.site_title'}</h1>
	<div class="extra_info">
		Join {param var='core.site_title'} to connect with friends, share photos and create your own profile.
	</div>
	<div id="main_registration_form_holder">
		{if ((Phpfox::isModule('facebook') && Phpfox::getParam('facebook.enable_facebook_connect')) || (Phpfox::isModule('janrain') && Phpfox::getParam('janrain.enable_janrain_login'))) && !Phpfox::getService('invite')->isInviteOnly()}
		<div id="main_registration_custom">
			or sign up with:
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
		</div>
		{/if}
{/if}
{if Phpfox::getLib('module')->getFullControllerName() != 'user.register'}
<div class="user_register_holder">
	<div class="holder">
		<div class="user_register_intro">		
			{module name='user.welcome'}
		</div>
		<div class="user_register_form">

			{if Phpfox::getParam('user.allow_user_registration')}
			<div class="user_register_title">
				Sign Up
				<div class="extra_info">
					It's free and always will be.
				</div>
			</div>
			{/if}
{/if}
		{if Phpfox::isModule('invite') && Phpfox::getService('invite')->isInviteOnly()}
		<div class="main_break">
			<div class="extra_info">
				{param var='core.site_title'} is an invite only community. Enter your email below if you have received an invitation. 
			</div>
			<div class="main_break">
				<form method="post" action="{url link='user.register'}">
					<div class="table">
						<div class="table_left">
							Email:
						</div>
						<div class="table_right">
							<input type="text" name="val[invite_email]" value="{if !empty($sUserEmailCookie)}{$sUserEmailCookie|clean}{/if}" />
						</div>
					</div>
					<div class="table_clear">
						<input type="submit" value="Submit" class="button_register" />
					</div>
				</form>
			</div>
		</div>
		{else}
		{if isset($sCreateJs)}
		{$sCreateJs}
		{/if}
		<div id="js_registration_process" class="t_center" style="display:none;">
			<div class="p_top_8">				
				{img theme='ajax/add.gif' alt=''}
			</div>
		</div>
		<div id="js_signup_error_message" style="width:350px;"></div>
		{if Phpfox::getParam('user.allow_user_registration')}
			<div class="main_break" id="js_registration_holder">	
				{if Phpfox::getParam('user.multi_step_registration_form') && !isset($bIsPosted)}
				<form method="post" action="{url link='user.register'}" id="js_form" enctype="multipart/form-data">
				{else}
				<form method="post" action="{url link='user.register'}" id="js_form"{if isset($sGetJsForm)} onsubmit="{$sGetJsForm}"{/if} enctype="multipart/form-data">
				{/if}		
				{token}

					<div id="js_signup_block">
						{if isset($bIsPosted) || !Phpfox::getParam('user.multi_step_registration_form')}
						<div>
							{template file='user.block.register.step1'}
							{template file='user.block.register.step2'}
						</div>
						{else}
							{template file='user.block.register.step1'}			
						{/if}
					</div>		
			
					{if Phpfox::isModule('captcha') && Phpfox::getParam('user.captcha_on_signup')}
					<div id="js_register_capthca_image"{if Phpfox::getParam('user.multi_step_registration_form') && !isset($bIsPosted)} style="display:none;"{/if}>
						{module name='captcha.form'}
					</div>
					{/if}			
					
					{if Phpfox::getParam('user.new_user_terms_confirmation')}
					<div id="js_register_accept">
						<div class="table">			
							<div class="table_clear">
								<input type="checkbox" name="val[agree]" id="agree" value="1" class="checkbox v_middle" {value type='checkbox' id='agree' default='1'}/> {required}{phrase var='user.i_have_read_and_agree_to_the_a_href_id_js_terms_of_use_terms_of_use_a_and_a_href_id_js_privacy_policy_privacy_policy_a'}					
							</div>			
						</div>	
					</div>					
					{/if}
					
					<div class="table_clear">
					{if isset($bIsPosted) || !Phpfox::getParam('user.multi_step_registration_form')}
						<input type="submit" value="{phrase var='user.sign_up'}" class="button_register" id="js_registration_submit" />
					{else}
						<input type="button" value="{phrase var='user.sign_up'}" class="button_register" id="js_registration_submit" onclick="$Core.registration.submitForm();" />
					{/if}
					</div>
				</form>
			</div>
			{/if}
		{/if}
{if Phpfox::getLib('module')->getFullControllerName() != 'user.register'}
		</div>
		<div class="clear"></div>
	</div>
	{module name='user.images'}
</div>
{/if}
{if Phpfox::getLib('module')->getFullControllerName() == 'user.register'}
	</div>
</div>
{/if}