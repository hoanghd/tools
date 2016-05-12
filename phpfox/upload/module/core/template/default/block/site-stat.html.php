<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: site-stat.html.php 2826 2011-08-11 19:41:03Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aStats key=sKey item=aStatItem}
	<div class="info_header">
		{$sKey}
	</div>
	<div class="p_4">
	{foreach from=$aStatItem item=aStat}
		<div class="info">
			<div class="info_left">
				{$aStat.phrase}:
			</div>
			<div class="info_right">
			{if isset($aStat.link)}<a href="{$aStat.link}">{/if}{$aStat.value}{if isset($aStat.link)}</a>{/if}
			</div>
		</div>
	{/foreach}
	</div>
{/foreach}