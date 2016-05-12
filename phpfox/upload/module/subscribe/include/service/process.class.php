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
 * @version 		$Id: process.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Subscribe_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('subscribe_package');	
	}
	
	public function add($aVals, $iUpdateId = null)
	{		
		$aForms = array(
			'title' => array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_message_for_the_package'),
				'type' => array('string:required')
			),
			'description' => array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_description_for_the_package'),
				'type' => 'string:required'
			),
			'user_group_id' => array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_user_group_on_success'),
				'type' => 'int:required'
			),
			'fail_user_group' => array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_user_group_on_cancellation'),
				'type' => 'int:required'
			),
			'is_registration' => array(
				'message' => Phpfox::getPhrase('subscribe.provide_if_the_package_should_be_added_to_the_registration_form'),
				'type' => 'int:required'
			),
			'is_active' => array(
				'message' => Phpfox::getPhrase('subscribe.select_if_the_package_is_active_or_not'),
				'type' => 'int:required'
			),			
			'cost' => array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_price_for_the_package'),
				'type' => 'currency:required'
			),
			'show_price' => array(
				'type' => 'int:required'
			),				
		);		
		
		$bIsRecurring = false;
		if (isset($aVals['is_recurring']) && $aVals['is_recurring'])
		{
			$aForms['recurring_cost'] = array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_recurring_cost'),
				'type' => 'currency:required'				
			);			
			$aForms['recurring_period'] = array(
				'message' => Phpfox::getPhrase('subscribe.provide_a_recurring_period'),
				'type' => 'int:required'
			);			
			
			$bIsRecurring = true;
		}
		
		if ($iUpdateId !== null)
		{
			if (isset($aVals['is_recurring']) && !$aVals['is_recurring'])
			{
				$aCacheForm = $aVals;
			}
		}
		
		$aVals = $this->validator()->process($aForms, $aVals);
		
		if (!Phpfox_Error::isPassed())
		{
			return false;
		}			
		
		if ($iUpdateId !== null)
		{
			if (isset($aCacheForm['is_recurring']) && !$aCacheForm['is_recurring'])
			{
				$aVals['recurring_period'] = 0;
				$aVals['recurring_cost'] = null;
			}			
		}		
		
		$aVals['cost'] = serialize($aVals['cost']);
		if ($bIsRecurring)
		{
			$aVals['recurring_cost'] = serialize($aVals['recurring_cost']);	
		}
		
		if (!empty($_FILES['image']['name']))
		{
			$aImage = Phpfox::getLib('file')->load('image', array('jpg', 'gif', 'png'));
			
			if ($aImage === false)
			{
				return false;
			}			
		}
		
		$aVals['title'] = $this->preParse()->convert($aVals['title']);
		$aVals['description'] = $this->preParse()->convert($aVals['description']);
		
		if ($iUpdateId !== null)
		{
			$iId = $iUpdateId;	
			
			$this->database()->update($this->_sTable, $aVals, 'package_id = ' . (int) $iUpdateId);	
		}
		else 
		{	
			$iLastOrderId = $this->database()->select('ordering')->from($this->_sTable)->order('ordering DESC')->execute('getSlaveField');
			$aVals['ordering'] = ($iLastOrderId + 1);
			
			$iId = $this->database()->insert($this->_sTable, $aVals);
		}
		
		if (!empty($_FILES['image']['name']) && ($sFileName = Phpfox::getLib('file')->upload('image', Phpfox::getParam('subscribe.dir_image'), $iId)))
		{
			$this->database()->update($this->_sTable, array('image_path' => $sFileName, 'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')), 'package_id = ' . (int) $iId);		
			
			Phpfox::getLib('image')->createThumbnail(Phpfox::getParam('subscribe.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('subscribe.dir_image') . sprintf($sFileName, '_120'), 120, 120);
			
			unlink(Phpfox::getParam('subscribe.dir_image') . sprintf($sFileName, ''));
		}	
		
		return $iId;
	}
	
	public function update($iId, $aVals)
	{
		return $this->add($aVals, $iId);
	}
	
	public function updateOrder($aVals)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		
		if (!isset($aVals['ordering']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('subscribe.not_a_valid_request'));
		}
		
		foreach ($aVals['ordering'] as $iId => $iOrder)
		{
			$this->database()->update($this->_sTable, array('ordering' => (int) $iOrder), 'package_id = ' . (int) $iId);
		}
	}
	
	public function updateActivity($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
	
		$this->database()->update($this->_sTable, array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'package_id = ' . (int) $iId);
	}
	
	public function deleteImage($iId, &$aPackage = null)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);	

		if ($aPackage === null)
		{
			$aPackage = $this->database()->select('package_id, image_path, server_id')
				->from($this->_sTable)
				->where('package_id = ' . (int) $iId)
				->execute('getRow');
		}
			
		if (!isset($aPackage['package_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('subscribe.unable_to_find_the_package'));
		}
		
		if (!empty($aPackage['image_path']))
		{
			$sImage = Phpfox::getParam('subscribe.dir_image') . sprintf($aPackage['image_path'], '_120');
			if (file_exists($sImage))
			{
				unlink($sImage);
			}
			
			$this->database()->update($this->_sTable, array('image_path' => null, 'server_id' => '0'), 'package_id = ' . $aPackage['package_id']);		
		}
		
		return true;
	}
	
	public function delete($iId)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);			
		
		$aPackage = $this->database()->select('package_id, image_path, server_id')
			->from($this->_sTable)
			->where('package_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aPackage['package_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('subscribe.unable_to_find_the_package'));
		}

		$this->deleteImage($aPackage['package_id'], $aPackage);
		$this->database()->delete(Phpfox::getT('subscribe_purchase'), 'package_id = ' . $aPackage['package_id']);
		$this->database()->delete(Phpfox::getT('subscribe_package'), 'package_id = ' . $aPackage['package_id']);		
		
		return true;
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
		if ($sPlugin = Phpfox_Plugin::get('subscribe.service_process__call'))
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