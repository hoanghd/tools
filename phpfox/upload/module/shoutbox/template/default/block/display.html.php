<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Shoutbox
 * @version 		$Id: display.html.php 2905 2011-08-26 17:52:00Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<script type="text/javascript">
{literal}
	function addShoutOut(oObj)
	{
		if ($('#js_shoutbox_input').val() == '')
		{
			return false;
		}
		
		$('#js_shoutbox_form').hide();
		{/literal}
		$('#js_shoutbox_message').html($.ajaxProcess('{phrase var='shoutbox.adding_your_shoutout' phpfox_squote=true}'));
		{literal}
		$(oObj).ajaxCall('shoutbox.add');
		
		return false;
	}
{/literal}
</script>
<div id="js_shoutbox_messages" class="label_flow shoutbox_holder">
	{foreach from=$aShoutouts item=aShoutout key=iShoutCount name=shoutout}
	{template file='shoutbox.block.entry'}
	{/foreach}
</div>
<div id="js_shoutbox_form">
	<form method="post" action="{url link='current'}" onsubmit="return addShoutOut(this);">	
	{if isset($aCallbackShoutbox.module)}
		<div><input type="hidden" name="module" value="{$aCallbackShoutbox.module}" /></div>
	{/if}
	{if isset($aCallbackShoutbox.item)}
		<div><input type="hidden" name="item" value="{$aCallbackShoutbox.item}" /></div>
	{/if}	
		<div><input id="js_shoutbox_input" type="text" name="shoutout" size="20" maxlength="255" value="" class="shoutbox_input" /></div>
	</form>
</div>
<div id="js_shoutbox_message"></div>
{if Phpfox::getParam('shoutbox.shoutbox_is_live')}
<script type="text/javascript">setTimeout("$.ajaxCall('shoutbox.getMessages', {if isset($aCallbackShoutbox.module)}'module={$aCallbackShoutbox.module}&item={$aCallbackShoutbox.item}'{else}''{/if}, 'GET')", {$iShoutboxRefresh});</script>
{/if}