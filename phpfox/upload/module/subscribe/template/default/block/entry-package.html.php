<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: entry-package.html.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="{if is_int($phpfox.iteration.packages/2)}row1{else}row2{/if}{if $phpfox.iteration.packages == 1} row_first{/if}">
	<div style="width:130px; float:left; text-align:center;">
		<a href="#" onclick="{if Phpfox::isUser()}tb_show('{phrase var='subscribe.select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">{img server_id=$aPackage.server_id title=$aPackage.title path='subscribe.url_image' file=$aPackage.image_path suffix='_120' max_width='120' max_height='120'}</a>
	</div>
	<div style="margin-left:135px; min-height:120px; height:auto !important; height:120px;">
	{if $aPackage.show_price}
		<div class="go_right" style="font-size:10pt; font-weight:bold;">
			{if isset($aPackage.default_cost) && $aPackage.default_cost != '0.00'}			
				{if isset($aPackage.default_recurring_cost)}
					{$aPackage.default_recurring_currency_id|currency_symbol}{$aPackage.default_recurring_cost}
				{else}
				{$aPackage.default_currency_id|currency_symbol}{$aPackage.default_cost|number_format:2}
				{/if}
			{elseif isset($aPackage.price)}			
			    {foreach from=$aPackage.price item=sCurrency name=iCost}
			    <span>{$sCurrency.currency_id}: {$sCurrency.cost}</span>
			    {/foreach}
			{else}
			{phrase var='subscribe.free'}
			{/if}
		</div>
	{/if}
		<div class="h3"><a href="#" onclick="{if Phpfox::isUser()}tb_show('{phrase var='subscribe.select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">{$aPackage.title|convert|clean}</a></div>
		{if !empty($aPackage.description)}
		<div class="extra_info">
			{$aPackage.description|convert}
		</div>
		{/if}
	</div>
	<div class="clear"></div>
	<div class="t_right">
		<ul class="item_menu" style="margin:0px;">
			<li><a href="#" onclick="{if Phpfox::isUser()}tb_show('{phrase var='subscribe.select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">{if Phpfox::isUser() || (isset($bIsOnSignup) && $bIsOnSignup)}{phrase var='subscribe.select_upgrade'}{else}{phrase var='subscribe.upgrade'}{/if}</a></li>
		</ul>
	</div>
</div>