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
 * @version 		$Id: im.class.php 3344 2011-10-24 14:21:16Z Miguel_Espinoza $
 */
class Im_Service_Im extends Phpfox_Service 
{	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('im');
	}
	
	public function getChat($iId, $bStatic = true)
	{
		static $aChat;
		
		if ($aChat && $bStatic)
		{
			return $aChat;
		}
		
		$aChat = $this->database()->select('i.im_id, i.parent_id, ls.user_id AS is_logged_in, ls.im_status, ls.im_hide, ib.user_id AS is_blocked_user, ' . Phpfox::getUserField())
			->from($this->_sTable, 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.owner_user_id')
			->leftJoin(Phpfox::getT('log_session'), 'ls', 'ls.user_id = i.owner_user_id')
			->leftJoin(Phpfox::getT('user_blocked'), 'ib', '(ib.user_id = i.owner_user_id AND ib.block_user_id = ' . Phpfox::getUserId() . ')')
			->where('i.parent_id = ' . (int) $iId . ' AND i.user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRow');
			
		if (!isset($aChat['im_id']))
		{
			return false;
		}
			
		if ($aChat['is_blocked_user'] == true)
		{
			$aChat['is_logged_in'] = 0;
		}
		
		if ($aChat['im_hide'] > 0)
		{
			$aChat['is_logged_in'] = 0;
		}
		
		return $aChat;
	}
	
	/**
	 * This function gets all the chat sessions for Phpfox::getUserId() given a set of 
	 * Rooms.
	 * Using this function is like calling this->getChat multiple times minus the multiple
	 * calls to the database (its all done in one call)
	 * @param array $aRooms 
	 * @param array $aLastMessage array( parent_id : text_id ) to fetch only recent messages
	 * @deprecated v3 build 2
	 */
	public function getChats($aRooms, $aLastMessage)
	{
		return Phpfox_Error::set('This function has been deprecated and will be removed in the near future');
		$sWhere = '';
		foreach ($aRooms as $iKey => $aRoom)
		{			
			$sWhere .= '(i.parent_id = '.(int)$aRoom['parent_id'];
			/* Check if we have a last message for this room */
			if (isset($aLastMessage[$aRoom['parent_id']]))
			{
				$sWhere .= ' AND text_id > ' . (int)$aLastMessage[$aRoom['parent_id']];
			}
			if ($aRoom['chat_user_id'] != Phpfox::getUserId() && $aRoom['user_id'] != Phpfox::getUserId())
			{
				/* chat_user_id OR user_id must be Phpfox::getUserId() */
				return false;
			}
			
			/* Only get messages sent by the other party */
			if ($aRoom['chat_user_id'] != Phpfox::getUserId())
			{
				$sWhere .= ' AND user_id = ' . (int)$aRoom['chat_user_id'];
			}
			else if ($aRoom['user_id'] != Phpfox::getUserId())
			{
				$sWhere . ' AND user_id = ' . (int)$aRoom['user_id'];
			}
			$sWhere .= ') OR';
		}
		$sWhere = rtrim($sWhere, 'OR');
		if (empty($sWhere))
		{
			return false;
		}
		// And now just like in getChat
		$aChats = $this->database()->select('i.im_id, i.parent_id, ls.user_id AS is_logged_in, ls.im_status, ls.im_hide, ib.user_id AS is_blocked_user, ' . Phpfox::getUserField())
			->from($this->_sTable, 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.owner_user_id')
			->leftJoin(Phpfox::getT('log_session'), 'ls', 'ls.user_id = i.owner_user_id')
			->leftJoin(Phpfox::getT('user_blocked'), 'ib', '(ib.user_id = i.owner_user_id AND ib.block_user_id = ' . Phpfox::getUserId() . ')')
			->where($sWhere)
			->execute('getSlaveRows');
		
		
		if (empty($aChats[0]))
		{
			return false;
		}
		
		/* Hide users who are blocked or hiding their status */
		foreach ($aChats as $iKey => $aChat)
		{
			if ($aChat['is_blocked_user'] == true || $aChat['im_hide'] > 0)
			{
				$aChats[$iKey]['is_logged_in'] = 0;
			}
			/* Now we get the messages, we do it in this loop because a limit per room
			 * must be respected
			 */
			$aChats[$iKey]['aMessages'] = $this->getMessages($aChat['parent_id']);
		}
		
		
		
		return $aChats;
	}
	
	public function getOnlineFriendsCount($iUserId)
	{
		$iCnt = $this->database()->select('COUNT(DISTINCT u.user_id)')
			->from(Phpfox::getT('friend'), 'f')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
			->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = f.friend_user_id AND ls.im_hide = 0')
			->where('f.user_id = ' . (int) $iUserId . ' AND u.im_hide != 1')
			->execute('getSlaveField');

		if ($iCnt > 0)
		{
			$aBlocked = $this->database()->select('ib.block_user_id')
				->from(Phpfox::getT('user_blocked'), 'ib')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = ib.block_user_id')
				->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = ib.block_user_id')
				->where('ib.user_id = ' . (int) $iUserId . ' OR ib.block_user_id = ' . (int) $iUserId)
				->group('u.user_id')
				->execute('getSlaveRows');
	
			foreach ($aBlocked as $aUser)
			{				
				$iCnt--;	
			}
		}
			
		return $iCnt;
	}
	
	public function getOnlineFriends($iUserId, $aCond, $iPage = '', $sLimit = '')
	{
		$iCnt = $this->getOnlineFriendsCount($iUserId);
		
		$aUsers = $this->database()->select('im.parent_id, ib.user_id AS is_blocked_user, ls.im_status,ls.last_activity, u.status, ' . Phpfox::getUserField())
			->from(Phpfox::getT('friend'), 'f')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
			->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = f.friend_user_id AND ls.im_hide = 0')
			->leftJoin(Phpfox::getT('user_blocked'), 'ib', '(ib.user_id = f.friend_user_id AND ib.block_user_id = ' . (int) $iUserId . ') OR (ib.user_id = ' . (int) $iUserId . ' AND ib.block_user_id = f.friend_user_id)')
			->leftJoin(Phpfox::getT('im'), 'im', 'im.user_id = u.user_id')
			// ->where('f.user_id = ' . (int) $iUserId)
			->where($aCond)
			->limit($iPage, $sLimit, $iCnt)
			->order('u.full_name ASC')
			->group('u.user_id')
			->execute('getSlaveRows');
		
		foreach ($aUsers as $iKey => $aUser)
		{
			if ($aUser['is_blocked_user'])
			{
				$iCnt--;
				unset($aUsers[$iKey]);
			}
			
			if ($aUser['last_activity'] < (PHPFOX_TIME - (Phpfox::getParam('log.active_session')*60)))
			{				
				$iCnt--;
				unset($aUsers[$iKey]);
			}
			
			if ($aUser['profile_page_id'])
			{
				$iCnt--;
				unset($aUsers[$iKey]);
			}
		}
		
		return array(count($aUsers), $aUsers);
	}
	
	public function isUserOnline($iUserId)
	{
		if (!is_int($iUserId) || empty($iUserId))
		{
			return false;
		}
		$iUser = $this->database()->select('u.user_id')
				->from(Phpfox::getT('user'),'u')
				->join(Phpfox::getT('log_session'), 'ls','ls.user_id = u.user_id AND ls.im_hide = 0')
				->where('u.im_hide != 1 AND u.user_id = ' . (int)$iUserId)
				->execute('getSlaveField');
		return $iUser == $iUserId;
	}
	
	/**
	 * Gets the chat conversations active for the current user
	 * is_active: 
	 *			0 User has closed the conversation (im.link)
	 *			1 User has the conversation open but minimized
	 *			2 User has the conversation open and with focus
	 * 
	 * @return array 
	 */
	public function getRooms()
	{
		$aRooms = $this->database()->select('i.*,i.is_active as is_active, u.im_hide as im_hide2, i.user_id as chat_user_id, ls.user_id AS is_logged_in, ls.im_status, ls.im_hide, ib.user_id AS is_blocked_user, ' . Phpfox::getUserField())
			->from($this->_sTable, 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.owner_user_id')
			->leftJoin(Phpfox::getT('log_session'), 'ls', 'ls.user_id = i.owner_user_id')
			->leftJoin(Phpfox::getT('user_blocked'), 'ib', '(ib.user_id = i.owner_user_id AND ib.block_user_id = ' . Phpfox::getUserId() . ')')
			->where('i.user_id = ' . Phpfox::getUserId() . ' ')
			->group('u.user_id')
			->order('i.ordering ASC')
			->execute('getSlaveRows');
		
		foreach ($aRooms as $iKey => $aRoom)
		{
			if ($aRooms[$iKey]['is_blocked_user'])
			{
				$aRooms[$iKey]['is_logged_in'] = 0;
			}			
			
			if ($aRooms[$iKey]['im_hide'] > 0)
			{
				$aRooms[$iKey]['is_logged_in'] = 0;
			}			
		}
			
		return $aRooms;
	}
	
	public function canAddAlert($iUserId, $iId)
	{
		$aAlert = $this->database()->select('is_seen')
			->from(Phpfox::getT('im_alert'))
			->where('user_id = ' . (int) $iUserId . ' AND room_id = ' . (int) $iId . '')
			->execute('getSlaveRow');		

		if (!isset($aAlert['is_seen']))		
		{
			return true;
		}
		
		return ($aAlert['is_seen'] == '0' ? true : false);
	}	
	
	public function hasAlert($iUserId, $iId)
	{
		$aAlert = $this->database()->select('is_seen')
			->from(Phpfox::getT('im_alert'))
			->where('user_id = ' . (int) $iUserId . ' AND room_id = ' . (int) $iId . '')
			->execute('getSlaveRow');			
			
		$this->database()->update(Phpfox::getT('im_alert'), array('is_seen' => '1'), 'user_id = ' . (int) $iUserId . ' AND room_id = ' . (int) $iId);		
		
		return ((isset($aAlert['is_seen']) && $aAlert['is_seen'] == '0') ? true : false);
	}
	
	public function getLastMessageForRoom($iRoom)
	{
		return $this->database()->select('is_new')->from($this->_sTable)->where('parent_id = ' . (int)$iRoom . ' AND user_id = ' . Phpfox::getUserId())->execute('getSlaveField');
	}
	
	/**
	 * Gets the messages associated with a specific chat conversation
	 * @param int $iId chat id (`phpfox_im_text`.`parent_id`)
	 * @param int $iLastMessage `phpfox_im_text`.`text_id`
	 * @return type 
	 */
	public function getMessages($iId, $iLastMessage = null)
	{
		$aMessages = $this->database()->select('it.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('im_text'), 'it')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = it.user_id')
			->where('it.parent_id = ' . (int) $iId . ($iLastMessage !== null ? ' AND text_id > ' . (int)$iLastMessage : ''))
			->order('it.text_id ASC')
			->limit(50)
			->execute('getSlaveRows');			
		
		//$aMessages = array_reverse($aMessages);
		
		$iLastId = 0;//$aMessages[0]['text_id'];
		foreach ($aMessages as $aMessage)
		{
			if ($aMessage['text_id'] > $iLastId)
			{
				$iLastId = $aMessage['text_id'];
			}
		}
		if ($iLastMessage !== null && !empty($aMessages))
		{
			$this->database()->update($this->_sTable, array('is_new' => $iLastId), 'parent_id = ' . (int)$iId . ' AND user_id = ' . Phpfox::getUserId());
		}
		$iLastOwner = 0;
		foreach ($aMessages as $iKey => $aMessage)
		{
			$aMessages[$iKey]['is_today'] = (Phpfox::getTime('d', $aMessage['time_stamp']) == Phpfox::getTime('d') ? true : false);
			$aMessages[$iKey]['last_owner'] = $iLastOwner;
			
			$iLastOwner = $aMessage['user_id'];
		}
		
		return $aMessages;
	}
	
	public function getStatuses()
	{
		$aStatuses = array(
			'0' => Phpfox::getPhrase('im.online'),
			'1' => Phpfox::getPhrase('im.away'),
			'2' => Phpfox::getPhrase('im.appear_offline')
		);
		
		return $aStatuses;
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
		if ($sPlugin = Phpfox_Plugin::get('im.service_im__call'))
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