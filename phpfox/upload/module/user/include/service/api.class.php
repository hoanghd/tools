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
 * @package 		Phpfox_Service
 * @version 		$Id: api.class.php 2900 2011-08-26 09:18:42Z Raymond_Benc $
 */
class User_Service_Api extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('user');
		$this->_oApi = Phpfox::getService('api');
	}
	
	public function getUser()
	{
		if ((int) $this->_oApi->get('user_id') === 0)
		{
			$iUserId = $this->_oApi->getUserId();
		}
		else
		{
			$iUserId = $this->_oApi->get('user_id');
		}
		
		$sSelect = 'u.user_id, u.user_name, u.joined, u.user_image, u.gender, u.country_iso';
		
		// Check if user allowed access to get his full_name
		if ($this->_oApi->isAllowed('user.get_full_name'))
		{
			$sSelect .= ', u.full_name'; 
		}				
				
		if ($this->_oApi->isAllowed('user.get_email'))
		{
			$sSelect .= ', u.email';
		}		
		
		$aRow = $this->database()->select($sSelect)
			->from($this->_sTable, 'u')
			->where('u.user_id = ' . (int) $iUserId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['user_id']))
		{
			return $this->_oApi->error('user.user_cannot_be_found', 'User cannot be found.');
		}
		
		$sImagePath = $aRow['user_image'];
		
		$aRow['photo_50px'] = Phpfox::getLib('image.helper')->display(array(
				'user' => $aRow,
				'suffix' => '_50',
				'return_url' => true
			)
		);
		
		$aRow['photo_50px_square'] = Phpfox::getLib('image.helper')->display(array(
				'user' => $aRow,
				'suffix' => '_50_square',
				'return_url' => true
			)
		);		
		
		$aRow['photo_120px'] = Phpfox::getLib('image.helper')->display(array(
				'user' => $aRow,
				'suffix' => '_120',
				'return_url' => true
			)
		);		
		
		$aRow['photo_original'] = Phpfox::getLib('image.helper')->display(array(
				'user' => $aRow,
				'suffix' => '',
				'return_url' => true
			)
		);		
		
		$aRow['gender'] = ($aRow['gender'] == '1' ? 'Male' : 'Female');
		$aRow['profile_url'] = Phpfox::getLib('url')->makeUrl($aRow['user_name']);
		
		unset($aRow['user_image']);

		return $aRow;
	}	
	
	/**
	 * This function updates the user status by adding a feed
	 */
	public function updateStatus()
	{
		if ($this->_oApi->isAllowed('user.update_status') == false)
		{
			return $this->_oApi->error('user.not_allowed', 'Status updates not allowed for this user.');
		}
		$sStatus = $this->_oApi->get('user_status');
		if (empty($sStatus))
		{
			return $this->_oApi->error('user.status_is_empty', 'The variable user_status cannot be empty');
		}
		
		$iPrivacy = $this->_oApi->get('privacy');
		if (empty($iPrivacy))
		{
			$iPrivacy = 0;
		}
		return (bool)Phpfox::getService('user.process')->updateStatus(array(
			'user_status' => $this->_oApi->get('user_status'),
			'privacy' => $iPrivacy
			));
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
		if ($sPlugin = Phpfox_Plugin::get('user.service_api__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>