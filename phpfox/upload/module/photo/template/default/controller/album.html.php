<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: album.html.php 2869 2011-08-22 19:22:58Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_view">
	<div class="item_info">
		{$aForms.time_stamp|convert_time} by {$aForms|user}
	</div>
	{if ((Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('photo.can_edit_own_photo_album')) || Phpfox::getUserParam('photo.can_edit_other_photo_albums'))
		|| (Phpfox::getUserId() == $aForms.user_id && $aForms.profile_id == '0')
		|| ($aForms.profile_id == '0' && (((Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('photo.can_delete_own_photo_album')) || Phpfox::getUserParam('photo.can_delete_other_photo_albums'))))
	}
	<div class="item_bar">
		<div class="item_bar_action_holder">
			<a href="#" class="item_bar_action"><span>Actions</span></a>		
			<ul>
				{template file='photo.block.menu-album'}
			</ul>			
		</div>		
	</div>
	{/if}
	<div id="js_album_description">
		{$aForms.description|clean}
		{if !empty($aForms.description)}
		<div class="separate"></div>
		{/if}
	</div>
	
	<div id="js_album_content">
		{template file='photo.block.photo-entry'}
	</div>
	
	<div {if $aForms.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
		{module name='feed.comment'}
	</div>	

</div>