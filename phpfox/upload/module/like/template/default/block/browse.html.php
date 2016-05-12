<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: browse.html.php 3048 2011-09-08 18:33:51Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aLikes)}
<div style="height:300px;" class="label_flow">
	{foreach from=$aLikes name=like item=aLike}
	<div class="{if is_int($phpfox.iteration.like/2)}row1{else}row2{/if}{if $phpfox.iteration.like == 1} row_first{/if}">
		<div class="go_left" style="width:55px; text-align:center;">
			{img user=$aLike suffix='_50_square' max_width=50 max_height=50}	
		</div>
		<div style="margin-left:55px;">
			{$aLike|user}
		</div>
		<div class="clear"></div>
	</div>
	{/foreach}
</div>
{else}
<div class="extra_info">
	{$sErrorMessage}
</div>
{/if}