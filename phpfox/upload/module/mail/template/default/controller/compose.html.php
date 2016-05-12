<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 * @version 		$Id: compose.html.php 3137 2011-09-20 07:41:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs}
<div id="js_ajax_compose_error_message"></div>
<div>
	{if PHPFOX_IS_AJAX}	
	<form method="post" action="{url link='mail.compose'}" id="js_form_mail" onsubmit="{plugin call='mail.template_controller_compose_ajax_onsubmit'} $Core.processForm('#js_mail_compose_submit'); $(this).ajaxCall('mail.composeProcess'); return false;">
	{else}
	<form method="post" action="{url link='mail.compose'}" id="js_form_mail" onsubmit="{$sGetJsForm}">
	{/if}
	<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
	{if PHPFOX_IS_AJAX && isset($aUser.user_id)}
	<div><input type="hidden" name="id" value="{$aUser.user_id}" /></div>
	<div><input type="hidden" name="val[to][]" value="{$aUser.user_id}" /></div>
	{else}
		<div class="table">
			<div class="table_left">
				To:
			</div>
			<div class="table_right">					
				<div id="js_mail_search_friend_placement" style="width:410px;"></div>
				<div id="js_mail_search_friend"></div>
				{literal}
				<script type="text/javascript">
				$Behavior.loadSearchFriendsCompose = function()
				{
					$Core.searchFriends({
						'id': '#js_mail_search_friend',
						'placement': '#js_mail_search_friend_placement',
						'width': '400px',
						'max_search': 10,
						'input_name': 'val[to]',
						'default_value': 'Search friends by their name...',
						'inline_bubble': true
					});		
				}
				</script>
				{/literal}
			</div>
		</div>
	{/if}
		<div class="table">
			<div class="table_left">			
				<label for="subject">{phrase var='mail.subject'}:</label>			
			</div>
			<div class="table_right">
				<input type="text" name="val[subject]" id="subject" value="{value type='input' id='subject'}" size="40" style="{if Phpfox::isMobile()}width:90%;{else}width:400px;{/if}" tabindex="1" />
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="table">
			<div class="table_left">
				<label for="message">{phrase var='mail.message'}:</label>
			</div>
			<div class="table_right" id="js_compose_new_message">
				{if PHPFOX_IS_AJAX}
				{editor id='message' rows='2' tabindex='2'}
				{else}
				{editor id='message'}
				{/if}
			</div>
			<div class="clear"></div>
		</div>
		
		{if Phpfox::isModule('captcha') && Phpfox::getUserParam('mail.enable_captcha_on_mail')}{module name='captcha.form' sType='mail'}{/if}
		
		<div class="table_clear" style="position:relative;">
			<div style="position:absolute; right:0px;">
				<label><input type="checkbox" name="val[copy_to_self]" value="1" class="v_middle" />{phrase var='mail.send_a_copy_to_myself'}</label>
			</div>
			{if PHPFOX_IS_AJAX}	
			<div id="js_mail_compose_submit">
				<ul class="table_clear_button">
					<li><input type="submit" value="Submit" class="button" /></li>
					<li class="table_clear_ajax"></li>
				</ul>
				<div class="clear"></div>
			</div>			
			{else}
			<input type="submit" value="{phrase var='mail.send'}" class="button" />
			{/if}
		</div>
		
	</form>
</div>