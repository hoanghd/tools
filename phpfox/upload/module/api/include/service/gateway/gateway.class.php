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
 * @package 		Phpfox_Service
 * @version 		$Id: gateway.class.php 1646 2010-06-10 12:40:28Z Raymond_Benc $
 */
class Api_Service_Gateway_Gateway extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('api_gateway');	
	}
	
	public function getActive()
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.is_active = 1')
			->execute('getRows');	
			
		foreach ($aGateways as $iKey => $aGateway)
		{
			$oGateway = Phpfox::getLib('gateway')->load($aGateway['gateway_id'], $aGateway);
			
			if ($oGateway === false)
			{
				continue;
			}
			
			$aGateways[$iKey]['custom'] = $oGateway->getEditForm();			
		}
			
		return $aGateways;	
	}
	
	public function getUserGateways($iUserId)
	{
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('user_gateway'))
			->where('user_id = ' . (int) $iUserId)
			->execute('getSlaveRows');
			
		$aGateways = array();
		foreach ($aRows as $iKey => $mValue)
		{
			$aCache = unserialize($mValue['gateway_detail']);
			$bSkip = false;
			foreach ($aCache as $sSettingKey => $sSettingValue)
			{
				if (empty($sSettingValue))
				{
					$bSkip = true;
				}
			}
			
			if ($bSkip === true)
			{
				$aGateways[$mValue['gateway_id']]['gateway'] = null;
				
				continue;
			}
			
			$aGateways[$mValue['gateway_id']]['gateway'] = unserialize($mValue['gateway_detail']);
		}		
			
		return $aGateways;
	}
	
	public function get($aGatewayData = array())
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.is_active = 1')
			->execute('getRows');
		
		foreach ($aGateways as $iKey => $aGateway)
		{
			if (isset($aGatewayData['fail_' . $aGateway['gateway_id']]) && $aGatewayData['fail_' . $aGateway['gateway_id']] === true)
			{
				unset($aGateways[$iKey]);
				
				continue;
			}
			
			if (!($oGateway = Phpfox::getLib('gateway')->load($aGateway['gateway_id'], array_merge($aGateway, $aGatewayData))))
			{				
				unset($aGateways[$iKey]);
				
				continue;
			}
			
			if (($aGateways[$iKey]['form'] = $oGateway->getForm()) === false)
			{				
				unset($aGateways[$iKey]);
			}
		}
		
		return $aGateways;
	}
	
	public function callback($sGateway)
	{
		Phpfox::startLog('Callback started.');
		Phpfox::log('Request: ' . var_export($_REQUEST, true));
		
		if (empty($sGateway))
		{
			Phpfox::log('Gateway is empty.');
			Phpfox::getService('api.gateway.process')->addLog(null, Phpfox::endLog());
			
			return false;
		}
		
		$aGateway = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.gateway_id = \'' . $this->database()->escape($sGateway) . '\' AND ag.is_active = 1')
			->execute('getRow');
			
		if (!isset($aGateway['gateway_id']))
		{
			Phpfox::log('"' . $sGateway . '" is not a valid gateway.');
			Phpfox::getService('api.gateway.process')->addLog(null, Phpfox::endLog());
			
			return false;
		}
		
		Phpfox::log('Attempting to load gateway: ' . $aGateway['gateway_id']);
		
		if (!($oGateway = Phpfox::getLib('gateway')->load($aGateway['gateway_id'], array_merge($_REQUEST, $aGateway))))
		{
			Phpfox::log('Unable to load gateway.');
			Phpfox::getService('api.gateway.process')->addLog($aGateway['gateway_id'], Phpfox::endLog());
			
			return false;
		}
		
		Phpfox::log('Gateway successfully loaded.');
		
		$mReturn = $oGateway->callback();
		
		Phpfox::log('Callback complete');
		
		Phpfox::getService('api.gateway.process')->addLog($aGateway['gateway_id'], Phpfox::endLog());
		
		if ($mReturn == 'redirect')
		{
			Phpfox::getLib('url')->send('');
		}
	}
	
	public function getPeriodPhrase($sPeriod, $sCost, $sDefaultCost)
	{		
		switch ($sPeriod)
		{
			case '1':
				$sPhrase = ($sCost == $sDefaultCost ? Phpfox::getPhrase('api.cost_per_month', array('cost' => $sCost)) : Phpfox::getPhrase('api.default_cost_and_then_cost_per_month', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;
			case '2':
				$sPhrase = ($sCost == $sDefaultCost ? Phpfox::getPhrase('api.cost_per_quarter', array('cost' => $sCost)) : Phpfox::getPhrase('api.default_cost_and_then_cost_per_quarter', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;
			case '3':
				$sPhrase = ($sCost == $sDefaultCost ? Phpfox::getPhrase('api.cost_biannualy', array('cost' => $sCost)) : Phpfox::getPhrase('api.default_cost_and_then_cost_biannualy', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;
			case '4':
				$sPhrase = ($sCost == $sDefaultCost ? Phpfox::getPhrase('api.cost_annually', array('cost' => $sCost)) : Phpfox::getPhrase('api.default_cost_and_then_cost_annually', array('default_cost' => $sDefaultCost, 'cost' => $sCost)));
				break;				
		}
		
		return $sPhrase;
	}
	
	public function getForAdmin()
	{
		$aGateways = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->order('ag.title ASC')
			->execute('getRows');	
			
		return $aGateways;		
	}	
	
	public function getForEdit($sGateway)
	{
		$aGateway = $this->database()->select('*')
			->from($this->_sTable)
			->where('gateway_id = \'' . $this->database()->escape($sGateway) . '\'')
			->execute('getSlaveRow');
			
		if (!isset($aGateway['gateway_id']))
		{
			return false;
		}
		
		$oGateway = Phpfox::getLib('gateway')->load($aGateway['gateway_id'], $aGateway);
		
		if ($oGateway === false)
		{
			return false;
		}
		
		$aGateway['custom'] = $oGateway->getEditForm();
			
		return $aGateway;
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('api.service_gateway_gateway__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}

?>