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
 * @version 		$Id: process.class.php 3332 2011-10-20 12:50:29Z Raymond_Benc $
 */
class Pages_Service_Process extends Phpfox_Service 
{
	private $_bHasImage = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('pages');
	}
	
	public function deleteWidget($iId)
	{
		$aWidget = $this->database()->select('*')
			->from(Phpfox::getT('pages_widget'))
			->where('widget_id = '. (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aWidget['widget_id']))
		{
			return false;
		}
		
		$aPage = Phpfox::getService('pages')->getPage($aWidget['page_id']);
		
		if (!isset($aPage['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_looking_for'));
		}
		
		if (!Phpfox::getService('pages')->isAdmin($aPage))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_delete_this_widget'));
		}
		
		$this->database()->delete(Phpfox::getT('pages_widget'), 'widget_id = ' . (int) $iId);
		$this->database()->delete(Phpfox::getT('pages_widget_text'), 'widget_id = ' . (int) $iId);
		
		return true;
	}
	
	public function addWidget($aVals, $iEditId = null)
	{
		$aPage = Phpfox::getService('pages')->getPage($aVals['page_id']);
		
		if (!isset($aPage['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_looking_for'));
		}
		
		if (!Phpfox::getService('pages')->isAdmin($aPage))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_add_a_widget_to_this_page'));
		}
		
		if (empty($aVals['title']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.provide_a_title_for_your_widget'));
		}
		
		if (empty($aVals['text']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.provide_content_for_your_widget'));
		}		
		
		if (!$aVals['is_block'])
		{
			if (empty($aVals['menu_title']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('pages.provide_a_menu_title_for_your_widget'));
			}			
			
			if (empty($aVals['url_title']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('pages.provide_a_url_title_for_your_widget'));
			}			
		}
		
		if (Phpfox::isModule($aVals['url_title']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.you_cannot_use_this_url_for_your_widget'));
		}
		
		$oFilter = Phpfox::getLib('parse.input');
		
		if (!$aVals['is_block'])
		{
			$sNewTitle = Phpfox::getLib('parse.input')->prepareTitle('pages', $aVals['url_title'], 'url_title', Phpfox::getUserId(), Phpfox::getT('pages_widget'), 'page_id = ' . (int) $aPage['page_id'] . ' AND url_title LIKE \'%' . $aVals['url_title'] . '%\'');
		}
		
		$aSql = array(
			'page_id' => $aPage['page_id'],
			'title' => $aVals['title'],
			'is_block' => (int) $aVals['is_block'],
			'menu_title' => ($aVals['is_block'] ? null : $aVals['menu_title']),
			'url_title' => ($aVals['is_block'] ? null : $sNewTitle)
		);		
		
		if ($iEditId === null)
		{			
			$aSql['time_stamp'] = PHPFOX_TIME;
			$aSql['user_id'] = Phpfox::getUserId();

			$iId = $this->database()->insert(Phpfox::getT('pages_widget'), $aSql);

			$this->database()->insert(Phpfox::getT('pages_widget_text'), array(
					'widget_id' => $iId,
					'text' => $oFilter->clean($aVals['text']),
					'text_parsed' => $oFilter->prepare($aVals['text'])
				)
			);		
		}
		else
		{
			$this->database()->update(Phpfox::getT('pages_widget'), $aSql, 'widget_id = ' . (int) $iEditId);
			$this->database()->update(Phpfox::getT('pages_widget_text'), array(
					'text' => $oFilter->clean($aVals['text']),
					'text_parsed' => $oFilter->prepare($aVals['text'])
				), 'widget_id = ' . (int) $iEditId
			);			
			
			$iId = $iEditId;
		}
		
		return $iId;
	}
	
	public function updateWidget($iId, $aVals)
	{
		return $this->addWidget($aVals, $iId);
	}
	
	public function updateCategory($iId, $aVals)
	{
		if (!empty($aVals['type_id']))
		{
			$this->database()->update(Phpfox::getT('pages_category'), array(
					'type_id' => (int) $aVals['type_id'],
					'name' => $this->preParse()->clean($aVals['name']),
					'page_type' => (int) $aVals['page_type']					
				), 'category_id = ' . (int) $iId
			);	
		}
		else
		{
			 $this->database()->update(Phpfox::getT('pages_type'), array(
					'name' => $this->preParse()->clean($aVals['name'])					
				), 'type_id = ' . (int) $iId
			);
		}
		
		$this->cache()->remove('pages', 'substr');		
		
		return true;
	}
	
	public function addCategory($aVals)
	{
		if (!empty($aVals['type_id']))
		{
			$iId = $this->database()->insert(Phpfox::getT('pages_category'), array(
					'type_id' => (int) $aVals['type_id'],
					'is_active' => '1',
					'name' => $this->preParse()->clean($aVals['name']),
					'page_type' => (int) $aVals['page_type']					
				)
			);			
		}
		else
		{
			$iId = $this->database()->insert(Phpfox::getT('pages_type'), array(
					'is_active' => '1',
					'name' => $this->preParse()->clean($aVals['name']),
					'time_stamp' => PHPFOX_TIME
				)
			);
		}
		
		$this->cache()->remove('pages', 'substr');
		
		return $iId;
	}
	
	public function updateActivity($iId, $iType, $iSub)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
	
		$this->database()->update(($iSub ? Phpfox::getT('pages_category') : Phpfox::getT('pages_type')), array('is_active' => (int) ($iType == '1' ? 1 : 0)), ($iSub ? 'category_id' : 'type_id') . ' = ' . (int) $iId);
		
		$this->cache()->remove('pages', 'substr');
	}	
	
	public function deleteCategory($iId, $bIsSub = false)
	{
		if ($bIsSub)
		{
			$this->database()->delete(Phpfox::getT('pages_category'), 'category_id = ' . (int) $iId);
		}
		else
		{
			$this->database()->delete(Phpfox::getT('pages_type'), 'type_id = ' . (int) $iId);
		}
		
		$this->cache()->remove('pages', 'substr');
		
		return true;
	}
	
	public function add($aVals)
	{ 
		$iViewId = (Phpfox::getUserParam('pages.approve_pages') ? '1' : '0');
		if (empty($aVals['title']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.page_name_cannot_be_empty'));
		}
		if (defined('PHPFOX_APP_CREATED'))
		{
			$iViewId = 0;
		}
		$aInsert = array(
			'view_id' => $iViewId,
			'type_id' => (isset($aVals['type_id']) ? (int) $aVals['type_id'] : 0),
			'app_id' => (isset($aVals['app_id']) ? (int)$aVals['app_id'] : 0),
			'category_id' => (isset($aVals['category_id']) ? (int) $aVals['category_id'] : 0),
			'user_id' => Phpfox::getUserId(),
			'title' => $this->preParse()->clean($aVals['title']),			
			'time_stamp' => PHPFOX_TIME
		);
		
		$iId = $this->database()->insert($this->_sTable, $aInsert);
		
		$this->database()->insert(Phpfox::getT('pages_text'), array('page_id' => $iId));
		
		$sSalt = '';
		for ($i = 0; $i < 3; $i++)
		{
			$sSalt .= chr(rand(33, 91));
		}		
		
		$sPossible = Phpfox::getParam('captcha.captcha_code');
      	$sPassword = '';
      	$i = 0;
      	while ($i < 10) 
      	{ 
			$sPassword .= substr($sPossible, mt_rand(0, strlen($sPossible)-1), 1);
      	   	$i++;
      	}  		
		
		$iUserId = $this->database()->insert(Phpfox::getT('user'), array(
				'profile_page_id' => $iId,
				'user_group_id' => NORMAL_USER_ID,
				'view_id' => '7',
				'full_name' => $this->preParse()->clean($aVals['title']),
				'joined' => PHPFOX_TIME,
				'password' => Phpfox::getLib('hash')->setHash($sPassword, $sSalt),
				'password_salt' => $sSalt
			)
		);
		
		$aExtras = array(
			'user_id' => $iUserId
		);

		$this->database()->insert(Phpfox::getT('user_activity'), $aExtras);
		$this->database()->insert(Phpfox::getT('user_field'), $aExtras);
		$this->database()->insert(Phpfox::getT('user_space'), $aExtras);
		$this->database()->insert(Phpfox::getT('user_count'), $aExtras);
		
		$this->cache()->remove(array('user', 'pages_' . Phpfox::getUserId()));
		
		return $iId;
	}
	
	public function update($iId, $aVals, $aPage)
	{		
		if (!$this->_verify($aVals, true))
		{
			return false;
		}
		
		$aUser = $this->database()->select('user_id')
			->from(Phpfox::getT('user'))
			->where('profile_page_id = ' . (int) $iId)
			->execute('getSlaveRow');

		$aUpdate = array(		
			'type_id' => (isset($aVals['type_id']) ? (int) $aVals['type_id'] : '0'),
			'category_id' => (isset($aVals['category_id']) ? (int) $aVals['category_id'] : 0),
			'reg_method' => (isset($aVals['reg_method']) ? (int) $aVals['reg_method'] : 0),
			'landing_page' => $aVals['landing_page'],
			'privacy' => (isset($aVals['privacy']) ? (int) $aVals['privacy'] : 0)			
		);
		
		if (!empty($aVals['title']))
		{
			$aUpdate['title'] = $this->preParse()->clean($aVals['title']);
		}
		
		if ($this->_bHasImage)
		{			
			if (!empty($aPage['image_path']))
			{
				$this->deleteImage($aPage);
			}
			
			$oImage = Phpfox::getLib('image');
			
			$sFileName = Phpfox::getLib('file')->upload('image', Phpfox::getParam('pages.dir_image'), $iId);
			$iFileSizes = filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));			
			
			$aUpdate['image_path'] = $sFileName;
			$aUpdate['image_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
			
			$iSize = 50;			
			$oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);			
			$iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));			
			
			$iSize = 120;			
			$oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);			
			$iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));

			$iSize = 200;			
			$oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);			
			$iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));
			
			define('PHPFOX_PAGES_IS_IN_UPDATE', true);
			
			Phpfox::getService('user.process')->uploadImage($aUser['user_id'], true, Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));
			
			// Update user space usage
			Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'pages', $iFileSizes);
		}		
		
		$this->database()->update($this->_sTable, $aUpdate, 'page_id = ' . (int) $iId);
		
		$this->database()->update(Phpfox::getT('pages_text'), array(
			'text' => $this->preParse()->clean($aVals['text']), 
			'text_parsed' => $this->preParse()->prepare($aVals["text"])
		), 'page_id = ' . (int) $iId);		
		
		if (isset($aVals['invite']) && is_array($aVals['invite']))
		{
			$aNewPage = Phpfox::getService('pages')->getForEdit($aPage['page_id']);
			
			$sUserIds = '';
			foreach ($aVals['invite'] as $iUserId)
			{
				if (!is_numeric($iUserId))
				{
					continue;
				}
				$sUserIds .= $iUserId . ',';
			}
			$sUserIds = rtrim($sUserIds, ',');
			
			$aUsers = $this->database()->select('user_id, email, language_id, full_name')
				->from(Phpfox::getT('user'))
				->where('user_id IN(' . $sUserIds . ')')
				->execute('getSlaveRows');
				
				
			$bSent = false;
			foreach ($aUsers as $aUser)
			{
				if (isset($aCachedEmails[$aUser['email']]))
				{
					continue;
				}	
				
				if (isset($aInvited['user'][$aUser['user_id']]))
				{
					continue;
				}
				
				$sLink = Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']);

				$sMessage = Phpfox::getPhrase('pages.full_name_invited_you_to_the_page_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aNewPage['title']));
				$sMessage .= "\n" . Phpfox::getPhrase('pages.to_view_this_page_click_the_link_below_a_href_link_link_a', array('link' => $sLink)) . "\n";
			
				$bSent = Phpfox::getLib('mail')->to($aUser['user_id'])						
					->subject(Phpfox::getPhrase('pages.full_name_sent_you_a_page_invitation', array('full_name' => Phpfox::getUserBy('full_name'))))
					->message($sMessage)					
					->send();
						
				if ($bSent)
				{					
					$iInviteId = $this->database()->insert(Phpfox::getT('pages_invite'), array(
							'page_id' => $iId,								
							'user_id' => Phpfox::getUserId(),
							'invited_user_id' => $aUser['user_id'],
							'time_stamp' => PHPFOX_TIME
						)
					);
					
					(Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('pages_invite', $iId, $aUser['user_id']) : null);
				}
			}
			if ($bSent == true)
			{
				Phpfox::addMessage(Phpfox::getPhrase('pages.invitations_sent_out'));
			}
		}		
		
		$aUserCache = array();
		$this->database()->delete(Phpfox::getT('pages_admin'), 'page_id = ' . (int) $iId);
		$aAdmins = Phpfox::getLib('request')->getArray('admins');
		if (count($aAdmins))
		{
			foreach ($aAdmins as $iAdmin)
			{
				if (isset($aUserCache[$iAdmin]))
				{
					continue;
				}
				
				$aUserCache[$iAdmin] = true;
				$this->database()->insert(Phpfox::getT('pages_admin'), array('page_id' => $iId, 'user_id' => $iAdmin));
				
				$this->cache()->remove(array('user', 'pages_' . $iAdmin));
			}			
		}		
		
		if (isset($aVals['perms']))
		{
			$this->database()->delete(Phpfox::getT('pages_perm'), 'page_id = ' . (int) $iId);
			foreach ($aVals['perms'] as $sPermId => $iPermValue)
			{
				$this->database()->insert(Phpfox::getT('pages_perm'), array('page_id' => (int) $iId, 'var_name' => $sPermId, 'var_value' => (int) $iPermValue));
			}
		}
		
	
		$this->database()->update(Phpfox::getT('user'), array('full_name' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255)), 'profile_page_id = ' . (int) $iId);		
		
		return true;
	}
	
	public function deleteImage($aPage)
	{	
		if (!empty($aPage['image_path']))
		{
			$aImages = array(
				Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], ''),
				Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_50'),
				Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_120'),
				Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_200')
			);			
			
			$iFileSizes = 0;
			foreach ($aImages as $sImage)
			{
				if (file_exists($sImage))
				{
					$iFileSizes += filesize($sImage);
					
					@unlink($sImage);
				}
			}
			
			if ($iFileSizes > 0)
			{
				Phpfox::getService('user.space')->update($aPage['user_id'], 'pages', $iFileSizes, '-');
			}
		}

		$this->database()->update($this->_sTable, array('image_path' => null), 'page_id = ' . (int) $aPage['page_id']);	
		
		return true;
	}	
	
	public function updateTitle($iId, $sNewTitle)
	{
		$aTitle = $this->database()->select('*')	
			->from(Phpfox::getT('pages_url'))
			->where('page_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (isset($aTitle['vanity_url']))
		{
			$this->database()->update(Phpfox::getT('pages_url'), array('vanity_url' => $sNewTitle), 'page_id = ' . (int) $iId);
		}
		else
		{
			$this->database()->insert(Phpfox::getT('pages_url'), array('vanity_url' => $sNewTitle, 'page_id' => (int) $iId));
		}
		
		$this->database()->update(Phpfox::getT('user'), array('user_name' => $sNewTitle), 'profile_page_id = ' . (int) $iId);
		
		return true;
	}
	
	public function register($iId)
	{
		$aPage = $this->database()->select('*')
			->from(Phpfox::getT('pages'))
			->where('page_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aPage['page_id']))
		{
			return false;
		}
		
		$this->database()->insert(Phpfox::getT('pages_signup'), array(
				'page_id' => $iId,
				'user_id' => Phpfox::getUserId(),
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		return true;
	}
	
	public function moderation($aModerations, $sAction)
	{
		$iCnt = 0;
		foreach ($aModerations as $iModeration)
		{
			$iCnt++;
			// if ($iCnt === 1)
			{
				$aPage = $this->database()->select('p.*, ps.user_id AS post_user_id')
					->from(Phpfox::getT('pages_signup'), 'ps')
					->join(Phpfox::getT('pages'), 'p', 'p.page_id = ps.page_id')
					->where('ps.signup_id = ' . (int) $iModeration)
					->execute('getSlaveRow');
				
				if (!isset($aPage['page_id']))
				{
					return Phpfox_Error::display(Phpfox::getPhrase('pages.unable_to_find_the_page'));
				}
				
				if (!Phpfox::getService('pages')->isAdmin($aPage))
				{
					return Phpfox_Error::display(Phpfox::getPhrase('pages.unable_to_moderate_this_page'));
				}
			}
			
			if ($sAction == 'approve')
			{
				Phpfox::getService('like.process')->add('pages', $aPage['page_id'], $aPage['post_user_id']);
			}
			
			$this->database()->delete(Phpfox::getT('pages_signup'), 'signup_id = ' . (int) $iModeration);
		}
		
		return true;
	}
	
	public function login($iPageId)
	{
		$aPage = $this->database()->select('p.*, p.user_id AS owner_user_id, u.*')
			->from(Phpfox::getT('pages'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
			->where('p.page_id = ' . (int) $iPageId)
			->execute('getSlaveRow');
		
		if (!isset($aPage['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_trying_to_login_to'));
		}
		
		$iCurrentUserId = Phpfox::getUserId();
		
		$bCanLogin = false;
		if ($aPage['owner_user_id'] == Phpfox::getUserId())
		{
			$bCanLogin = true;
		}
		
		if (!$bCanLogin)
		{
			$iAdmin = (int) $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('pages_admin'))
				->where('page_id = ' . (int) $aPage['page_id'] . ' AND user_id = ' . (int) Phpfox::getUserId())
				->execute('getSlaveField');

			if ($iAdmin)
			{
				$bCanLogin = true;
			}			
		}
		
		if (!$bCanLogin)
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_log_in_as_this_page'));
		}
		
		$sPasswordHash = Phpfox::getLib('hash')->setRandomHash(Phpfox::getLib('hash')->setHash($aPage['password'], $aPage['password_salt']));

		$iTime = 0;
		Phpfox::setCookie('user_id', $aPage['user_id'], $iTime);
		Phpfox::setCookie('user_hash', $sPasswordHash, $iTime);
		
		Phpfox::getLib('session')->remove(Phpfox::getParam('core.theme_session_prefix') . 'theme');

		$this->database()->update(Phpfox::getT('user'), array('last_login' => PHPFOX_TIME), 'user_id = ' . $aPage['user_id']);
		$this->database()->insert(Phpfox::getT('user_ip'), array(
				'user_id' => $aPage['user_id'],
				'type_id' => 'login',
				'ip_address' => Phpfox::getIp(),
				'time_stamp' => PHPFOX_TIME
			)
		);	
		
		$iLoginId = $this->database()->insert(Phpfox::getT('pages_login'), array(
				'page_id' => $aPage['page_id'],
				'user_id' => $iCurrentUserId,
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		Phpfox::setCookie('page_login', $iLoginId, $iTime);
		
		return true;
	}
	
	public function clearLogin($iUserId)
	{
		$this->database()->delete(Phpfox::getT('pages_login'), 'user_id = ' . (int) $iUserId);
		
		Phpfox::setCookie('page_login', '', -1);
	}
	
	public function delete($iId)
	{		
		$aPage = $this->database()->select('*')
			->from(Phpfox::getT('pages'))
			->where('page_id = ' . (int) $iId)
			->execute('getSlaveRow');

		if (!isset($aPage['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_trying_to_delete'));
		}
		
		if ($aPage['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('pages.can_moderate_pages'))
		{
			/*
			$aTables = array(
				'pages',
				'pages_admin',
				'pages_invite',
				'pages_perm',
				'pages_signup',
				'pages_text',
				'pages_url'
			);
			
			foreach ($aTables as $sTable)
			{
				
			}
			*/
			
			$this->database()->update(Phpfox::getT('pages'), array('view_id' => '2'), 'page_id = ' . (int) $aPage['page_id']);
			$this->database()->update(Phpfox::getT('user'), array('user_name' => null), 'profile_page_id = ' . (int) $aPage['page_id']);
			$this->database()->delete(Phpfox::getT('pages_url'), 'page_id = ' . (int) $aPage['page_id']);
			$this->database()->delete(Phpfox::getT('feed'), 'type_id = \'pages_itemLiked\' AND item_id = ' . (int) $aPage['page_id']);
			
			return true;
		}
		
		return Phpfox_Error::set(Phpfox::getPhrase('pages.you_are_unable_to_delete_this_page'));
	}
	
	public function approve($iId)
	{
		if (!Phpfox::getUserParam('pages.can_moderate_pages'))
		{
			return false;
		}
		
		$aPage = Phpfox::getService('pages')->getPage($iId);

		if (!isset($aPage['page_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_find_the_page_you_are_trying_to_approve'));
		}
		
		if ($aPage['view_id'] != '1')
		{
			return false;
		}
		
		$this->database()->update(Phpfox::getT('pages'), array('view_id' => '0', 'time_stamp' => PHPFOX_TIME), 'page_id = ' . $aPage['page_id']);
		
		if (Phpfox::isModule('notification'))
		{
			Phpfox::getService('notification.process')->add('pages_approved', $aPage['page_id'], $aPage['user_id']);
		}		
		
		Phpfox::getService('user.activity')->update($aPage['user_id'], 'pages');
		
		// Send the user an email
		$sLink = Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
		Phpfox::getLib('mail')->to($aPage['user_id'])
			->subject(Phpfox::getPhrase('pages.page_title_approved', array('title' => $aPage['title'])))
			->message(Phpfox::getPhrase('pages.your_page_title_has_been_approved', array('title' => $aPage['title'], 'link' => $sLink)))
			->send();		
		
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
		if ($sPlugin = Phpfox_Plugin::get('pages.service_process__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
	
	private function _verify(&$aVals, $bIsUpdate = false)
	{	
		if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
		{
			$aImage = Phpfox::getLib('file')->load('image', array(
					'jpg',
					'gif',
					'png'
				), (Phpfox::getUserParam('pages.max_upload_size_pages') === 0 ? null : (Phpfox::getUserParam('pages.max_upload_size_pages') / 1024))
			);
			
			if ($aImage === false)
			{
				return false;
			}
			
			$this->_bHasImage = true;
		}

		return true;	
	}	
}

?>