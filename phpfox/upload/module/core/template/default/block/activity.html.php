<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: activity.html.php 3326 2011-10-20 09:12:45Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div style="position:relative;">
	{foreach from=$aActivites key=sPhrase item=sValue}
	<div class="info">
		<div class="info_left">
			{$sPhrase}:
		</div>	
		<div class="info_right" style="margin-left: 125px;">
			{$sValue}
		</div>	
	</div>
	{/foreach}
</div>