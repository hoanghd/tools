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
 * @version 		$Id: link.class.php 2251 2010-12-27 19:03:36Z Raymond_Benc $
 */
class Like_Component_Block_Link extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		$this->template()->assign(array(
				'aLike' => array(
					'like_type_id' => $this->getParam('like_type_id'),
					'like_item_id' => $this->getParam('like_item_id'),
					'like_is_liked' => $this->getParam('like_is_liked')
				)
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('like.component_block_link_clean')) ? eval($sPlugin) : false);
		
		$this->template()->clean('aLike');
	}
}

?>