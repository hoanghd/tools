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
 * @package  		Module_Mail
 * @version 		$Id: mail.class.php 3194 2011-09-27 11:50:16Z Raymond_Benc $
 */
class Mail_Service_Mail extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('mail');
	}

	/**
	 * This function validates the permission to send a PM to another user, it 
	 * takes into account the user group setting: mail.can_compose_message
	 * the privacy setting by the receiving user: mail.send_message
	 * and if the receiving user is blocked by the sender user or viceversa
	 * Also checks on other user group based restrictions
	 * @param int $iUser The user id of the member trying to send a message
	 * @return boolean true if its ok to send the message, false otherwise
	 */
	public function canMessageUser($iUser)
	{
		(($sPlugin = Phpfox_Plugin::get('mail.service_mail_canmessageuser_1')) ? eval($sPlugin) : false);
		if (isset($bCanOverrideChecks))
		{
			return true;
		}
		// 1. user group setting:
		if (!Phpfox::getUserParam('mail.can_compose_message'))
		{			
			return false;
		}
		// 2. Privacy setting check
		$iPrivacy = $this->database()->select('user_value')
				->from(Phpfox::getT('user_privacy'))
				->where('user_id = ' . (int)$iUser . ' AND user_privacy = "mail.send_message"')
				->execute('getSlaveField');

		if (!empty($iPrivacy) && !Phpfox::isAdmin())
		{
			if ($iPrivacy == 4) // No one
			{				
				return false;
			}			
			else if($iPrivacy == 1 && !Phpfox::isUser()) // trivial case
			{				
				return false;
			}
			else if ($iPrivacy == 2 && !Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $iUser, false)) // friends only
			{				
				return false;
			}
		}

		// 3. Blocked users		
		if (!Phpfox::isAdmin() && (Phpfox::getService('user.block')->isBlocked(Phpfox::getUserId(), $iUser) > 0 || Phpfox::getService('user.block')->isBlocked($iUser, Phpfox::getUserId()) > 0))
		{			
			return false;
		}

		// 4. Sending message to oneself vs the setting mail.can_message_self
		if ($iUser == Phpfox::getUserId() && !Phpfox::getUserParam('mail.can_message_self'))
		{			
			return false;
		}

		// 5. User group setting (different from check 2 since that is user specific)		
		if ((Phpfox::getUserParam('mail.restrict_message_to_friends') == true)
			&& (Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $iUser, false) == false)
			&& (Phpfox::getUserParam('mail.override_restrict_message_to_friends') == false))
		{
			return false;
		}
		// then its ok
		return true;
	}
	
	public function get($aConds = array(), $sSort = 'm.time_updated DESC', $iPage = '', $iLimit = '', $bIsSentbox = false, $bIsTrash = false)
	{
		$aRows = array();
		$aInputs = array(
			'unread',
			'read'
		);
	
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'm')
			->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id ' . (!$bIsSentbox ? '= m.owner_user_id' : '= m.viewer_user_id'))
			->where($aConds)
			->execute('getSlaveField');

		if ($iCnt)
		{
			(($sPlugin = Phpfox_Plugin::get('mail.service_mail_get')) ? eval($sPlugin) : false);

			if ($bIsTrash)
			{
				$this->database()
					->select(Phpfox::getUserField('u2', 'other_') . ', ')
					->join(Phpfox::getT('user'), 'u2', 'u2.user_id = m.viewer_user_id');
			}

			$aRows = $this->database()->select('m.*, ' . Phpfox::getUserField())
				->from($this->_sTable, 'm')
				->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id ' . (!$bIsSentbox ? '= m.owner_user_id' : '= m.viewer_user_id'))
				->where($aConds)
				->limit($iPage, $iLimit, $iCnt)
				->order($sSort)
				->execute('getSlaveRows');
	
			if (!$bIsSentbox)
			{
				foreach ($aRows as $iKey => $aRow)
				{
					if ($aRow['viewer_is_new'])
					{
						$aInputs['unread'][] = $aRow['mail_id'];
					}
					else
					{
						$aInputs['read'][] = $aRow['mail_id'];
					}
				}
			}
		}

		return array($iCnt, $aRows, $aInputs);
	}

	/**
	 * Gets the percentage used of the mailbox
	 * @param int $iUser
	 * @return int
	 */
	public function getSpaceUsed($iUser)
	{
		$iUsed = $this->database()->select('COUNT(viewer_user_id)')
			->from($this->_sTable)
			->where('viewer_user_id = ' . (int)$iUser . ' AND viewer_type_id != 1 AND viewer_type_id != 3')
			->execute('getSlaveField');
		$iAllowed = Phpfox::getUserParam('mail.mail_box_limit');
		return ceil(($iUsed / $iAllowed) * 100);
	}

	/**
	 * Gets all the mail_id for a specific user in a specific folder.
	 * @param <type> $iUser
	 * @param <type> $iFolder
	 * @param <type> $bIsSentbox
	 * @return <type>
	 */
	public function getAllMailFromFolder($iUser, $iFolder, $bIsSentbox, $bIsTrash)
	{
		$sWhere = '';
		if ($bIsSentbox)
		{
			$sWhere .= (int)$iUser . ' = m.owner_user_id' . ' AND ' . (int)$iFolder. ' = m.owner_folder_id';
		}
		elseif ($bIsTrash)
		{
			$sWhere .= '(m.viewer_user_id = '.(int)$iUser.' AND m.viewer_type_id = 1) OR (m.owner_user_id = '.(int)$iUser.' AND m.owner_type_id = 1)';
		}
		else
		{
			$sWhere .= (int)$iUser . ' = m.viewer_user_id AND ' . (int)$iFolder . ' = m.viewer_folder_id' ;
		}
		$aMails = $this->database()->select('m.mail_id')
				->from($this->_sTable, 'm')				
				->where($sWhere)
				->execute('getSlaveRows');
		$aOut = array();
		foreach ($aMails as $aMail) $aOut[] = $aMail['mail_id'];
		return $aOut;
	}
	
	public function getMail($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('mail.service_mail_getmail')) ? eval($sPlugin) : false);

		$aMail = $this->database()->select('m.*, ' . (Phpfox::getParam('core.allow_html') ? "mreply.text_parsed" : "mreply.text") . ' AS text_reply, ' . (Phpfox::getParam('core.allow_html') ? "mt.text_parsed" : "mt.text") . ' AS text, ' . Phpfox::getUserField('u', 'owner_') . ', ' . Phpfox::getUserField('u2', 'viewer_'))
			->from($this->_sTable, 'm')
			->join(Phpfox::getT('mail_text'), 'mt', 'mt.mail_id = m.mail_id')
			->leftjoin(Phpfox::getT('user'), 'u', 'u.user_id = m.owner_user_id')
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = m.viewer_user_id')
			->leftJoin(Phpfox::getT('mail_text'), 'mreply', 'mreply.mail_id = m.parent_id') /** @TODO PUREFAN changed this */
			->where('m.mail_id = ' . (int) $iId . '')
			->execute('getSlaveRow');
		if (empty($aMail))
		{
			return $aMail;
		}
		
		if ($aMail['viewer_folder_id'] > 0)
		{
			$aMail['folder_name'] = Phpfox::getService('mail.folder')->getFolder($aMail['viewer_folder_id']);
		}		
		
		return $aMail;
	}

	public function getPrev($iTime, $bIsSentbox = false)
	{
		//return array();
		return $this->database()->select('m.mail_id')
			->from($this->_sTable, 'm')
			->where(($bIsSentbox ? 'm.owner_user_id = ' . Phpfox::getUserId() . ' AND m.time_updated > ' . (int) $iTime . '' : 'm.viewer_user_id = ' . Phpfox::getUserId() . ' AND m.time_updated > ' . (int) $iTime . ''))
			->order('m.time_updated ASC')
			->execute('getSlaveField');
	}

	public function getNext($iTime, $bIsSentbox = false)
	{
		//return array();
		return $this->database()->select('m.mail_id')
			->from($this->_sTable, 'm')
			->where(($bIsSentbox ? 'm.owner_user_id = ' . Phpfox::getUserId() . ' AND m.time_updated < ' . (int) $iTime . '' : 'm.viewer_user_id = ' . Phpfox::getUserId() . ' AND m.time_updated < ' . (int) $iTime . ''))
			->order('m.time_updated DESC')
			->execute('getSlaveField');
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
		if ($sPlugin = Phpfox_Plugin::get('mail.service_mail__call'))
		{
			return eval($sPlugin);
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

	public function getDefaultFoldersCount($iUserId)
	{
		// count how many messages are in the inbox folder
		$iCountInbox = $this->database()->select("COUNT(*)")
			->from($this->_sTable, 'm')
			->where('viewer_user_id = ' . $iUserId . ' AND viewer_folder_id = 0 AND viewer_type_id = 0')
			->execute('getSlaveField');

		// how many messages are in the sent folder
		$iCountSentbox = $this->database()->select("COUNT(*)")
			->from($this->_sTable, 'm')
			->where('owner_user_id = ' . $iUserId . ' AND owner_folder_id = 0 AND owner_type_id = 0')
			->execute('getSlaveField');

		// How many messages are in the deleted folder
		$iCountDeleted = $this->database()->select("COUNT(*)")
			->from($this->_sTable, 'm')
			->where('(owner_user_id = ' . $iUserId . ' AND owner_folder_id = 0 AND owner_type_id = 1) OR (viewer_user_id = ' . $iUserId . ' AND viewer_folder_id = 0 AND viewer_type_id = 1)')
			->execute('getSlaveField');

		return array(
			'iCountInbox' => $iCountInbox,
			'iCountSentbox' => $iCountSentbox,
			'iCountDeleted' => $iCountDeleted);
	}
	
	public function getLatest()
	{
		$aRows = $this->database()->select('m.*, ' . Phpfox::getUserField())
			->from($this->_sTable, 'm')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = m.owner_user_id')
			->where('m.viewer_folder_id = 0 AND m.viewer_user_id = ' . Phpfox::getUserId() . ' AND m.viewer_type_id = 0')
			->order('m.time_stamp DESC')
			->limit(5)
			->execute('getSlaveRows');
			
		$sIds = '';
		foreach ($aRows as $aRow)
		{
			$sIds .= $aRow['mail_id'] . ',';
		}
		$sIds = rtrim($sIds, ',');
		
		if (!empty($sIds))
		{
			$this->database()->update($this->_sTable, array('viewer_is_new' => '0'), 'mail_id IN(' . $sIds . ')');
		}
			
		return $aRows;
	}

	/**
	 * We needed a different join so instead of adding another param or loading extra the $this->get() function
	 * its more practical to create a new function with stepping
	 */
	public function getPrivate($aConds, $iLimit,  $sSort, $iPage = 0)
	{
		$bFeatured = false;
		$bText = false;
		$bJoinSender = false;
		foreach ($aConds as $iKey => $sCond)
		{
			if ($sCond == 'FEATURED_2')
			{
				$aConds[] = 'AND uf.user_id > 0';
				$bFeatured = true;
				unset($aConds[$iKey]);
			}
			elseif($sCond == 'FEATURED_1')
			{
				unset($aConds[$iKey]);
			}
			elseif(strpos($sCond, 'mt.text') !== false)
			{
				$bText = true;
			}
			if (strpos($sCond, 'SENDER=') !== false)
			{				
				$bJoinSender = true;
				$aConds[$iKey] = 'AND sender.user_name = "' . Phpfox::getLib('parse.input')->clean(str_replace('SENDER=','',$sCond)) . '"';
			}
			if (strpos($sCond, 'RECEIVER=') !== false)
			{				
				$aConds[$iKey] = 'AND receiver.user_name = "' . Phpfox::getLib('parse.input')->clean(str_replace('RECEIVER=','',$sCond)) . '"';
				$this->database()->join(Phpfox::getT('user'), 'receiver', 'receiver.user_id = m.viewer_user_id');
			}
		}
		
		$aConds = array_merge(array('m.owner_user_id != 0'), $aConds);
		if ($bJoinSender)
		{
			$this->database()->join(Phpfox::getT('user'), 'sender', 'sender.user_id = m.owner_user_id');
		}
		else
		{
			$this->database()->leftjoin(Phpfox::getT('user'), 'sender', 'sender.user_id = m.owner_user_id')			;
		}
		$this->database()
			->select('COUNT(m.mail_id)')
			->from($this->_sTable, 'm')
			//->leftjoin(Phpfox::getT('user'), 'sender', 'sender.user_id = m.owner_user_id')
			->order($sSort)
			->where($aConds);
		
		if ($bFeatured)
		{			
			$this->database()->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = sender.user_id');
		}
		if ($bText)
		{
			$this->database()->leftjoin(Phpfox::getT('mail_text'), 'mt', 'mt.mail_id = m.mail_id');
		}
		
		$iCnt = $this->database()->execute('getSlaveField');

		if ($iCnt > 0)
		{
			if (is_int($iLimit) && $iLimit > 0 )
			{
				$this->database()->limit($iPage, $iLimit, $iCnt);
			}
			
			$this->database()
				->select('m.subject, m.mail_id, m.time_stamp, ' . Phpfox::getUserField('sender', 'sender_') . ', ' . Phpfox::getUserField('receiver', 'receiver_'))
				->from($this->_sTable, 'm')
				->join(Phpfox::getT('user'), 'sender', 'sender.user_id = m.owner_user_id')
				->join(Phpfox::getT('user'), 'receiver', 'receiver.user_id = m.viewer_user_id')
				->order($sSort)
				->where($aConds);
				
			if ($bFeatured)
			{				
				$this->database()->join(Phpfox::getT('user_featured'), 'uf', 'uf.user_id = sender.user_id');
			}
			if ($bText)
			{
				$this->database()->leftjoin(Phpfox::getT('mail_text'), 'mt', 'mt.mail_id = m.mail_id');
			}
			$aMail = $this->database()->execute('getSlaveRows');

			return array($aMail, $iCnt);
		}

		return array(array(), 0);
	}

	public function isDeleted($iMail)
	{
		$iValue = $this->database()->select('mail_id')
			->from($this->_sTable)
			->where('(viewer_user_id = '.Phpfox::getUserId().' AND viewer_type_id = 1) OR (owner_user_id = '.Phpfox::getUserId().' AND owner_type_id = 1)')
			->execute('getSlaveField');
		return ($iValue == $iMail);
		//(m.viewer_user_id = 2 AND m.viewer_type_id = 1) OR (m.owner_user_id = 2 AND m.owner_type_id = 1)
	}

	public function isSent($iMail)
	{
		$iValue = $this->database()->select('mail_id')
			->from($this->_sTable)
			->where('mail_id = ' . (int)$iMail . ' AND owner_user_id = ' . Phpfox::getUserId())
			->execute('getSlaveField');
		return ($iValue == $iMail);
	}
	
	public function getUnseenTotal()
	{
		$iCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('mail'), 'm')
			->where('m.viewer_folder_id = 0 AND m.viewer_user_id = ' . Phpfox::getUserId() . ' AND m.viewer_type_id = 0 AND m.viewer_is_new = 1')
			->execute('getSlaveField');
			
		return $iCnt;
	}
	
	public function buildMenu()
	{
		$aFilterMenu = array(
			'Inbox' => '',
			'Sent Messages' => 'sent',
			'Trash' => 'trash'			
		);		
		
		$aFilterMenu[] = true;
		
		$aFolders = Phpfox::getService('mail.folder')->get();
		foreach ($aFolders as $aFolder)
		{
			$aFilterMenu[$aFolder['name']] = 'mail.view_box.id_' . $aFolder['folder_id'];
		}
		
		Phpfox::getLib('template')->buildSectionMenu('mail', $aFilterMenu);	
	}
}

?>