<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: register.html.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if empty($aPurchase.status)}
{module name='api.gateway.form'}
{else}
{if $aPurchase.status == 'pending'}
<div class="extra_info">
	{phrase var='subscribe.thank_you_for_your_purchase_your_payment_is_currently_pending_approval'}
</div>
{/if}
{/if}