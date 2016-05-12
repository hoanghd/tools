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
 * @package  		Module_Share
 * @version 		$Id: frame.class.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
class Share_Component_Block_Frame extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->template()->assign(array(
				'sBookmarkType' => $this->getParam('type'),
				'sBookmarkUrl' => $this->getParam('url'),
				'sBookmarkTitle' => $this->getParam('title')
			)
		);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('share.component_block_frame_clean')) ? eval($sPlugin) : false);
	}
}

?>