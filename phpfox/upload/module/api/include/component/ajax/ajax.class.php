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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 673 2009-06-14 19:28:30Z Raymond_Benc $
 */
class Api_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function updateGatewayActivity()
	{
		if (Phpfox::getService('api.gateway.process')->updateActivity($this->get('gateway_id'), $this->get('active')))
		{
			
		}		
	}
	
	public function updateGatewayTest()
	{
		if (Phpfox::getService('api.gateway.process')->updateTest($this->get('gateway_id'), $this->get('active')))
		{
			
		}			
	}
}

?>