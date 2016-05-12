<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: edit-album.class.php 2602 2011-05-16 07:40:29Z Raymond_Benc $
 */
class Photo_Component_Controller_Edit_Album extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		if (!($aAlbum = Phpfox::getService('photo.album')->getForEdit($this->request()->getInt('id'))))
		{
			return Phpfox_Error::display('Photo album not found.');
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($this->request()->get('req3') == 'photo')
			{
				if (Phpfox::getService('photo.process')->massProcess($aAlbum, $aVals))
				{
					$this->url()->send('photo.edit-album.photo', array('id' => $aAlbum['album_id']), 'Photo(s) successfully updated.');
				}
			}
			else 
			{
				if (Phpfox::getService('photo.album.process')->update($aAlbum['album_id'], $aVals))
				{
					$this->url()->send('photo.edit-album', array('id' => $aAlbum['album_id']), 'Album successfully updated.');
				}
			}
		}
		
		$aMenus = array(
			'detail' => 'Album Info',
			'photo' => 'Photos'
		);
		
		$this->template()->buildPageMenu('js_photo_block', 
			$aMenus,
			array(
				'link' => $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']),
				'phrase' => 'View This Album'
			)
		);	
		
		list($iCnt, $aPhotos) = Phpfox::getService('photo')->get('p.album_id = ' . (int) $aAlbum['album_id']);
		list($iAlbumCnt, $aAlbums) = Phpfox::getService('photo.album')->get('pa.user_id = ' . Phpfox::getUserId());
		
		$this->template()->setTitle('Editing Album: ' . $aAlbum['name'])
			->setFullSite()
			->setBreadcrumb('Photo', $this->url()->makeUrl('photo'))
			->setBreadcrumb('Editing Album: ' . $aAlbum['name'], $this->url()->makeUrl('photo.edit-album', array('id' => $aAlbum['album_id'])), true)
			->setHeader(array(
					'edit.css' => 'module_photo',
					'photo.js' => 'module_photo'
				)
			)
			->assign(array(
					'aForms' => $aAlbum,
					'aPhotos' => $aPhotos,
					'aAlbums' => $aAlbums
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_edit_album_clean')) ? eval($sPlugin) : false);
	}
}

?>