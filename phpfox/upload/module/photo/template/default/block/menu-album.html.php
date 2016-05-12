<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: menu-album.html.php 3177 2011-09-26 10:10:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
		{if (Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('photo.can_edit_own_photo_album')) || Phpfox::getUserParam('photo.can_edit_other_photo_albums')}
		<li><a href="{url link='photo.edit-album' id=$aForms.album_id}" id="js_edit_this_album">{phrase var='photo.edit_this_album'}</a></li>
		{/if}
		{if Phpfox::getUserId() == $aForms.user_id && $aForms.profile_id == '0'}
		<li><a href="{url link='photo.add' album=$aForms.album_id}">{phrase var='photo.upload_photos_to_album'}</a></li>
		{/if}
		{if $aForms.profile_id == '0' && (((Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('photo.can_delete_own_photo_album')) || Phpfox::getUserParam('photo.can_delete_other_photo_albums')))}
		<li class="item_delete"><a href="{url link='photo.albums' delete=$aAlbum.album_id}" id="js_delete_this_album" class="sJsConfirm">{phrase var='photo.delete'}</a></li>
		{/if}
		{plugin call='photo.template_block_menu_album'}