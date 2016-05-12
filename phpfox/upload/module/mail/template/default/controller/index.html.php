<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 * @version 		$Id: index.html.php 3194 2011-09-27 11:50:16Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $iFolder}
<div style="position:absolute; right:0px; top:-15px;">
	<a href="#" onclick="if (confirm('Are you sure?')) {l} $.ajaxCall('mail.deleteFolder', 'id={$iFolder}'); {r} return false;">Delete This List</a>
</div>
{/if}
{if count($aMails)}
{foreach from=$aMails item=aMail name=mail}
<div id="js_message_{$aMail.mail_id}" class="mail_holder{if !$bIsSentbox && !$bIsTrash && $aMail.viewer_is_new} mail_is_new{/if}">
	<div class="mail_moderation">
		<a href="#{$aMail.mail_id}" class="moderate_link" rel="mail">Moderate</a>		
	</div>
	<div class="mail_image">
		{if $aMail.user_id == Phpfox::getUserId()}
			{img user=$aMail suffix='_50_square' max_width=50 max_height=50}
		{else}
			{if (isset($aMail.user_id) && !empty($aMail.user_id))}
				{img user=$aMail suffix='_50_square' max_width=50 max_height=50}
			{/if}
		{/if}
	</div>
	<div class="mail_content">
		<div class="mail_action">
			<ul>
				<li>{$aMail.time_stamp|convert_time}</li>
				{if !$bIsSentbox && !$bIsTrash}				
				<li class="js_mail_mark_read"{if !$aMail.viewer_is_new} style="display:none;"{/if}><a href="#" class="mail_read js_hover_title" onclick="$.ajaxCall('mail.toggleRead', 'id={$aMail.mail_id}', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_mail_mark_unread').show(); $(this).parents('.mail_holder:first').removeClass('mail_is_new'); return false;"><span class="js_hover_info">Mark as Read</span></a></li>
				<li class="js_mail_mark_unread"{if $aMail.viewer_is_new} style="display:none;"{/if}><a href="#" class="mail_read js_hover_title" onclick="$.ajaxCall('mail.toggleRead', 'id={$aMail.mail_id}', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_mail_mark_read').show(); $(this).parents('.mail_holder:first').addClass('mail_is_new'); return false;"><span class="js_hover_info">Mark as Unread</span></a></li>
				{/if}
				<li><a href="#" class="mail_delete js_hover_title" onclick="$.ajaxCall('mail.delete', 'id={$aMail.mail_id}{if $bIsSentbox}&amp;type=sentbox{/if}{if $bIsTrash}&amp;type=trash{/if}', 'GET'); return false;"><span class="js_hover_info">Delete</span></a></li>
			</ul>
			<div class="clear"></div>
		</div>		
		<a href="{url link='mail.view' id=$aMail.mail_id}" class="mail_link">{if $aMail.parent_id}{phrase var='mail.re'}: {/if}{$aMail.subject|clean|shorten:35:'...'}</a>	
		<div class="extra_info">
			{if $aMail.user_id == Phpfox::getUserId()}
				{phrase var='mail.to'}: You
			{else}
				{if $bIsSentbox}
				{phrase var='mail.to'}: {$aMail|user}
				{else}
				{phrase var='mail.from'}: {if empty($aMail.user_id)}{param var='core.site_title'}{else}{$aMail|user}{/if}
				{/if}
			{/if}	
		</div>
		
		{if Phpfox::getParam('mail.show_preview_message')}
		<div class="mail_preview">
			{$aMail.preview|clean|shorten:40:'...'|cleanbb}
		</div>
		{/if}		
		
	</div>	
</div>
{/foreach}
{else}
<div class="extra_info">
	No messages found here.
</div>
{/if}

{moderation}

{pager}