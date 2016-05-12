<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: info.html.php 3346 2011-10-24 15:20:05Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="info_holder">

	<div class="info">
		<div class="info_left">
			{phrase var='marketplace.posted_on'}:
		</div>
		<div class="info_right">
			{$aListing.time_stamp|date:'marketplace.marketplace_view_time_stamp'}
		</div>
	</div>
	
	{if is_array($aListing.categories) && count($aListing.categories)}
	<div class="info">
		<div class="info_left">
			{phrase var='marketplace.category'}:
		</div>
		<div class="info_right">
			{$aListing.categories|category_display}
		</div>
	</div>		
	{/if}	
	
	<div class="info">
		<div class="info_left">
			{phrase var='marketplace.posted_by'}:
		</div>
		<div class="info_right">
			{$aListing|user}
		</div>
	</div>
	<div class="info">
		<div class="info_left">
			{phrase var='marketplace.location'}:
		</div>
		<div class="info_right">
			{$aListing.country_iso|location}
			{if !empty($aListing.country_child_id)}
			<div class="p_2">&raquo; {$aListing.country_child_id|location_child}</div>
			{/if}
			{if !empty($aListing.city)}
			<div class="p_2">&raquo; {$aListing.city|clean|split:50} </div>
			{/if}			
		</div>
	</div>
	<div class="item_view_content">
		{$aListing.description|parse|split:70}
	</div>
</div>