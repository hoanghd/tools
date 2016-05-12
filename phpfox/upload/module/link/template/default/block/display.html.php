<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_global_link_id_{$aLink.link_id}">
	<div class="attachment_row_title">
		<a href="{$aLink.link|clean}" class="attachment_row_link">{$aLink.title|clean}</a>
	</div>
	<div class="attachment_image">
		<div class="attachment_image_holder">
			{if !empty($aLink.image)}
			<div id="js_attachment_link_default_image">	
				<a href="{$aLink.link|clean}"{if $aLink.has_embed} class="play_link" onclick="$.ajaxCall('link.play', 'id={$aLink.link_id}', 'GET'); return false;"{/if}>{if $aLink.has_embed}<span class="play_link_img">Play</span>{/if}<img src="{$aLink.image}" alt="" style="max-width:120px;" /></a>
			</div>
			{/if}
		</div>
	</div>
	<div class="attachment_body">
		<div class="attachment_body_link">
			{$aLink.link|clean|shorten:50:'...'}
		</div>
		{if isset($aLink.description)}
		<div class="attachment_body_description">	
			{$aLink.description|clean}
		</div>
		{/if}		
	</div>
	<div class="clear"></div>
</div>