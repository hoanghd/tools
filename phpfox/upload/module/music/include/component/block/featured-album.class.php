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
 * @version 		$Id: featured-album.class.php 2569 2011-04-27 19:03:20Z Raymond_Benc $
 */
class Music_Component_Block_Featured_Album extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aAlbums = Phpfox::getService('music.album')->getFeaturedAlbums();
		
		$this->template()->assign(array(
				'sHeader' => 'Featured Albums',
				'aFeaturedAlbums' => $aAlbums
			)
		);		
		
		if (count($aAlbums) == 5)
		{
			$this->template()->assign(array(
					'aFooter' => array(
						'View More' => $this->url()->makeUrl('music.browse.album', array('view' => 'featured'))
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
		(($sPlugin = Phpfox_Plugin::get('music.component_block_featured_album_clean')) ? eval($sPlugin) : false);
	}
}

?>