<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: display.html.php 2284 2011-02-01 15:58:18Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
				<div class="global_attachment_holder_section" id="global_attachment_photo">
					<div><input type="hidden" name="val[group_id]" value="{if isset($aFeedCallback.item_id)}{$aFeedCallback.item_id}{else}0{/if}" /></div>			
					<div><input type="hidden" name="val[action]" value="upload_photo_via_share" /></div>
					<div><input type="file" name="image[]" id="global_attachment_photo_file_input" value="" onchange="$bButtonSubmitActive = true; $('.activity_feed_form_button .button').removeClass('button_not_active');" /></div>
					<div class="extra_info">
						{phrase var='photo.select_a_photo_to_attach'}
					</div>
				</div>		