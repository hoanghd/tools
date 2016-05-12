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
 * @version 		$Id: form.class.php 1544 2010-04-07 13:20:17Z Raymond_Benc $
 */
class Api_Component_Block_Gateway_Form extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aGatewayData = $this->getParam('gateway_data');
		
		$this->template()->assign(array(
				'aGateways' => Phpfox::getService('api.gateway')->get($aGatewayData),
				'aGatewayData' => $aGatewayData
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_block_list_clean')) ? eval($sPlugin) : false);
	}
}

?>