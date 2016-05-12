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
 * @version 		$Id: sponsored.class.php 1537 2010-03-30 11:55:12Z Raymond_Benc $
 */
class Ad_Component_Block_Sponsored extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->template()->assign(array(
				'sHeader' => 'Sponsored',
				'aFooter' => array(
					'Create an Ad' => $this->url()->makeUrl('ad.add')
				)
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
		(($sPlugin = Phpfox_Plugin::get('ad.component_block_sponsored_clean')) ? eval($sPlugin) : false);
	}
}

?>