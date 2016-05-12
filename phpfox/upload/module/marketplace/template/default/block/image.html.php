<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: image.html.php 2992 2011-09-04 11:55:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aImages) > 1}
<div class="js_box_thumbs_holder">
{/if}
	<div class="marketplace_image_holder">		
		<div class="marketplace_image">
			{img thickbox=true thickbox_suffix='_400' server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path suffix='_200' max_width='180' max_height='180'}
		</div>
		{if count($aImages) > 1}
		<div class="marketplace_image_extra js_box_image_holder_thumbs">
			<ul>{foreach from=$aImages name=images item=aImage}<li>{img thickbox=true thickbox_suffix='_400' server_id=$aImage.server_id title=$aListing.title path='marketplace.url_image' file=$aImage.image_path suffix='_50_square' max_width='50' max_height='50'}</li>{/foreach}</ul>
			<div class="clear"></div>
		</div>		
		<div class="marketplace_view_image">
			<a href="#" onclick="tb_show('', '{img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' suffix='_400' file=$aListing.image_path return_url=true}', $(this).parents('.js_box_thumbs_holder:first').find('.marketplace_image')); return false;">View Larger Images</a>
		</div>
		{/if}
	</div>
{if count($aImages) > 1}
</div>
{/if}