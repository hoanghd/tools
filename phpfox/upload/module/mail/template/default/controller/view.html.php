<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 * @version 		$Id: view.html.php 2860 2011-08-20 19:17:52Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_info">
	<ul class="extra_info_middot">
		<li>{$aMail.time_stamp|convert_time}</li>
		<li>&middot;</li>
		<li>
			<span class="js_hover_title">
		{if $aMail.owner_user_id == Phpfox::getUserId()}
			{if $aMail.owner_user_id == $aMail.viewer_user_id && $aMail.owner_user_id == Phpfox::getUserId()}
			You <span class="js_hover_info">This message was sent from you</span>
			{else}
			{$aMail|user:'viewer_'}<span class="js_hover_info">This message was sent from {$aMail|user:'viewer_'}</span>
			{/if}	
		{elseif $aMail.owner_user_id != 0}
			{$aMail|user:'owner_'}<span class="js_hover_info">This message was sent from {$aMail|user:'owner_'}</span>
		{else}
			{phrase var='mail.site_sent_you_a_message' site=$sSite}<span class="js_hover_info">This message was sent from {$sSite}</span>
		{/if}		
			</span>
		</li>
	</ul>
</div>

	<div class="item_bar">
		<div class="mail_next_prev">
			<ul>
				{if $iNextId != ""}
					<li class="previous_message"><a href="{url link='mail.view' id=$iNextId}">{phrase var='mail.previous'}</a></li>
				{/if}
				{if $iPrevId != ""}
					<li class="next_message"><a href="{url link='mail.view' id=$iPrevId}">{phrase var='mail.next'}</a></li>
				{/if}
			</ul>
			<div class="clear"></div>
		</div>
		<div class="item_bar_action_holder">
			<a href="#" class="item_bar_action"><span>Actions</span></a>	
			<ul>				
				{if Phpfox::isModule('report') && $aMail.owner_user_id != Phpfox::getUserId()}
				<li><a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=mail&amp;id={$aMail.mail_id}" class="inlinePopup" title="{phrase var='mail.report_this_message'}">{phrase var='mail.report'}</a></li>
				{/if}			
				<li class="item_delete"><a href="{url link='mail' action='delete' id=$aMail.mail_id}" onclick="return confirm('{phrase var='mail.are_you_sure' phpfox_squote=true}')">{phrase var='mail.delete'}</a></li>
			</ul>				
		</div>		
	</div>

	{if isset($bMass)}
	<div class="p_top_8">
		{phrase var='mail.mass_message_to'}: 
		{foreach from=$aMails name=mass item=aMass}{if $phpfox.iteration.mass != 1}, {/if}{$aMass|user}{/foreach}
	</div>
	{/if}
	<div>
		{$aMail.text|parse|split:100}
		{if $aMail.parent_id && $aMail.text_reply}
		<div class="quote">
			<div class="quote_body">
				{$aMail.text_reply|parse|split:80}
			</div>
		</div>
		{/if}
	</div>

	{if isset($aAttachments)}
	{module name='attachment.list' sType='mail' attachments=$aAttachments}
	{/if}

	{if $aMail.viewer_user_id == Phpfox::getUserId() && $aMail.owner_user_id != 0}
	<br />
	{$sCreateJs}
	<form method="post" action="{url link='mail.view' id=$aMail.mail_id}" id="js_form_mail" onsubmit="{$sGetJsForm}">
		<div><input type="hidden" name="val[parent_id]" value="{$aMail.mail_id}" /></div>
		<div><input type="hidden" name="val[attachment]" id="js_attachment" value="{value type='input' id='attachment'}" /></div>
		<div class="table">
			<div class="table_left">
				<label for="message">{phrase var='mail.reply'}:</label>
			</div>
			<div class="table_right" id="js_mail_textarea">
				{editor id='message' rows='8'}
			</div>
		</div>
		<div class="table_clear">
			<input type="submit" value="{phrase var='mail.send'}" class="button" id="js_mail_submit" />
		</div>
	</form>
	{/if}