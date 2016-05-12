<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: edit-album.html.php 2602 2011-05-16 07:40:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_photo_block_detail" class="js_photo_block page_section_menu_holder">
	<form method="post" action="{url link='photo.edit-album' id=$aForms.album_id}">
		<div id="js_custom_privacy_input_holder_album">
			{module name='privacy.build' privacy_item_id=$aForms.album_id privacy_module_id='photo_album'}
		</div>	
		{template file='photo.block.form-album'}
		<div class="table_clear">
			<input type="submit" value="Update" class="button" />
		</div>
	</form>
</div>

<div id="js_photo_block_photo" class="js_photo_block page_section_menu_holder" style="display:none;">
	<form method="post" action="{url link='photo.edit-album.photo' id=$aForms.album_id}">
		{foreach from=$aPhotos item=aForms}
			{template file='photo.block.edit-photo'}
		{/foreach}
	
		<div class="photo_table_clear">
			<input type="submit" value="Save Changes" class="button" />
		</div>
	</form>
</div>