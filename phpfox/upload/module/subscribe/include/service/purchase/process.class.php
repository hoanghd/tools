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
 * @version 		$Id: process.class.php 1802 2010-09-08 12:52:12Z Miguel_Espinoza $
 */
class Subscribe_Service_Purchase_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('subscribe_purchase');	
	}
	
	public function add($aVals, $iUserId = null)
	{		
		if ($iUserId === null)
		{
			Phpfox::isUser(true);
		}
		
		$aForms = array(
			'package_id' => array(
				'message' => Phpfox::getPhrase('subscribe.package_is_required'),
				'type' => 'int:required'
			),
			'currency_id' => array(
				'message' => Phpfox::getPhrase('subscribe.currency_is_required'),
				'type' => array('string:required', 'regex:currency_id')
			),
			'price' => array(
				'message' => Phpfox::getPhrase('subscribe.price_is_required'),
				'type' => 'price:required'
			)
		);		
		
		$aVals = $this->validator()->process($aForms, $aVals);
		
		if (!Phpfox_Error::isPassed())
		{
			return false;
		}		
		
		$aExtra = array(
			'user_id' => ($iUserId === null ? Phpfox::getUserId() : $iUserId),
			'time_stamp' => PHPFOX_TIME	
		);
		
		$iId = $this->database()->insert($this->_sTable, array_merge($aExtra, $aVals));
		
		return $iId;
	}
	
	public function update($iPurchaseId, $iPackageId, $sStatus, $iUserId, $iUserGroupId, $iFailUserGroupId)
	{		
		$sLink = Phpfox::getLib('url')->makeUrl('subscribe.view', array('id' => $iPurchaseId));		
		switch ($sStatus)
		{
			case 'completed':
				Phpfox::getService('user.process')->updateUserGroup($iUserId, $iUserGroupId);
				Phpfox::log('Moving user "' . $iUserId . '" to user group "' . $iUserGroupId . '"');
				$sSubject = array('subscribe.membership_successfully_updated_site_title', array('site_title' => Phpfox::getParam('core.site_title')));
				$sMessage = array('subscribe.your_membership_on_site_title_has_successfully_been_updated', array(
						'site_title' => Phpfox::getParam('core.site_title'),
						'link' => $sLink
					)
				);
				$this->database()->updateCounter('subscribe_package', 'total_active', 'package_id', $iPackageId);
				$this->database()->update(Phpfox::getT('user_field'), array('subscribe_id' => '0'), 'user_id = ' . (int) $iUserId);
				break;
			case 'pending':
				$sSubject = array('subscribe.membership_pending_site_title', array('site_title' => Phpfox::getParam('core.site_title')));
				$sMessage = array('subscribe.your_membership_subscription_on_site_title_is_currently_pending', array(
						'site_title' => Phpfox::getParam('core.site_title'),
						'link' => $sLink
					)
				);
				$this->database()->update(Phpfox::getT('user_field'), array('subscribe_id' => $iPurchaseId), 'user_id = ' . (int) $iUserId);
				break;
			case 'cancel':
				Phpfox::getService('user.process')->updateUserGroup($iUserId, $iFailUserGroupId);
				Phpfox::log('Moving user "' . $iUserId . '" to user group "' . $iFailUserGroupId . '"');
				$sSubject = array('subscribe.membership_canceled_site_title', array('site_title' => Phpfox::getParam('core.site_title')));
				$sMessage = array('subscribe.your_membership_subscription_on_site_title_has_been_canceled', array(
						'site_title' => Phpfox::getParam('core.site_title'),
						'link' => $sLink
					)
				);
				$this->database()->updateCounter('subscribe_package', 'total_active', 'package_id', $iPackageId, true);
				$this->database()->update(Phpfox::getT('user_field'), array('subscribe_id' => $iPurchaseId), 'user_id = ' . (int) $iUserId);
				break;
		}
		if ($sPlugin = Phpfox_Plugin::get('subscribe.service_purchase_process_update_pre_log'))
		{
			eval($sPlugin);
		}
		Phpfox::log('Updating status of purchase order');
		
		$this->database()->update($this->_sTable, array('status' => $sStatus), 'purchase_id = ' . (int) $iPurchaseId);	
		
		Phpfox::log('Sending user an email');
		Phpfox::getLib('mail')->to($iUserId)
			->subject($sSubject)
			->message($sMessage)
			->notification('subscribe.payment_update')
			->send();		
			
		Phpfox::log('Email sent');
	}
	
	public function updatePurchase($iId, $sStatus)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);	

		$aStatus = array(
			'completed',
			'cancel',
			'pending'			
		);	
		
		if (empty($sStatus))
		{
			$this->database()->update($this->_sTable, array('status' => '0'), 'purchase_id = ' . (int) $iId);
			
			return  true;
		}
		else 
		{
			if (!in_array($sStatus, $aStatus))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('subscribe.not_a_valid_purchase_status'));
			}
			
			$aPurchase = $this->database()->select('sp.*, spack.*')
				->from($this->_sTable, 'sp')
				->join(Phpfox::getT('subscribe_package'), 'spack', 'spack.package_id = sp.package_id')
				->where('sp.purchase_id = ' . (int) $iId)
				->execute('getRow');
				
			if (!isset($aPurchase['purchase_id']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('subscribe.unable_to_find_the_purchase_you_are_editing'));
			}
			
			$this->update($aPurchase['purchase_id'], $aPurchase['package_id'], $sStatus, $aPurchase['user_id'], $aPurchase['user_group_id'], $aPurchase['fail_user_group']);
			
			return  true;
		}
	}
	
	public function delete($iId)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);

		$aPurchase = $this->database()->select('sp.*, spack.*')
			->from($this->_sTable, 'sp')
			->join(Phpfox::getT('subscribe_package'), 'spack', 'spack.package_id = sp.package_id')
			->where('sp.purchase_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aPurchase['purchase_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('subscribe.unable_to_find_the_purchase_you_are_trying_to_delete'));
		}			
		
		$this->database()->updateCounter('subscribe_package', 'total_active', 'package_id', $aPurchase['package_id'], true);
		$this->database()->delete($this->_sTable, 'purchase_id = ' . $aPurchase['purchase_id']);
		
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
		if ($sPlugin = Phpfox_Plugin::get('subscribe.service_purchase_process__call'))
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