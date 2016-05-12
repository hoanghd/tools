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
 * @version 		$Id: featured.class.php 2569 2011-04-27 19:03:20Z Raymond_Benc $
 */
class Music_Component_Block_Featured extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aFeatured = Phpfox::getService('music')->getFeaturedSongs();
		
		if (!is_array($aFeatured))
		{
			return false;
		}
		
		if (!count($aFeatured))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => 'Featured Songs',
				'aFeaturedSongs' => $aFeatured
			)
		);
		
		if (count($aFeatured) == 5)
		{
			$this->template()->assign(array(
					'aFooter' => array(
						'View More' => $this->url()->makeUrl('music', array('view' => 'featured'))
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
		(($sPlugin = Phpfox_Plugin::get('music.component_block_featured_clean')) ? eval($sPlugin) : false);
	}
}

?>