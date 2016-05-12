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
 * @version 		$Id: subscribe.class.php 1966 2010-10-27 19:10:23Z Raymond_Benc $
 */
class Subscribe_Service_Subscribe extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('subscribe_package');	
	}
	
	public function getPackages($bIsForSignUp = false)
	{		
		$aPackages = $this->database()->select('sp.*')
			->from($this->_sTable, 'sp')
			->where('sp.is_active = 1' . ($bIsForSignUp ? ' AND sp.is_registration = 1' : ''))
			->order('sp.ordering ASC')
			->execute('getRows');			
		
		foreach ($aPackages as $iKey => $aPackage)
		{			
			if (Phpfox::getUserBy('user_group_id') == $aPackage['user_group_id'])
			{				
				unset($aPackages[$iKey]);
				
				continue;
			}
			
			if (!empty($aPackage['cost']) && Phpfox::getLib('parse.format')->isSerialized($aPackage['cost']))
			{
				$aCosts = unserialize($aPackage['cost']);	
				foreach ($aCosts as $sKey => $iCost)
				{
					if (Phpfox::getService('core.currency')->getDefault() == $sKey)
					{
						$aPackages[$iKey]['default_cost'] = $iCost;
						$aPackages[$iKey]['default_currency_id'] = $sKey;
					}
					else
					{
					    $aPackages[$iKey]['price'][$sKey]['cost'] = $iCost;
					    $aPackages[$iKey]['price'][$sKey]['currency_id'] = $sKey;
					}
				}
				
				if ($aPackage['recurring_period'] > 0 && Phpfox::getLib('parse.format')->isSerialized($aPackage['recurring_cost']))
				{
					$aRecurringCosts = unserialize($aPackage['recurring_cost']);	
					foreach ($aRecurringCosts as $sKey => $iCost)
					{
						if (Phpfox::getService('core.currency')->getDefault() == $sKey)
						{
							$aPackages[$iKey]['default_recurring_cost'] = Phpfox::getService('api.gateway')->getPeriodPhrase($aPackage['recurring_period'], $iCost, $aPackages[$iKey]['default_cost']);
							$aPackages[$iKey]['default_recurring_currency_id'] = $sKey;
						}
					}					
				}
			}
		}		
			
		return $aPackages;
	}
	
	public function getPackage($iPackageId, $bIsAdminEdit = false)
	{
		$aPackage = $this->database()->select('sp.*')
			->from($this->_sTable, 'sp')
			->where('sp.package_id = ' . (int) $iPackageId . ' ' . ($bIsAdminEdit ? '' : 'AND sp.is_active = 1'))
			->order('sp.ordering ASC')
			->execute('getRow');	
			
		if (!isset($aPackage['package_id']))
		{
			return false;
		}
		
		if (!empty($aPackage['cost']) && Phpfox::getLib('parse.format')->isSerialized($aPackage['cost']))
		{
			$aCosts = unserialize($aPackage['cost']);	
			foreach ($aCosts as $sKey => $iCost)
			{
				if (Phpfox::getService('core.currency')->getDefault() == $sKey)
				{
					$aPackage['default_cost'] = $iCost;
					$aPackage['default_currency_id'] = $sKey;
				}
				else
				{
				    $aPackage['price'][]= array('cost' => $iCost, 'currency_id' => $sKey);
				}
			}
		}		
		
		if ($aPackage['recurring_period'] > 0 && Phpfox::getLib('parse.format')->isSerialized($aPackage['recurring_cost']))
		{
			$aRecurringCosts = unserialize($aPackage['recurring_cost']);	
			foreach ($aRecurringCosts as $sKey => $iCost)
			{
				if (Phpfox::getService('core.currency')->getDefault() == $sKey)
				{
					$aPackage['default_recurring_cost'] = $iCost;
					$aPackage['default_recurring_currency_id'] = $sKey;
				}
			}					
		}
		
		if ($aPackage['recurring_period'] > 0)
		{
			$aPackage['is_recurring'] = '1';
		}
			
		return $aPackage;
	}	
	
	public function getForEdit($iPackageId)
	{
		return $this->getPackage($iPackageId, true);
	}
	
	public function getForAdmin()
	{
		$aPackages = $this->database()->select('sp.*')
			->from($this->_sTable, 'sp')
			->order('sp.ordering ASC')
			->execute('getRows');	
			
		return $aPackages;		
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
		if ($sPlugin = Phpfox_Plugin::get('subscribe.service_subscribe__call'))
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