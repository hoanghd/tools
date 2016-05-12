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
 * @version 		$Id: block.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Link_Component_Block_Display extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iLinkId = (int) $this->getParam('link_id');	
		
		if (!($aLink = Phpfox::getService('link')->getLinkById($iLinkId)))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'aLink' => $aLink
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('link.component_block_display_clean')) ? eval($sPlugin) : false);		
	}
}

?>