<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: info.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="info_holder">

	<div class="info">
		<div class="info_left">
			{phrase var='event.time'}
		</div>
		<div class="info_right">
			{$aEvent.event_date}	
		</div>
	</div>	
	
	{if is_array($aEvent.categories) && count($aEvent.categories)}
	<div class="info">
		<div class="info_left">
			{phrase var='event.category'}
		</div>
		<div class="info_right">
			{$aEvent.categories|category_display}
		</div>
	</div>		
	{/if}
	
	<div class="info">
		<div class="info_left">
			{phrase var='event.location'}
		</div>
		<div class="info_right">				 	
			{$aEvent.location|clean|split:60}
			{if !empty($aEvent.address)}
			<div class="p_2">{$aEvent.address|clean}</div>
			{/if}			
			{if !empty($aEvent.city)}
			<div class="p_2">{$aEvent.city|clean}</div>
			{/if}					
			{if !empty($aEvent.postal_code)}
			<div class="p_2">{$aEvent.postal_code|clean}</div>
			{/if}								
			{if !empty($aEvent.country_child_id)}
			<div class="p_2">{$aEvent.country_child_id|location_child}</div>
			{/if}			
			<div class="p_2">{$aEvent.country_iso|location}</div>
			{if isset($aEvent.map_location)}						
			<div style="width:420px; height:175px; position:relative;">
				<div style="margin-left:-8px; margin-top:-8px; position:absolute; background:#fff; border:8px blue solid; width:12px; height:12px; left:50%; top:50%; z-index:200; overflow:hidden; text-indent:-1000px; border-radius:12px;">Marker</div>
				<a href="http://maps.google.com/?q={$aEvent.map_location}" target="_blank" title="{phrase var='event.view_this_on_google_maps'}"><img src="http://maps.googleapis.com/maps/api/staticmap?center={$aEvent.map_location}&amp;zoom=16&amp;size=420x175&amp;sensor=false&amp;maptype=hybrid" alt="" /></a>
			</div>		
			<div class="p_top_4">					
				<a href="http://maps.google.com/?q={$aEvent.map_location}" target="_blank">{phrase var='event.view_on_google_maps'}</a>
			</div>			
			{/if}
		</div>
	</div>
	
	<div class="info">
		<div class="info_left">
			{phrase var='event.created_by'}
		</div>
		<div class="info_right">
			{$aEvent|user}	
		</div>
	</div>

	{$aEvent.description|parse|split:70}

</div>