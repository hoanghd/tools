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
 * @package  		Module_User
 * @version 		$Id: user.class.php 3296 2011-10-12 13:29:57Z Raymond_Benc $
 */
class User_Service_User extends Phpfox_Service 
{	
	private $_aUser = array();
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('user');
	}	
	
	public function getByUserName($sUser)
	{	
		return $this->database()->select('u.*, user_field.*')
			->from($this->_sTable, 'u')
			->join(Phpfox::getT('user_field'), 'user_field', 'user_field.user_id = u.user_id')
			->where("u.user_name = '" . $this->database()->escape($sUser) . "'")
			->execute('getSlaveRow');
	}
	
	public function getUser($mUser, $sSelect = 'u.*', $bUserName = false)
	{
		(($sPlugin = Phpfox_Plugin::get('user.service_user_getuser_start')) ? eval($sPlugin) : false);
		
		if ($bUserName === false)
		{
			if ((int) $mUser === 0)
			{
				return false;
			}
		}
		else 
		{
			if (empty($mUser))
			{
				return false;
			}
		}		
		
		$aRow = $this->database()->select($sSelect)
			->from($this->_sTable, 'u')
			->where(($bUserName ? "u.full_name = '" . $this->database()->escape($mUser) . "'" : 'u.user_id = ' . (int) $mUser))
			->execute('getSlaveRow');
		
		(($sPlugin = Phpfox_Plugin::get('user.service_user_getuser_end')) ? eval($sPlugin) : false);
			
		return $aRow;
	}
	
	public function get($mName = null, $bUseId = true)
	{		
		static $aUser = array();
		
		if (isset($aUser[$mName]))
		{		
			return $aUser[$mName];
		}
		
		(($sPlugin = Phpfox_Plugin::get('user.service_user_get_start')) ? eval($sPlugin) : false);
		
		if (Phpfox::isUser())
		{
			$this->database()->select('ut.item_id AS is_viewed, ')->leftJoin(Phpfox::getT('user_track'), 'ut', 'ut.item_id = u.user_id AND ut.user_id = ' . Phpfox::getUserId());
		}
		
		$this->database()->select('ur.rate_id AS has_rated, ')->leftJoin(Phpfox::getT('user_rating'), 'ur', 'ur.item_id = u.user_id AND ur.user_id = ' . Phpfox::getUserId());
		
		if (Phpfox::getUserParam('user.can_feature'))
		{
			$this->database()
				->select('uf.user_id as is_featured, uf.ordering as featured_order, ')
				->leftjoin(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');					
		}
		
		$aRow = $this->database()->select('u.*, user_space.*, user_field.*, user_activity.*, ls.user_id AS is_online, ts.style_id AS designer_style_id, ts.folder AS designer_style_folder, t.folder AS designer_theme_folder, t.total_column, ts.l_width, ts.c_width, ts.r_width, t.parent_id AS theme_parent_id, ug.prefix, ug.suffix, ug.icon_ext, ug.title')
			->from($this->_sTable, 'u')
			->join(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = u.user_group_id')
			->join(Phpfox::getT('user_space'), 'user_space', 'user_space.user_id = u.user_id')
			->join(Phpfox::getT('user_field'), 'user_field', 'user_field.user_id = u.user_id')
			->join(Phpfox::getT('user_activity'), 'user_activity', 'user_activity.user_id = u.user_id')
			->leftJoin(Phpfox::getT('theme_style'), 'ts', 'ts.style_id = user_field.designer_style_id AND ts.is_active = 1')
			->leftJoin(Phpfox::getT('theme'), 't', 't.theme_id = ts.theme_id')
			->leftJoin(Phpfox::getT('log_session'), 'ls', 'ls.user_id = u.user_id AND ls.im_hide = 0')
			->where(($bUseId ? "u.user_id = " . (int) $mName . "" : "u.user_name = '" . $this->database()->escape($mName) . "'"))
			->execute('getSlaveRow');
		
		(($sPlugin = Phpfox_Plugin::get('user.service_user_get_end')) ? eval($sPlugin) : false);
		if (isset($aRow['is_invisible']) && $aRow['is_invisible'])
		{
			$aRow['is_online'] = '0';
		}
		
		$aUser[$mName] =& $aRow;			
			
		if (!isset($aUser[$mName]['user_name']))
		{
			return false;
		}		
		
		$aUser[$mName]['user_server_id'] = $aUser[$mName]['server_id'];		
		
		$aUser[$mName]['is_friend'] = false;
		$aUser[$mName]['is_friend_of_friend'] = false;
		if (Phpfox::isUser() && Phpfox::isModule('friend') && Phpfox::getUserId() != $aUser[$mName]['user_id'])
		{
			$aUser[$mName]['is_friend'] = Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aUser[$mName]['user_id']);				
			$aUser[$mName]['is_friend_of_friend'] = Phpfox::getService('friend')->isFriendOfFriend($aUser[$mName]['user_id']);
			if (!$aUser[$mName]['is_friend'])
			{
				$aUser[$mName]['is_friend'] = (Phpfox::getService('friend.request')->isRequested(Phpfox::getUserId(), $aUser[$mName]['user_id']) ? 2 : false);
				if (!$aUser[$mName]['is_friend'])
				{
					$aUser[$mName]['is_friend'] = (Phpfox::getService('friend.request')->isRequested($aUser[$mName]['user_id'], Phpfox::getUserId()) ? 3 : false);
				}
			}			
		}				
		
		$this->_aUser[$aRow['user_id']] = $aUser[$mName];
	
		return $aUser[$mName];
	}	
	
	public function getUserObject($iUserId)
	{
		return (isset($this->_aUser[$iUserId]) ? (object) $this->_aUser[$iUserId] : false);
	}
	
	public function getForEdit($iUserId)
	{
		Phpfox::getUserParam('user.can_edit_users', true);
		
		(($sPlugin = Phpfox_Plugin::get('user.service_user_getforedit')) ? eval($sPlugin) : false);
		
		$aUser = $this->database()->select('u.*, uf.*, ua.*')
			->from($this->_sTable, 'u')
			->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
			->join(Phpfox::getT('user_activity'), 'ua', 'ua.user_id = u.user_id')
			->where('u.user_id = ' . (int) $iUserId)
			->execute('getRow');
			
		if (!isset($aUser['user_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('user.unable_to_find_the_user_you_plan_to_edit'));
		}
		
		return $aUser;
	}
	
	public function getNew($iLimit = 8)
	{
		return $this->database()->select(Phpfox::getUserField())
			->from($this->_sTable, 'u')
			->order('u.joined DESC')
			->limit($iLimit)
			->execute('getSlaveRows');
	}
	
	public function getRandom($iLimit = 4)
	{
		return $this->database()->select(Phpfox::getUserField())
			->from($this->_sTable, 'u')
			->where('u.user_image IS NOT NULL')
			->order('RAND()')
			->limit($iLimit)
			->execute('getSlaveRows');
	}	
	
	public function isUser($mName, $bId = false)
	{
		(($sPlugin = Phpfox_Plugin::get('user.service_user_isuser')) ? eval($sPlugin) : false);
		
		return $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where(($bId ? "user_id = " . (int) $mName : "user_name = '" . $this->database()->escape($mName) . "'"))
			->execute('getField');
	}

	/**
	 * Gets the language_phrase variable names for the reasons available to show at account cancelation
	 * @return array
	 */
	public function getReasons()
	{
		$sCacheId = $this->cache()->set('user_cancellations');
		if (!($aReasons = $this->cache()->get($sCacheId)))
		{
			$aReasons = $this->database()->select('*')
				->from(Phpfox::getT('user_delete'))
				->order('ordering ASC')
				->where('is_active = 1')
				->execute('getSlaveRows');
				
			$this->cache()->save($sCacheId, $aReasons);
		}
		if (!isset($aReasons) || !is_array($aReasons))
		{
			$aReasons = array();
		}
		foreach ($aReasons as $iKey => $aReason)
		{
			if ($aReasons[$iKey]['is_active'] != 1)
			{
				unset($aReasons[$iKey]);
			}
		}
		return $aReasons;
	}

	public function gender($iGender, $iType = 0)
	{
		$sGender = false;
		foreach ((array) Phpfox::getParam('core.global_genders') as $iKey => $aGender)
		{
			if ($iGender == $iKey)
			{
				return ($iType == 1 ? Phpfox::getPhrase($aGender[0]) : Phpfox::getPhrase($aGender[1]));
			}
		}
		
		(($sPlugin = Phpfox_Plugin::get('user.service_user_gender')) ? eval($sPlugin) : false);
		
		return $sGender;
	}

	/**
	 * Formats the date so its easier to search birthdates
	 * @param int $iDay
	 * @param int $iMonth
	 * @param int $iYear
	 * @return String
	 * @example buildAge(1,9,1980) returns: "09011980"
	 * @example buildAge("8","19",1980) returns false, there is no month 19th
	 * @example buildAge("8","11","1978") returns "11081978"
	 */
	public function buildAge($iDay, $iMonth, $iYear = null)
	{
		$iDay = (int)$iDay;
		$iMonth = (int)$iMonth;
		$iYear = ($iYear !== null) ? (int)$iYear : null;
		if ( (1 > $iDay || $iDay > 31) || (1 > $iMonth || $iMonth > 12) )
		{
				return false;
		}
		if ($iYear !== null)
		{
			return ($iMonth < 10 ? '0' . $iMonth : $iMonth) .($iDay < 10 ? '0' . $iDay : $iDay) . $iYear;
		}

		return ($iMonth < 10 ? '0' . $iMonth : $iMonth) .($iDay < 10 ? '0' . $iDay : $iDay);
	}

	/**
	 * Returns how old is a user based on its birthdate
	 * @param String $sAge
	 * @return int
	 */
	public function age($sAge)
	{
		if (!$sAge)
		{
			return $sAge;
		}
		$iYear = intval(substr($sAge,4));
		$iMonth = intval(substr($sAge,0,2));
		$iDay = intval(substr($sAge,2,2));
		$iAge = date('Y') - (int) $iYear;
		$iCurrDate = date('m') * 100 + date('d');
	    $iBirthDate = $iMonth * 100 + $iDay;
	    
	    if ($iCurrDate < $iBirthDate)
	    {
	        $iAge--;
	    }
	    
	    return $iAge;
	}
	
	public function getAgeArray($sAge)
	{
		return array(
			'day' => intval(substr($sAge, 2, 2)),
			'month' => intval(substr($sAge, 0, 2)),
			'year' => intval(substr($sAge, 4))
		);
	}

	public function getInlineSearch($sUser, $sOld)
	{
		(($sPlugin = Phpfox_Plugin::get('user.service_user_getinlinesearch')) ? eval($sPlugin) : false);

		$sOld = trim(rtrim($sOld, ','));	
		if (strpos($sOld, ','))
		{
			$sOld = explode(',', $sOld);
			$sOld = array_map('trim', $sOld);
		}
		
		$aRows = $this->database()->select('u.user_id, u.full_name AS tag_text, u.user_name, u.server_id, u.user_name, u.user_image')
			->from($this->_sTable, 'u')
			->join(Phpfox::getT('friend'), 'f', 'f.user_id = u.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
			->where((strpos($sUser, '@') ? "u.email LIKE '" . $this->database()->escape($sUser) . "%'" : "(u.full_name LIKE '" . $this->database()->escape($sUser) . "%' OR u.user_name LIKE '" . $this->database()->escape($sUser) . "%')"))			
			->limit(0, 10)
			->execute('getSlaveRows');		
			
		foreach ($aRows as $iKey => $aRow)
		{			
			if ((is_array($sOld) && in_array($aRow['user_id'], $sOld) || (is_string($sOld) && $aRow['user_id'] === $sOld)))
			{				
				unset($aRows[$iKey]);
			}
		}
		
		return $aRows;
	}
	
	public function getLink($iUserId = null, $sUserName = null, $aParams = null)
	{
		if (($iUserId === null || $sUserName === null))
		{
			$aRow = $this->database()->select('user_id, user_name')
				->from($this->_sTable)
				->where(($iUserId === null ? "user_name = '" . $this->database()->escape($sUserName) . "'" : 'user_id = ' . (int) $iUserId))
				->execute('getSlaveRow');

			if (!isset($aRow['user_id']))
			{
				return Phpfox_Error::trigger('Not a valid user.', E_USER_ERROR);
			}
			
			$iUserId = $aRow['user_id'];
			$sUserName = $aRow['user_name'];
		}
		
		return Phpfox::getLib('url')->makeUrl($sUserName, $aParams);
	}
	
	/**
	 * Returns the first name of a users full name.
	 *
	 * Usage within a template:
	 * <code>
	 * {$sFullname|first_name}
	 * </code>
	 *
	 * Usage within a PHP class:
	 * <code>
	 * Phpfox::getService('user')->getFirstName($sFullName);
	 * </code>
	 * 
	 * @param string $sName Full name of the member.
	 * 
	 * @return string Returns the first part of the name.
	 */
	public function getFirstName($sName)
	{
		// Create an array based on a space between a persons name		
		$aParts = explode(' ', $sName);
		// Return the first part of the name, which is the first name
		return $aParts[0];
	}
	
	public function getUserFields($bReturnUserValues = false, &$aUser = null, $sPrefix = null, $iUserId = null)
	{
		$aFields = array(
			'user_id',
			'profile_page_id',
			'server_id',
			'user_name',
			'full_name',
			'gender',
			'user_image',
			'is_invisible'
		);	
		
		if (Phpfox::getParam('user.display_user_online_status'))
		{
			$aFields[] = 'last_activity';
		}
		
		/* Return $aFields but about iUserId */
		if ($iUserId != null)
		{
			$aUser = $this->database()->select(implode(',', $aFields))
					->from(Phpfox::getT('user'))
					->where('user_id = ' . (int)$iUserId)
					->execute('getSlaveRow');
			
			return $aUser;
		}
		
		(($sPlugin = Phpfox_Plugin::get('user.service_user_getuserfields')) ? eval($sPlugin) : false);
		
		if ($bReturnUserValues)
		{
			$aCache = array();
			foreach ($aFields as $sField)
			{
				if ($sPrefix !== null)
				{
					if ($sField == 'server_id')
					{
						$sField = 'user_' . $sPrefix . $sField;	
					}					
					else 
					{
						$sField = $sPrefix . $sField;
					}					
				}
				
				$aCache[$sField] = ($aUser === null ? Phpfox::getUserBy($sField) : $aUser[$sField]);
			}
			
			return $aCache;
		}
		
		return $aFields;	
	}	
	
	public function getSpamTotal()
	{
		return $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('user'))
			->where('total_spam > ' . (int) Phpfox::getParam('core.auto_deny_items'))
			->execute('getSlaveField');
	}
	
	public function getCredit()
	{
		static $sCredit = null;
		
		if ($sCredit === null)
		{
			if (Phpfox::getUserId())
			{
				$sCredit = $this->database()->select('uf.credit')
					->from(Phpfox::getT('user'), 'u')
					->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
					->where('u.user_id = ' . Phpfox::getUserId())
					->execute('getSlaveField');
			}
			else 
			{
				$sCredit = '0.00';
			}
		}
		
		return $sCredit;
	}
	
	public function getCurrency()
	{
		static $sCredit = null;
		
		if ($sCredit === null)
		{
			if (Phpfox::getUserId())
			{
				$sCredit = $this->database()->select('uf.default_currency')
					->from(Phpfox::getT('user'), 'u')
					->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
					->where('u.user_id = ' . Phpfox::getUserId())
					->execute('getSlaveField');
			}
			else 
			{
				$sCredit = false;
			}
		}
		
		if ($sCredit == null)
		{
			return $this->database()->select('currency_id')
			    ->from(Phpfox::getT('currency'))
			    ->where('is_active = 1 AND is_default = 1')
			    ->execute('getField'); 
		}
		
		return $sCredit;
	}	
	
	public function isAdminUser($iUserId)
	{
		if (ADMIN_USER_ID == Phpfox::getUserBy('user_group_id'))
		{
			return false;
		}
		
		$sUserGroupId = $this->database()->select('user_group_id')
			->from(Phpfox::getT('user'))
			->where('user_id = ' . (int) $iUserId)
			->execute('getField');
		
		if ($sUserGroupId == ADMIN_USER_ID)
		{
			return true;
		}
			
		return false;
	}
	
	public function getProfileBirthDate($aUser)
	{
		static $aUserDetails = null;
		
		if (is_array($aUserDetails))
		{
			return $aUserDetails;
		}
		
		if ($aUserDetails === null)
		{
			$aUserDetails = array();
		}
		
		if (isset($aUser['dob_setting']) && !empty($aUser['birthday']) && $aUser['dob_setting'] != '3')
		{
			$aBirthDay = Phpfox::getService('user')->getAgeArray($aUser['birthday_time_stamp']);
			$sDateExtra = '';			
			switch($aUser['dob_setting'])
			{
				case '1':
					$sDateExtra = '' . Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . '';
					break;	
				case '2':
					$sDateExtra = $aUser['birthday'];
					break;
				default:
					$sDateExtra = '' . Phpfox::getLib('date')->getMonth($aBirthDay['month']) . ' ' . $aBirthDay['day'] . ', ' . $aBirthDay['year'] . '';
					break;
			}
			$aUserDetails[Phpfox::getPhrase('profile.age')] = $sDateExtra;
		}	
		
		return $aUserDetails;
	}


	/**
	 * Gets the count for how many members have been inactive since $iDyas
	 * @param int $iDays
	 * @return int inactive members since $iDays
	 */
	public function getInactiveMembersCount($iDays)
	{
		$iDays = (int)$iDays;
		
		$iCnt = $this->database()->select('COUNT(user_id)')
			->from(Phpfox::getT('user'))					
			->where('profile_page_id = 0 AND last_login < ' .(PHPFOX_TIME - ($iDays * 86400)))
			->execute('getSlaveField');
		
		return $iCnt;
	}

	public function getUserImages()
	{
		$sCacheId = $this->cache()->set(array('user', 'user_welcome_image'));
		
		if (!($aRows = $this->cache()->get($sCacheId, 60)))
		{
			$aRows = $this->database()->select(Phpfox::getUserField())
				->from(Phpfox::getT('user'), 'u')
				->where('u.status_id = 0 AND u.view_id = 0 AND ' . $this->database()->isNotNull('u.user_image'))
				->limit(70)
				->order('u.last_activity DESC')
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		return $aRows;
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
		if ($sPlugin = Phpfox_Plugin::get('user.service_user__call'))
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