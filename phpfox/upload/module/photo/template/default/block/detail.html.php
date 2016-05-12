<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: detail.html.php 2610 2011-05-19 18:43:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aPhotoDetails key=sKey item=sValue}
{if !empty($sValue)}
<div class="info">
	<div class="info_left">
		{$sKey}:
	</div>	
	<div class="info_right">
		{$sValue}
	</div>	
</div>
{/if}
{/foreach}