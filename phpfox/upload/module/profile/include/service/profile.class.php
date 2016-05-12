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
 * @version 		$Id: profile.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Profile_Service_Profile extends Phpfox_Service 
{
	private $_iUserId = 0;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		
	}
	
	public function getProfileMenu($aUser)
	{
		$aMenus = array();
		if (Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'feed.view_wall'))
		{
			$aMenus[] = array(
				'phrase' => Phpfox::getPhrase('profile.wall'),
				'url' => 'profile',
				'icon' => 'misc/comment.png'	
			);
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('profile.info'),
			'url' => 'profile.info' . (defined('PHPFOX_IN_DESIGN_MODE') ? '.design' : ''),
			'icon' => 'misc/application_view_list.png'	
		);	
		
		if (!Phpfox::getUserBy('profile_page_id') && !defined('PHPFOX_IN_DESIGN_MODE'))
		{
			$aModuleCalls = Phpfox::massCallback('getProfileMenu', $aUser);
			foreach ($aModuleCalls as $sModule => $aModuleCall)
			{
				if (!is_array($aModuleCall))
				{
					continue;
				}
				$aMenus[] = $aModuleCall[0];
			}
		}
		
		foreach ($aMenus as $iKey => $aMenu)
		{
			$bSubIsSelected = false;
			if (isset($aMenu['sub_menu']))
			{
				foreach ((array) $aMenu['sub_menu'] as $iSubKey => $aSubMenu)
				{
					if ($this->request()->get('view'))
					{
						$sCurrent = 'profile.' . $this->request()->get('req2') . '.view_' . $this->request()->get('view');
					}
					else 
					{
						$sCurrent = 'profile.' . $this->request()->get('req2') . '.' . $this->request()->get('req3');
					}

					if ($sCurrent == $aSubMenu['url'])
					{
						$aMenus[$iKey]['sub_menu'][$iSubKey]['is_selected'] = true;
						$bSubIsSelected = true;
						break;	
					}
				}
			}

			if ($bSubIsSelected === false 
				&& (
					($aMenu['url'] == 'profile' . (Phpfox::getLib('request')->get('req2') ? '.' . Phpfox::getLib('request')->get('req2') : '') . (Phpfox::getLib('request')->get('req3') ? '.' . Phpfox::getLib('request')->get('req3') : ''))
					|| (Phpfox::getLib('request')->get('req2') == '' && $iKey === 0 && !Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'feed.view_wall'))					
				)
			)
			{
				$aMenus[$iKey]['is_selected'] = true;
			}
			
			$aMenus[$iKey]['actual_url'] = str_replace('.', '_', $aMenu['url']);
			
			if ($aMenu['url'] == 'profile')
			{
				$aMenus[$iKey]['url'] = $aUser['user_name'];
			}
			else 
			{
				$aMenus[$iKey]['url'] = $aUser['user_name'] . '.' . Phpfox::getLib('url')->doRewrite(preg_replace("/^profile\.(.*)$/i", "\\1", $aMenu['url']));
			}
		}		
		
		return $aMenus;	
	}
	
	public function setUserId($iUserId)
	{
		$this->_iUserId = (int) $iUserId;
	}
	
	public function getProfileUserId()
	{
		return (int) $this->_iUserId;
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
		if ($sPlugin = Phpfox_Plugin::get('profile.service_profile__call'))
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