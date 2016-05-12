<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: list.html.php 671 2009-06-12 17:12:28Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aPurchases item=aPurchase name=purchases}
<div class="{if is_int($phpfox.iteration.purchases/2)}row1{else}row2{/if}{if $phpfox.iteration.purchases == 1} row_first{/if}">
	{template file='subscribe.block.entry'}
</div>
{/foreach}