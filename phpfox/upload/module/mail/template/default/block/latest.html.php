<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: latest.html.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aMessages)}
<ul id="js_new_message_holder_drop">
{foreach from=$aMessages name=messages item=aMessage}
	<li id="js_mail_read_{$aMessage.mail_id}" class="holder_notify_drop_data{if $phpfox.iteration.messages == 1} first{/if}"><a href="{url link='mail.view.'$aMessage.mail_id''}" title="{$aMessage.preview|clean}" class="main_link{if $aMessage.viewer_is_new} is_new{/if}">
			<div class="drop_data_image">
				{if !empty($aMessage.user_id)}
				{img user=$aMessage max_width='50' max_height='50' suffix='_50_square' no_link=true}
				{/if}
			</div>
			<div class="drop_data_content">
				<div class="drop_data_user">
					{if empty($aMessage.user_id)}
					{param var='core.site_title'}
					{else}
					{$aMessage.full_name|clean}
					{/if}
				</div>
				{$aMessage.subject|clean}
				<div class="drop_data_time">
					{$aMessage.time_stamp|convert_time}
				</div>
			</div>
			<div class="clear"></div>
		</a>
	</li>
{/foreach}
</ul>
{literal}
<script type="text/javascript">	
	var $iTotalMessages = parseInt($('#js_total_new_messages').html());
	var $iNewTotalMessages = 0;
	$('#js_new_message_holder_drop li').each(function()
	{
		$iNewTotalMessages++;
		$aMailOldHistory[$(this).attr('id').replace('js_mail_read_', '')] = true;		
	});
	
	$iTotalMessages = parseInt(($iTotalMessages - $iNewTotalMessages));
	if ($iTotalMessages < 0)
	{
		$iTotalMessages = 0;
	}
	
	if ($iTotalMessages === 0)
	{
		$('#js_total_new_messages').html('').hide();	
	}
	else
	{
		$('#js_total_new_messages').html($iTotalMessages);
	}	
</script>
{/literal}
{else}
<div class="drop_data_empty">
	{phrase var='mail.no_new_messages'}
</div>
{/if}
<a href="{url link='mail'}" class="holder_notify_drop_link">{phrase var='mail.see_all_messages'}</a>