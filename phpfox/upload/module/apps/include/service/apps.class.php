<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Service
 * @version 		$Id: service.class.php 67 2009-01-20 11:32:45Z Raymond_Benc $
 */
class Apps_Service_Apps extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('app');
	}

	/**
	 * This function gets all the apps as an array, one of its sub-elements is an array
	 * of permissions
	 * @Todo cache apps
	 */
	public function getAllApps()
	{
		$aApps = $this->database()->select('a.*')
				->from($this->_sTable, 'a')
				->execute('getSlaveRows');
				
		foreach ($aApps as $iKey => $aApp)
		{
			$aApps[$iKey]['image_url'] = Phpfox::getParam('app.url_image') . sprintf($aApp['image_path'], '_square');
		}
		return $aApps;
	}
	
	/**
	 *
	 * @param array $aCategories an array of integers that reference category_id
	 * @return an array with information about each app that matches the categories inputted
	 */
	public function getAppsByCategories($aCategories)
	{
		$sWhere = '';
		foreach ($aCategories as $iCatId)
		{
			$sWhere .= 'ac.category_id = ' . (int)$iCatId . ' OR ';
		}
		$sWhere = rtrim($sWhere, ' OR ');
		
		$aApps = $this->database()->select('a.*')
				->from(Phpfox::getT('app'),'a')
				->join(Phpfox::getT('app_category_data'),'ac','ac.app_id = a.app_id')				
				->where($sWhere)
				->execute('getSlaveRows');
		foreach ($aApps as $iKey => $aApp)
		{
			$aApps[$iKey]['image_url'] = Phpfox::getParam('app.url_image') . sprintf($aApp['image_path'], '_square');
		}
	}
	
	/**
	 * Gets apps installed by Phpfox::getUserId()
	 * @return array
	 */
	public function getMyApps()
	{
		$aApps = $this->database()->select('a.*')
				->from(Phpfox::getT('app'), 'a')
				->where('a.user_id = ' . Phpfox::getUserId())
				->execute('getSlaveRows');
		foreach ($aApps as $iKey => $aApp)
		{
			$aApps[$iKey]['image_url'] = Phpfox::getParam('app.url_image') . sprintf($aApp['image_path'], '_square');
		}
		return $aApps;
	}
	
	public function getInstalledApps()
	{
		static $aApps = null;
			
		if ($aApps !== null)
		{
			return $aApps;
		}
		
		$sCacheId = $this->cache()->set(array('user', 'apps_' . Phpfox::getUserId()));
		
		if (!($aApps = $this->cache()->get($sCacheId)))
		{
			$aApps = $this->database()->select('a.*')
				->from(Phpfox::getT('app_installed'), 'aa')
				->join(Phpfox::getT('app'), 'a', 'a.app_id = aa.app_id')
				->where('aa.user_id = ' . Phpfox::getUserId())
				->execute('getSlaveRows');
			
			$this->cache()->set($sCacheId, $aApps);
		}
		
		if (!is_array($aApps))
		{
			$aApps = array($aApps);
		}
		
		return $aApps;
	}
	
	public function getForPage($iId)
	{
		$aApp = $this->database()->select('a.*')
			->from(Phpfox::getT('app'),'a')			
			->where('a.app_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aApp['app_id']))
		{
			return false;
		}
		
		return $aApp;
	}
	
	/**
	 * We call "installing" the app when a user grants permissions to that app to act on
	 * their account. This is assuming that every app requires a certain level of 
	 * permission
	 * @param int $iId the app_id
	 * @Todo cache
	 */
	public function getAppById($iId)
	{
		$aApp = $this->database()->select('a.*, p.page_id, p.total_like, au.install_id as is_installed, ac.category_id, ac.name as category_name, ' . Phpfox::getUserField())
			->from(Phpfox::getT('app'),'a')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = a.user_id')
			->leftjoin(Phpfox::getT('app_installed'), 'au', 'au.app_id = a.app_id AND au.user_id = ' . Phpfox::getUserId())
			->leftjoin(Phpfox::getT('app_category_data'), 'acd', 'acd.app_id = a.app_id')
			->leftjoin(Phpfox::getT('app_category'), 'ac', 'ac.category_id = acd.category_id')
			->leftjoin(Phpfox::getT('pages'), 'p', 'p.app_id = a.app_id')
			->where('a.app_id = ' . (int)$iId)
			->execute('getSlaveRow');
		
		if (empty($aApp))
		{
			return Phpfox_Error::display(Phpfox::getPhrase('apps.this_app_does_not_exist'));
		}
			
		$aApp['category_name'] = Phpfox::getLib('locale')->convert($aApp['category_name']);
		
		return $aApp;
	}
	
	/**
	 * Gets all categories available
	 */
	public function getCategories()
	{
		$aRows = $this->database()->select('*')
				->from(Phpfox::getT('app_category'))
				->order('name ASC')
				->execute('getSlaveRows');
		$aCategories = array();
		foreach ($aRows as $aRow)
		{
			$aCategories[] = array(
				'category_id' => $aRow['category_id'],
				'name' => $aRow['name'],
				'url' => Phpfox::permalink('apps.category', $aRow['category_id'], $aRow['name'])
				);
		}
		return $aCategories;
	}
	
	/**
	 * Used from the index controller
	 * @return type 
	 */
	public function getPendingTotal()
	{
		return (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('app'))
			->where('view_id = 1')
			->execute('getSlaveField');		
	}	
	
	/**
	 * This function collects permissions to be displayed to a user when they're going
	 * to install an app. They must accept these in order to use the app
	 * @param null|int $iId the app_id
	 * @return if $iId !== null it returns an extra field with the current value of 
	 *				Phpfox::getUserId
	 */
	public function getPermissions($iId = null)
	{
		$aPermissions = Phpfox::massCallback('getApiPermissions');
		
		if ($iId !== null)
		{
			$aDisallow = $this->database()->select('data_id as is_disallowed, var_name as sPermission')
					->from(Phpfox::getT('app_disallow'))
					->where('user_id = ' . Phpfox::getUserId() . ' AND app_id = ' . (int)$iId)
					->execute('getSlaveRows');
		}
		
		$aOut = array();
		foreach ($aPermissions as $sModule => $aPerm)
		{
			foreach ($aPerm as $sVariable => $sText)
			{
				$aPerm = $sModule .'.' . $sVariable;
				
				$aAdd = array('sPhrase' => $sText, 'sVariable' => $sModule .'.'.$sVariable);
				
				if (Phpfox::getLib('locale')->isPhrase($aPerm))
				{
					$aPerm = Phpfox::getLib('locale')->getPhrase($aPerm);
					$aAdd = array('sPhrase' => $sText, 'sVariable' => $sModule .'.'.$sVariable);
				}
				
				if ($iId !== null)
				{
					$aAdd['disallow'] = false;
					foreach ($aDisallow as $aRow)
					{
						if (($sModule .'.'.$sVariable) == $aRow['sPermission'])
						{
							$aAdd['disallow'] = true;
							break;
						}
					}
				}				
				$aOut[] = $aAdd;
			}
		}
		
		return $aOut;
	}
	
	/**
	 * This function generates a token, it will be used to show the iframe that loads the
	 * external app
	 * @param int $iAppId 
	 * @return string
	 */
	public function getKey($iAppId)
	{	
		$sKey = md5(((int) $iAppId) . uniqid() . Phpfox::getUserBy('email') . uniqid() . Phpfox::getUserBy('password_salt'));
		
		$this->database()->delete(Phpfox::getT('app_key'), 'app_id = ' . $iAppId . ' AND user_id = ' . Phpfox::getUserId());
		$this->database()->insert(Phpfox::getT('app_key'), array(
				'key_check' => $sKey,
				'app_id' => (int)$iAppId,
				'user_id' => Phpfox::getUserId(),
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		return $sKey;
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
		if ($sPlugin = Phpfox_Plugin::get('apps.service_apps__call'))
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