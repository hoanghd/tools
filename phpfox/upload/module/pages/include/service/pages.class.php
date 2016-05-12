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
 * @version 		$Id: pages.class.php 3245 2011-10-07 11:44:39Z Miguel_Espinoza $
 */
class Pages_Service_Pages extends Phpfox_Service 
{
	private $_bIsInViewMode = false;
	
	private $_aPage = null;
	
	private $_aRow = array();
	
	private $_bIsInPage = false;
	
	private $_aWidgetMenus = array();
	private $_aWidgetUrl = array();
	private $_aWidgetBlocks = array();
	private $_aWidgets = array();
	private $_aWidgetEdit = array();
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('pages');
	}
	
	public function setMode($bMode = true)
	{
		$this->_bIsInViewMode = $bMode;
	}
	
	public function isViewMode()
	{
		return (bool) $this->_bIsInViewMode;
	}
	
	public function setIsInPage()
	{
		$this->_bIsInPage = true;		
	}
	
	public function isInPage()
	{
		return $this->_bIsInPage;
	}
	
	public function buildWidgets($iId)
	{		
		$aWidgets = $this->database()->select('pw.*, pwt.text_parsed AS text')
			->from(Phpfox::getT('pages_widget'), 'pw')
			->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
			->where('pw.page_id = ' . (int) $iId)
			->execute('getSlaveRows');
		
		foreach ($aWidgets as $aWidget)
		{
			$this->_aWidgetEdit[] = array(
				'widget_id' => $aWidget['widget_id'],
				'title' => $aWidget['title']
			);
			
			$this->_aWidgetMenus[] = array(
				'phrase' => $aWidget['menu_title'],
				'url' => $this->getUrl($aWidget['page_id'], $this->_aRow['title'], $this->_aRow['vanity_url']) . $aWidget['url_title'] . '/',
				'landing' => $aWidget['url_title']
			);
			
			$this->_aWidgetUrl[$aWidget['url_title']] = $aWidget['widget_id'];
			
			if ($aWidget['is_block'])
			{
				$this->_aWidgetBlocks[] = $aWidget;
			}
			else
			{
				$this->_aWidgets[$aWidget['url_title']] = $aWidget;
			}			
		}
	}	
	
	public function getForEditWidget($iId)
	{
		$aWidget = $this->database()->select('pw.*, pwt.text_parsed AS text')
			->from(Phpfox::getT('pages_widget'), 'pw')
			->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
			->where('pw.widget_id = ' . (int) $iId)
			->execute('getSlaveRow');	
		
		if (!isset($aWidget['widget_id']))
		{
			return false;
		}
		
		$aPage = $this->getPage($aWidget['page_id']);
		
		if (!isset($aPage['page_id']))
		{
			return false;
		}
		
		if (!$this->isAdmin($aPage))
		{
			return false;
		}
		
		return $aWidget;
	}
	
	public function getWidgetsForEdit()
	{
		return $this->_aWidgetEdit;
	}
	
	public function isWidget($sUrl)
	{
		return (isset($this->_aWidgetUrl[$sUrl]) ? true : false);
	}
	
	public function getWidget($sUrl)
	{
		return $this->_aWidgets[$sUrl];
	}
	
	public function getWidgetBlocks()
	{
		return $this->_aWidgetBlocks;
	}
	
	public function getForProfile($iUserId)
	{
		$aPages = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField())
			->from(Phpfox::getT('like'), 'l')			
			->join(Phpfox::getT('pages'), 'p', 'p.page_id = l.item_id AND p.view_id = 0')
			->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->where('l.type_id = \'pages\' AND l.user_id = ' . (int) $iUserId)
			->order('l.time_stamp DESC')
			->execute('getSlaveRows');		
		
		foreach ($aPages as $iKey => $aPage)
		{
			$aPages[$iKey]['url'] = $this->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
		}
		
		return $aPages;
	}
	
	public function getForView($mId)
	{
		if ($this->_aPage !== null)
		{
			$mId = $this->_aPage['page_id'];
		}
		
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = p.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}			
		
		$this->_aRow = $this->database()->select('p.*, pu.vanity_url, pg.name AS category_name, pg.page_type, pt.text_parsed AS text, l.like_id AS is_liked, u.full_name')
			->from($this->_sTable, 'p')	
			->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
			->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'pages\' AND l.item_id = p.page_id AND l.user_id = ' . Phpfox::getUserId())
			->where('p.page_id = ' . (int) $mId)			
			->execute('getSlaveRow');
	
		if (!isset($this->_aRow['page_id']))
		{
			return false;
		}
		
		$this->_aRow['is_admin'] = $this->isAdmin($this->_aRow);		
		$this->_aRow['link'] = Phpfox::getService('pages')->getUrl($this->_aRow['page_id'], $this->_aRow['title'], $this->_aRow['vanity_url']);
		
		if ($this->_aRow['page_type'] == '1' && $this->_aRow['reg_method'] == '1')
		{
			$this->_aRow['is_reg'] = (int) $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('pages_signup'))
				->where('page_id = ' . (int) $this->_aRow['page_id'] . ' AND user_id = ' . Phpfox::getUserId())
				->execute('getSlaveField');
		}
		
		if ($this->_aRow['reg_method'] == '2' && Phpfox::isUser())
		{
			$this->_aRow['is_invited'] = (int) $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('pages_invite'))
				->where('page_id = ' . (int) $this->_aRow['page_id'] . ' AND invited_user_id = ' . Phpfox::getUserId())
				->execute('getSlaveField');
			
			if (!$this->_aRow['is_invited'])
			{
				unset($this->_aRow['is_invited']);
			}
		}	
		
		if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			$this->_aRow['is_liked'] = true;
		}		
		
		if ($this->_aRow['app_id'])
		{			
			if ($this->_aRow['aApp'] = Phpfox::getService('apps')->getForPage($this->_aRow['app_id']))
			{
				$this->_aRow['is_app'] = true;
				$this->_aRow['title'] = $this->_aRow['aApp']['app_title'];
				$this->_aRow['category_name'] = 'App';
			}
		}
		else
		{
			$this->_aRow['is_app'] = false;
		}		
		
		return $this->_aRow;
	}
	
	public function isMember($iPage)
	{
		if (empty($this->_aRow))
		{
			$this->_aRow = $this->getForView($iPage);
		}
		
		if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			return true;
		}		
		
		return ((isset($this->_aRow['is_liked']) && $this->_aRow['is_liked']) ? true : false);
	}
	
	public function isAdmin($aPage)
	{
		if (!Phpfox::isUser())
		{
			return false;
		}
		
		if (empty($aPage))
		{
			$aPage = $this->getPage();
		}
		
		if ($aPage['page_id'] == Phpfox::getUserBy('profile_page_id'))
		{
			return true;
		}

		if ($aPage['user_id'] == Phpfox::getUserId())
		{
			return true;
		}
		
		$iAdmin = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('pages_admin'))
			->where('page_id = ' . (int) $aPage['page_id'] . ' AND user_id = ' . (int) Phpfox::getUserId())
			->execute('getSlaveField');
		
		if ($iAdmin)
		{
			return true;
		}
		
		return false;
	}
	
	public function getPage($iId = null)
	{
		static $aRow = null;
		
		if (is_array($aRow) && $iId === null)
		{
			return $aRow;
		}
		
		$aRow = $this->database()->select('p.*, pu.vanity_url, pg.name AS category_name, pg.page_type')
			->from($this->_sTable, 'p')			
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')
			->where('p.page_id = ' . (int) $iId)			
			->execute('getSlaveRow');
		
		if (!isset($aRow['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_looking_for'));
		}
		
		return $aRow;
	}
	
	public function getMyPages()
	{
		$aRows = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')			
			->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->where('p.view_id = 0 AND p.user_id = ' . Phpfox::getUserId())			
			->order('p.time_stamp DESC')
			->execute('getSlaveRows');
		
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['link'] = $this->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
		}
		
		return $aRows;
	}
	
	public function getUrl($iPageId, $sTitle, $sVanityUrl)
	{
		if (!empty($sVanityUrl))
		{
			return Phpfox::getLib('url')->makeUrl($sVanityUrl);
		}

		// return Phpfox::permalink('pages', $iPageId, $sTitle);
		return Phpfox::getLib('url')->makeUrl('pages', $iPageId);
	}
	
	public function isPage($sUrl)
	{
		$aPage = $this->database()->select('*')
			->from(Phpfox::getT('pages_url'))
			->where('vanity_url = \'' . $this->database()->escape($sUrl) . '\'')
			->execute('getSlaveRow');
		
		if (!isset($aPage['page_id']))
		{
			return false;
		}
		
		$this->_aPage = $aPage;
		
		return true;
	}
	
	public function getMenu($aPage)
	{
		$sHomeUrl = Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
		$sCurrentModule = Phpfox::getLib('module')->getModuleName();
		
		$aMenus = array();
		if ($this->isAdmin($aPage))
		{
			$iTotalPendingMembers = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('pages_signup'))
				->where('page_id = ' . (int) $aPage['page_id'])
				->execute('getSlaveField');
			
			if ($iTotalPendingMembers > 0)
			{
				$aMenus[] = array(
					'phrase' => '' . Phpfox::getPhrase('pages.pending_memberships') . '<span class="pending">' . $iTotalPendingMembers . '</span>',
					'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'pending/',
					'icon' => 'misc/comment.png'	
				);
			}
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('pages.wall'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . (empty($aPage['landing_page']) ? '' : 'wall/'),
			'icon' => 'misc/comment.png',
			'landing' => ''
		);
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('pages.info'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'info/',
			'icon' => 'misc/application_view_list.png',
			'landing' => 'info'
		);		
		
		$aModuleCalls = Phpfox::massCallback('getPageMenu', $aPage);
		foreach ($aModuleCalls as $sModule => $aModuleCall)
		{
			if (!is_array($aModuleCall))
			{
				continue;
			}
			$aMenus[] = $aModuleCall[0];
		}
		
		if (count($this->_aWidgetMenus))
		{
			$aMenus = array_merge($aMenus, $this->_aWidgetMenus);
		}
		
		if ($sCurrentModule == 'pages')
		{
			foreach ($aMenus as $iKey => $aMenu)
			{
				$sSubUrl = rtrim(str_replace($sHomeUrl, '', $aMenu['url']), '/');			
								
				if ((Phpfox::getLib('request')->get('req3') == 'info' || Phpfox::getLib('request')->get('req2') == 'info') && $sSubUrl == 'info')
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;					
				}
				
				if ((Phpfox::getLib('request')->get('req3') == 'wall' || Phpfox::getLib('request')->get('req2') == 'wall') && $sSubUrl == 'wall')
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;					
				}				
				
				if ((Phpfox::getLib('request')->get('req3') == 'pending' || Phpfox::getLib('request')->get('req2') == 'pending') && $sSubUrl == 'pending')
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;					
				}				
				
				if (empty($sSubUrl) && Phpfox::getLib('request')->get((empty($aPage['vanity_url']) ? 'req3' : 'req2')) == '')
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;										
				}
				
				if ($sSubUrl == 'info' && $aPage['landing_page'] == 'info' && Phpfox::getLib('request')->get((empty($aPage['vanity_url']) ? 'req3' : 'req2')) == '')
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;										
				}			
				
				if (!empty($sSubUrl) && $sSubUrl == Phpfox::getLib('request')->get((empty($aPage['vanity_url']) ? 'req3' : 'req2')))
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;						
				}
			}
		}
		else
		{			
			foreach ($aMenus as $iKey => $aMenu)
			{
				$sSubUrl = rtrim(str_replace($sHomeUrl, '', $aMenu['url']), '/');			

				if ($sCurrentModule == $sSubUrl)
				{
					$aMenus[$iKey]['is_selected'] = true;
					break;
				}
			}
		}
		
		return $aMenus;	
	}	
	
	public function getForEdit($iId)
	{
		static $aRow = null;
		
		if (is_array($aRow))
		{
			return $aRow;
		}
		
		$aRow = $this->database()->select('p.*, pu.vanity_url, pt.text, pc.page_type')
			->from($this->_sTable, 'p')			
			->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_category'), 'pc', 'p.category_id = pc.category_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')			
			->where('p.page_id = ' . (int) $iId)			
			->execute('getSlaveRow');
		
		if (!isset($aRow['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_trying_to_edit'));
		}
		
		if (!$this->isAdmin($aRow))
		{
			if (!Phpfox::getUserParam('pages.can_moderate_pages'))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('pages.you_are_unable_to_edit_this_page'));
			}
		}
		
		$this->_aRow = $aRow;
		
		Phpfox::getService('pages')->buildWidgets($aRow['page_id']);
		
		$aRow['admins'] = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('pages_admin'), 'pa')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
			->where('pa.page_id = ' . (int) $aRow['page_id'])
			->execute('getSlaveRows');
		
		$aMenus = $this->getMenu($aRow);
		if (!empty($aRow['landing_page']))
		{
			foreach ($aMenus as $iKey => $aMenu)
			{
				if ($aMenu['landing'] == $aRow['landing_page'])
				{
					$aMenus[$iKey]['is_selected'] = true;
				}
			}
		}
		
		$aRow['landing_pages'] = $aMenus;
		
		if ($aRow['app_id'])
		{			
			if ($aRow['aApp'] = Phpfox::getService('apps')->getForPage($aRow['app_id']))
			{
				$aRow['is_app'] = true;
				$aRow['title'] = $aRow['aApp']['app_title'];				
			}
		}
		else
		{
			$aRow['is_app'] = false;
		}			
		
		return $aRow;		
	}
	
	public function getMembers($iPage)
	{
		if (!Phpfox::isModule('like'))
		{
			return false;
		}
		return Phpfox::getService('like')->getForMembers('pages', $iPage);
	}
	
	public function getPerms($iPage)
	{
		$aCallbacks = Phpfox::massCallback('getPagePerms');
		$aPerms = array();
		$aUserPerms = $this->getPermsForPage($iPage);
			
		foreach ($aCallbacks as $aCallback)
		{
			foreach ($aCallback as $sId => $sPhrase)
			{
				$aPerms[] = array(
					'id' => $sId,
					'phrase' => $sPhrase,
					'is_active' => (isset($aUserPerms[$sId]) ? $aUserPerms[$sId] : '0')
				);	
			}			
		}	
		
		return $aPerms;
	}
	
	public function getPermsForPage($iPage)
	{
		static $aPerms = null;
		
		if (is_array($aPerms))
		{
			return $aPerms;
		}
		
		$aPerms = array();
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('pages_perm'))
			->where('page_id = ' . (int) $iPage)
			->execute('getSlaveRows');
		
		foreach ($aRows as $aRow)
		{
			$aPerms[$aRow['var_name']] = (int) $aRow['var_value'];
		}
		
		return $aPerms;
	}
	
	public function getPendingUsers($iPage)
	{
		$aUsers = $this->database()->select('ps.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('pages_signup'), 'ps')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ps.user_id')
			->execute('getSlaveRows');
		
		return $aUsers;
	}
	
	public function hasPerm($iPage = null, $sPerm)
	{
		static $aCheck = array();
		
		if ($iPage === null)
		{
			$iPage = $this->_aRow['page_id'];
		}
		
		if (isset($aCheck[$iPage][$sPerm]))
		{
			return $aCheck[$iPage][$sPerm];
		}
		
		if (isset($this->_aRow['page_id']) && $this->_aRow['user_id'] == Phpfox::getUserId())
		{
			$aCheck[$iPage][$sPerm] = true;
			
			return $aCheck[$iPage][$sPerm];
		}
		
		$bReturn = true;
		$aPerms = $this->getPermsForPage($iPage);
		
		if (isset($aPerms[$sPerm]))		
		{
			switch ((int) $aPerms[$sPerm])
			{
				case 1:
					if (!$this->isMember($iPage))
					{
						$bReturn = false;
					}
					break;
				case 2:
					if (!$this->isAdmin($this->_aRow))
					{
						$bReturn = false;
					}
					break;
			}
		}
		
		$aCheck[$iPage][$sPerm] = $bReturn;
		
		return $aCheck[$iPage][$sPerm];
	}
	
	public function getPendingTotal()
	{
		return (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('pages'))
			->where('app_id = 0 AND view_id = 1')
			->execute('getSlaveField');		
	}		
	
	public function getLastLogin()
	{
		static $aUser = null;
		
		if ($aUser !== null)
		{
			return $aUser;
		}
		
		$this->database()->join(Phpfox::getT('user'), 'u', 'u.user_id = pl.user_id');
		
		if (($sPlugin = Phpfox_Plugin::get('pages.service_pages_getlastlogin')))
		{
			eval($sPlugin);
		}		
		
		$aUser = $this->database()->select(Phpfox::getUserField() . ', u.email, u.style_id, u.password')
			->from(Phpfox::getT('pages_login'), 'pl')			
			->where('pl.login_id = ' . (int) Phpfox::getCookie('page_login') . ' AND pl.page_id = ' . Phpfox::getUserBy('profile_page_id'))
			->execute('getSlaveRow');
		
		if (!isset($aUser['user_id']))
		{
			$aUser = false;
			
			return false;
		}
		
		return $aUser;
	}
	
	public function getMyAdminPages()
	{
		$sCacheId = $this->cache()->set(array('user', 'pages_' . Phpfox::getUserId()));
		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{				
			$this->database()->select('pages.*')
				->from(Phpfox::getT('pages'), 'pages')				
				->where('pages.app_id = 0 AND pages.view_id = 0 AND pages.user_id = ' . Phpfox::getUserId())							
				->union();		

			$this->database()->select('pages.*')
				->from(Phpfox::getT('pages_admin'), 'pa')
				->leftJoin(Phpfox::getT('pages'), 'pages', 'pages.page_id = pa.page_id')				
				->where('pa.user_id = ' . Phpfox::getUserId())							
				->union();		

			$aRows = $this->database()->select('pages.*, pu.vanity_url, ' . Phpfox::getUserField())
				->unionFrom('pages')
				->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = pages.page_id')
				->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = pages.page_id')	
				->group('pages.page_id')
				->execute('getSlaveRows');	

			foreach ($aRows as $iKey => $aRow)
			{
				$aRows[$iKey]['link'] = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			}
			
			$this->cache()->set($sCacheId, $aRows);
		}
		
		if (!is_array($aRows))
		{
			$aRows = array($aRows);
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
		if ($sPlugin = Phpfox_Plugin::get('pages.service_pages__call'))
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