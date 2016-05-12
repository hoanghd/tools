<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel_Espinoza
 * @package 		Phpfox
 * @version 		$Id: featured.html.php 852 2009-08-10 18:05:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div>
{foreach from=$aFeaturedUsers item=aUser name=featured}
<div class="t_center p_bottom_10" style="width:32%; float:left;">
{if Phpfox::getLib('module')->getBlockLocation('log.login') == 'content'}
	{img user=$aUser suffix='_75' max_width=75 max_height=75}
{else}
	{img user=$aUser suffix='_50' max_width=50 max_height=50}
{/if}
	<div class="p_top_4">
		{$aUser|user}
	</div>
</div>
{if is_int($phpfox.iteration.featured/3)}
<div class="clear"></div>
{/if}


{/foreach}
</div>
<div class="clear"></div>