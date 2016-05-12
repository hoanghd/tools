<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: category.html.php 2763 2011-07-28 07:11:01Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="action">
	{foreach from=$aCategories item=aCategory}
	<li><a href="{$aCategory.link}">{$aCategory.name|convert}</a></li>
	{/foreach}
</ul>