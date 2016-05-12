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
 * @version 		$Id: callback.class.php 3346 2011-10-24 15:20:05Z Raymond_Benc $
 */
class Mail_Service_Callback extends Phpfox_Service 
{
	public function  __construct()
	{
		$this->_sTable = Phpfox::getT('mail');
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => 'Mail',
			'link' => Phpfox::getLib('url')->makeUrl('mail'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_mail.png')),
			'total' => Phpfox::getService('mail')->getUnseenTotal()
		);
	}		
	
	public function addTrack($iId, $iUserId = null)
	{
		return $this->database()->insert(Phpfox::getT('mail_track'), array(
				'item_id' => (int) $iId,
				'user_id' => ($iUserId === null ? Phpfox::getUserBy('user_id') : $iUserId),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}
	
	public function removeTrack($iId, $iUserId = null)
	{
		return $this->database()->delete(Phpfox::getT('mail_track'), 'item_id = ' . (int) $iId . ' AND user_id = ' . ($iUserId === null ? Phpfox::getUserBy('user_id') : $iUserId));
	}	
	
	public function getNotificationSettings()
	{
		return array('mail.new_message' => array(
				'phrase' => Phpfox::getPhrase('mail.new_messages'),
				'default' => 1
			)
		);		
	}
	
	public function getProfileSettings()
	{
		return array(
			'mail.send_message' => array(
				'phrase' => Phpfox::getPhrase('user.send_you_a_message'),
				'default' => '1',
				'anyone' => false
			)			
		);		
	}

	public function getNotificationLink($mId, $mTotal = null)
	{
		$sImage = '<img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/email.png') . '" alt="" class="v_middle" />';	
		if (is_array($mId) && $mTotal === null)
		{	
			return '<li><a href="' . Phpfox::getLib('url')->makeUrl('mail') . '">' . $sImage . ' New Messages (' . (isset($mId['mail']) ? $mId['mail'] : '0') . ')</a></li>';
		}
		else 
		{			
			return '<li><a href="' . Phpfox::getLib('url')->makeUrl('mail') . '" class="js_nofitication_' . $mId . '">' . $sImage . ' ' . ($mTotal > 1 ? Phpfox::getPhrase('mail.total_new_messages', array('total' => $mTotal)) : Phpfox::getPhrase('mail.1_new_message')) . '</a></li>';
		}
	}
	
	public function getAttachmentField()
	{
		return array(
			'mail',
			'mail_id'
		);
	}	
	
	public function getNotificationFeedSend($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('mail.user_link_sent_you_a_message', array(($aRow['user_id'] > 0 ? 'user' : 'user_link') => ($aRow['user_id'] > 0 ? $aRow : Phpfox::getParam('core.site_title')))),
			'link' => Phpfox::getLib('url')->makeUrl('mail.view', array('id' => $aRow['item_id']))
		);
	}	
	
	public function getUserCountFieldSend()
	{
		return 'mail_new';
	}

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{		
		// get all the mail in this user's inbox		
		$aMails = $this->database()
			->select('mail_id, owner_user_id, viewer_user_id')
			->from($this->_sTable)
			->where('owner_user_id = ' . (int)$iUser . ' OR viewer_user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
			
		foreach ($aMails as $aMail)
		{
			if (Phpfox::getParam('mail.delete_sent_when_account_cancel'))
			{// if that setting is enabled then we can do a hard delete:
				$this->database()->delete($this->_sTable, 'mail_id = ' . $aMail['mail_id']);
				$this->database()->delete(Phpfox::getT('mail_text'), 'mail_id = ' . $aMail['mail_id']);
				// soft delete
				//Phpfox::getService('mail.process')->delete($aMail['mail_id'], true);
			}
			else
			{
				$bSent = $aMail['owner_user_id'] == $iUser;
				Phpfox::getService('mail.process')->delete($aMail['mail_id'], $bSent);
			}
		}
		$this->database()->delete(Phpfox::getT('mail_folder'), 'user_id = ' . (int)$iUser);
	}
	
	public function reparserList()
	{
		return array(
			'name' => Phpfox::getPhrase('mail.mail_text'),
			'table' => 'mail_text',
			'original' => 'text',
			'parsed' => 'text_parsed',
			'item_field' => 'mail_id'
		);
	}

	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('mail.mesages_sent'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('mail'))
				->where('time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}	

	public function getReportRedirect($iId)
	{
		return Phpfox::getLib('url')->makeUrl('admincp.mail.view', array('id' => $iId));
	}

	public function updateCounterList()
	{
		$aList = array();		
		
		$aList[] = array(
			'name' => Phpfox::getPhrase('mail.update_mail_count'),
			'id' => 'mail-count'			
		);		
		
		return $aList;
	}			
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('user'))
			->execute('getSlaveField');
			
		$aRows = $this->database()->select('u.user_id')
			->from(Phpfox::getT('user'), 'u')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');						
					
		foreach ($aRows as $aRow)
		{
			$iTotalNewMessages = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('mail'), 'm')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = m.owner_user_id')
				->where('m.viewer_user_id = ' . (int) $aRow['user_id'] . ' AND m.viewer_is_new = 1 AND m.viewer_type_id = 0')
				->execute('getSlaveField');		
				
			$this->database()->update(Phpfox::getT('user_count'), array('mail_new' => $iTotalNewMessages), 'user_id = ' . (int) $aRow['user_id']);
		}		
			
		return $iCnt;		
	}

	public function getSqlTitleField()
	{
		return array(
			'table' => 'mail',
			'field' => 'subject'
		);
	}		

	public function getGlobalNotifications()
	{
		if (Phpfox::isMobile())
		{
			return false;
		}
		
		$iTotal = Phpfox::getService('mail')->getUnseenTotal();
		if ($iTotal > 0)
		{
			Phpfox::getLib('ajax')->call('$(\'#js_total_new_messages\').html(\'' . (int) $iTotal . '\').css({display: \'block\'}).show();');
		}
	}	

	/**
	 * This function checks if the current user is either the sender or the receiver of iMailId
	 * Used to validate who can download attachments
	 * @param int $iMailId
	 * @return bool
	 */
	public function attachmentControl($iMailId)
	{
		$aMail = Phpfox::getService('mail')->getMail($iMailId);
		return ($aMail['owner_user_id'] == Phpfox::getUserId() || $aMail['viewer_user_id'] == Phpfox::getUserId());
	}
	
	public function getApiSupportedMethods()
	{
		$aMethods = array();
		
		$aMethods[] = array(
			'call' => 'getNewCount',
			'requires' => array(
				'user_id' => 'user_id'
			),
			'detail' => 'Get the total number of unseen messages. If you do not pass the #{USER_ID} we will return information about the user that is currently logged in.',
			'type' => 'GET',			
			'response' => '{"api":{"total":0,"pages":0,"current_page":0},"output":1}'
		);		
		
		return array(
			'module' => 'mail', 
			'module_info' => '', 
			'methods' => $aMethods
		);
	}		

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('mail.service_callback__call'))
		{
			return eval($sPlugin);
		}
			
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>