<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Tag
 * @version 		$Id: cloud.html.php 2322 2011-03-02 11:00:01Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aRows)}
<div class="tag_cloud">
	<ul>
	{foreach from=$aRows item=aRow}
		<li><a href="{$aRow.link}">{$aRow.key}</a></li>
	{/foreach}
	</ul>
	<div class="clear"></div>
</div>
{else}
<div class="message">
	{phrase var='tag.no_tags_have_been_found'}
</div>
{/if}