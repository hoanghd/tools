<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: index.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $sView == 'my'}
		<div class="item_bar">
			<div class="item_bar_action_holder">				
				<a href="#" class="item_bar_action"><span>{phrase var='photo.actions'}</span></a>		
				<ul>
					<li><a href="{url link='photo' view='my' mode='edit'}">{phrase var='photo.mass_edit_photos'}</a></li>
				</ul>			
			</div>		
		</div>	    
{/if}
<div id="js_actual_photo_content">
	<div id="js_album_outer_content">
		{if count($aPhotos)}
		{if isset($bIsEditMode)}
		<form method="post" action="#" onsubmit="$('#js_photo_multi_edit_image').show(); $('#js_photo_multi_edit_submit').hide(); $(this).ajaxCall('photo.massUpdate'); return false;">
			{foreach from=$aPhotos item=aForms}
				{template file='photo.block.edit-photo'}
			{/foreach}
			<div class="photo_table_clear">
				<div id="js_photo_multi_edit_image" style="display:none;">
					{img theme='ajax/add.gif'}
				</div>		
				<div id="js_photo_multi_edit_submit">
					<input type="submit" value="{phrase var='photo.update_photo_s'}" class="button" />
				</div>
			</div>
			{pager}
		</form>
		{else}
		{template file='photo.block.photo-entry'}
		{if Phpfox::getUserParam('photo.can_approve_photos') || Phpfox::getUserParam('photo.can_delete_other_photos')}
		{moderation}
		{/if}
		{/if}
		{else}
		<div class="extra_info">
			{phrase var='photo.no_photos_found'}			
		</div>
		{/if}	
	</div>
</div>