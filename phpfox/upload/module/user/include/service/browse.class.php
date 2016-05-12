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
 * @version 		$Id: browse.class.php 3246 2011-10-07 12:12:45Z Raymond_Benc $
 */
class User_Service_Browse extends Phpfox_Service
{
	private $_aConditions = array();
	
	private $_sSort = 'u.joined DESC';
	
	private $_iPage = 0;
	
	private $_iLimit = 9;	
	
	private $_bIsOnline = false;
	
	private $_bExtend = false;
	
	private $_aCallback = false;

	/**
	 * boolean show featured or non featured | null: show all
	 * @var mixed
	 */
	private $_mFeatured = null;
	/**
	 * Tells if admin is looking for users pending email verification
	 * @var bool 
	 */
	private $_bPendingVerify = null;
	
	private $_aCustom = false;
	
	private $_bIsGender = false;
	
	private $_sIp = null;
	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('user');
	}
	
	public function conditions($aConditions)
	{
		$this->_aConditions = $aConditions;
		
		return $this;
	}	
	
	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;
		
		return $this;
	}
	
	public function sort($sSort)
	{
		$this->_sSort = $sSort;
		
		if ($this->_sSort == 'u.last_login ASC')
		{
			$this->_aConditions[] = 'AND u.last_login > 0';	
		}
		
		return $this;
	}
	
	public function page($iPage)
	{
		$this->_iPage = $iPage;
		
		return $this;
	}	

	public function pending($bPending)
	{
		$this->_bPendingVerify = (bool)$bPending;
		return $this;
	}
	
	public function featured($bFeatured)
	{
		$this->_mFeatured = $bFeatured;
		
		return $this;
	}
	
	public function limit($iLimit)
	{
		$this->_iLimit = $iLimit;
		
		return $this;
	}	

	public function online($bIsOnline)
	{
		$this->_bIsOnline = $bIsOnline;
		
		return $this;
	}	

	public function extend($bExtend)
	{
		$this->_bExtend = $bExtend;
		
		return $this;
	}
	
	public function custom($mCustom)
	{
		$this->_aCustom = $mCustom;
		
		return $this;
	}
	
	public function gender($bGender)
	{
		$this->_bIsGender = $bGender;
		
		return $this;
	}
	
	public function ip($sIp)
	{
		$this->_sIp = $sIp;
	}
	
	public function get()
	{
		$aUsers = array();
		

		if ($sPlugin = Phpfox_Plugin::get('user.service_browse_get__start')){return eval($sPlugin);}
		$aCustomSearch = array();		
		if (is_array($this->_aCustom))
		{
			$sCondition = ' AND (';
			// When searching for more than one custom field searchFields will 
			// return more than one join instruction
			$aAlias = array();
			$aCustomSearch = Phpfox::getService('custom')->searchFields($this->_aCustom);
			foreach ($aCustomSearch as $aSearchParam)
			{
			    if (is_array($aSearchParam))
			    {				
				if (!in_array($aSearchParam['alias'], $aAlias))
				{
				    $this->database()->leftjoin($aSearchParam['table'], $aSearchParam['alias'], $aSearchParam['on']);
				    $aAlias[] = $aSearchParam['alias'];
				}
				//$this->_aConditions[] = $aSearchParam['where'];
				$sCondition .= $aSearchParam['where'] . ' OR ';
			    }
			    else
			    {
				$sCondition .= $aSearchParam . ' OR ';
				//$this->_aConditions[] = $aSearchParam['where']; 
				//array_merge($this->_aConditions, $aCustomSearch);
			    }
			}
			if ($sCondition != ' AND (')
			{
			    $this->_aConditions[] = rtrim($sCondition, ' OR ') . ')';
			}
		}		
		
		if ($this->_bIsOnline === true)
		{
			$this->database()->select('COUNT(DISTINCT u.user_id)')->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = u.user_id' . (!defined('PHPFOX_IS_ADMIN_SEARCH') ? ' AND ls.im_hide = 0' : '') . '');
		}			
		else 
		{
			if ($this->_sIp !== null)
			{
				$this->database()->select('COUNT(DISTINCT u.user_id)');
			}
			else 
			{
				$this->database()->select('COUNT(*)');
			}
		}
		
		if (count($aCustomSearch))
		{
			$this->database()->leftjoin(Phpfox::getT('user_custom_value'), 'ucv', 'ucv.user_id = u.user_id');
		}

		// one page to display all, one page to display only featured.		
		if ($this->_mFeatured === true)
		{
			$this->database()->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
		}
		
		// check if user is pending mail verification
		if ($this->_bPendingVerify === true)
		{
			$this->database()->join(Phpfox::getT('user_verify'), 'uv', 'uv.user_id = u.user_id')->select(', status_id as unverified')->group('u.user_id');
		}
		
		if ($this->_aCallback !== false && isset($this->_aCallback['query']))
		{
			Phpfox::callback($this->_aCallback['module'] . '.getBrowseQueryCnt', $this->_aCallback);
		}		
		
		if ($this->_sIp !== null)
		{
			$this->database()->join(Phpfox::getT('user_ip'), 'uip', 'uip.user_id = u.user_id AND uip.ip_address = \'' . $this->database()->escape($this->_sIp) . '\'');
		}
	
		$iCnt = $this->database()->from($this->_sTable, 'u')
			->join(Phpfox::getT('user_field'), 'ufield', 'ufield.user_id = u.user_id')
			->where($this->_aConditions)
			//->group('u.user_id')
			->execute('getSlaveField');
		//d($this->_aCustom);
		//d($iCnt, true);die();
		if ($iCnt)
		{
			if ($sPlugin = Phpfox_Plugin::get('user.service_browse_get__cnt')){return eval($sPlugin);}
			$aAlias = array();
			foreach ($aCustomSearch as $aSearchParam)
			{
			    if (is_array($aSearchParam))
			    {				
				if (!in_array($aSearchParam['alias'], $aAlias))
				{
				    $this->database()->leftjoin($aSearchParam['table'], $aSearchParam['alias'], $aSearchParam['on']);
				    $aAlias[] = $aSearchParam['alias'];
				}
			    }
			}
			if (count($aCustomSearch))
			{
				$this->database()->leftjoin(Phpfox::getT('user_custom_value'), 'ucv', 'ucv.user_id = u.user_id');
			}			
			
			if ($this->_bIsOnline === true)
			{
				$this->database()->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = u.user_id' . (!defined('PHPFOX_IS_ADMIN_SEARCH') ? ' AND ls.im_hide = 0' : '') . '')->group('u.user_id');
			}	
			
			if ($this->_aCallback !== false && isset($this->_aCallback['query']))
			{
				Phpfox::callback($this->_aCallback['module'] . '.getBrowseQuery', $this->_aCallback);
			}
			
			if (defined('PHPFOX_IS_ADMIN_SEARCH'))
			{
				$this->database()->select('ug.title AS user_group_title, ')->join(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = u.user_group_id');
				
				// check if user is pending mail verification
				if ($this->_bPendingVerify === true)
				{
					$this->database()->join(Phpfox::getT('user_verify'), 'uv', 'uv.user_id = u.user_id')->select('uv.email as pendingMail, status_id as unverified, ');
				}
			}
			
			// display the Unfeature/Feature option when landing on the Search page.
			// using bIsOnline as its not defined in the admincp but it is on the user browse page
			if ($this->_mFeatured !== true || (Phpfox::getUserParam('user.can_feature') && $this->_bIsOnline)) 
			{
				$this->database()
					->select('uf.user_id as is_featured, uf.ordering as featured_order, ')
					->leftjoin(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');
			}			
			
			// display the Unfeature/Feature option when landing on the Search page.
			if ($this->_mFeatured === true && !$this->_bIsOnline)
			{
				$this->database()
					->select('uf.user_id as is_featured, uf.ordering as featured_order, ')
					->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = u.user_id');					
			}
			
			if (!defined('PHPFOX_IS_ADMIN_SEARCH') && Phpfox::isUser() && Phpfox::isModule('friend'))
			{
				$this->database()->select('friend.friend_id AS is_friend, ')
					->leftJoin(Phpfox::getT('friend'), 'friend', 'friend.user_id = u.user_id AND friend.friend_user_id = ' . Phpfox::getUserId());
			}		

			if ($this->_sIp !== null)
			{
				$this->database()->join(Phpfox::getT('user_ip'), 'uip', 'uip.user_id = u.user_id AND uip.ip_address = \'' . $this->database()->escape($this->_sIp) . '\'');
			}
				
			$aUsers = $this->database()->select('u.status_id as unverified, ' . ($this->_bExtend ? 'u.*, ufield.*' : Phpfox::getUserField()))
				->from($this->_sTable, 'u')
				->join(Phpfox::getT('user_field'), 'ufield', 'ufield.user_id = u.user_id')
				->where($this->_aConditions)
				->order($this->_sSort)
				->limit($this->_iPage, $this->_iLimit, $iCnt)
				->group('u.user_id')
				->execute('getSlaveRows');
				
			if (Phpfox::isModule('friend'))
			{
				foreach ($aUsers as $iKey => $aUser)
				{
					$aUsers[$iKey]['mutual_friends'] = (Phpfox::getUserId() == $aUser['user_id'] ? 0 : $this->database()->select('COUNT(*)')
						->from(Phpfox::getT('friend'), 'f')
						->innerJoin('(SELECT friend_user_id FROM ' . Phpfox::getT('friend') . ' WHERE is_page = 0 AND user_id = ' . $aUser['user_id'] . ')', 'sf', 'sf.friend_user_id = f.friend_user_id')
						->where('f.user_id = ' . Phpfox::getUserId())
						->execute('getSlaveField'));				
				}
			}
				
			if ($this->_bExtend)
			{
				foreach ($aUsers as $iKey => $aUser)
				{
					$aBirthDay = Phpfox::getService('user')->getAgeArray($aUser['birthday']);
					
					$aUsers[$iKey]['month'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']);
					$aUsers[$iKey]['day'] = $aBirthDay['day'];
					$aUsers[$iKey]['year'] = $aBirthDay['year'];
					if (isset($aUser['last_ip_address']))
					{
						$aUsers[$iKey]['last_ip_address_search'] = str_replace('.', '-', $aUser['last_ip_address']);
					}
				}
			}
		}
				
		return array($iCnt, $aUsers);
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
		if ($sPlugin = Phpfox_Plugin::get('user.service_browse__call'))
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