<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: album.class.php 1491 2010-03-03 15:34:04Z Raymond_Benc $
 */
class Music_Component_Block_Album extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aUser = $this->getParam('aUser');
		
		if (!Phpfox::getUserGroupParam($aUser['user_group_id'], 'music.can_upload_music_public'))
		{
			return false;
		}
		
		$aAlbums = Phpfox::getService('music.album')->getForProfile($aUser['user_id']);
		
		if (!count($aAlbums) && !defined('PHPFOX_IN_DESIGN_MODE'))
		{
			return false;
		}		
		
		$this->template()->assign(array(
				'sHeader' => Phpfox::getPhrase('music.albums'),
				'sBlockJsId' => 'profile_music_album',
				'aAlbums' => $aAlbums
			)
		);
		
		if (count($aAlbums) >= 4)
		{
			$this->template()->assign(array(
					'aFooter' => array(
						Phpfox::getPhrase('music.view_more') => $this->url()->makeUrl('music.browse.album', array('userid' => $aUser['user_id']))
					)
				)
			);
		}
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('music.component_block_album_clean')) ? eval($sPlugin) : false);
	}
	
	public function widget()
	{
		return true;
	}	
}

?>