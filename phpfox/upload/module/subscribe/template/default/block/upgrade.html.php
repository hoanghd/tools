<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: upgrade.html.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($bIsFree)}
<div class="extra_info">
	{phrase var='subscribe.your_membership_has_successfully_been_upgraded'}
	<ul class="action">
		<li><a href="{url link='subscribe.view' id=$iPurchaseId}">{phrase var='subscribe.view_your_subscription'}</a></li>
	</ul>
</div>
{else}
{module name='api.gateway.form'}
{/if}