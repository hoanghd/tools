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
 * @version 		$Id: block.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Ad_Component_Block_Sample extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aPlan = Phpfox::getService('ad')->getPlan($this->getParam('block_id'));
		
		if (!isset($aPlan['plan_id']))
		{
			return false;
		}
		
		$iBlockId = $this->getParam('block_id');
		
		$this->template()->assign(array(
				'sSizes' => '<a href="#" onclick="window.parent.$(\'#location\').val(' . $iBlockId . '); window.parent.blockPlacementCallback(\'' . $aPlan['d_width'] . '\', \'' . $aPlan['d_height'] . '\',\'' . $iBlockId . '\'); window.parent.tb_remove();">' . $aPlan['d_width'] . 'x' . $aPlan['d_height'] . '</a>',
				'sBlockLocation' => $this->getParam('block_id'),
				'aPlan' => Phpfox::getService('ad')->getPlan($this->getParam('block_id'))
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ad.component_block_sample_clean')) ? eval($sPlugin) : false);
	}
}

?>