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
 * @version 		$Id: track.class.php 3346 2011-10-24 15:20:05Z Raymond_Benc $
 */
class Music_Component_Block_Track extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		if ($this->getParam('album_user_id', null) === null)
		{
			return false;
		}
		
		$aSongs = Phpfox::getService('music.album')->getTracks($this->getParam('album_user_id'), $this->getParam('album_id'), $this->getParam('album_view_all', false));		
		
		$this->template()->assign(array(
				'sHeader' => Phpfox::getPhrase('music.mp3_tracks'),
				'aTracks' => $aSongs
			)
		);		
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('music.component_block_track_clean')) ? eval($sPlugin) : false);
	}
}

?>