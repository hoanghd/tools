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
 * @version 		$Id: process.class.php 2696 2011-06-30 19:30:33Z Raymond_Benc $
 */
class Mail_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('mail');
	}
	
	public function add($aVals)
	{
		if (isset($aVals['copy_to_self']) && $aVals['copy_to_self'] == 1)
		{
			$aVals['to'][] = Phpfox::getUserId();
			unset($aVals['copy_to_self']);
			return $this->add($aVals);
		}		

		$iSentTo = 0;
		if (is_array($aVals['to']))
		{
			$aCache = array();			
			foreach ($aVals['to'] as $mTo)
			{				
				if ($mTo != Phpfox::getUserId())
				{
					++$iSentTo;
				}
				
				if (Phpfox::getUserParam('mail.send_message_to_max_users_each_time') > 0
					&& $iSentTo > Phpfox::getUserParam('mail.send_message_to_max_users_each_time'))
				{
					return Phpfox_Error::set(Phpfox::getPhrase('mail.too_many_users_this_message_was_sent_to_the_first_total_users', array('total' => Phpfox::getUserParam('mail.send_message_to_max_users_each_time'))));
				}
				
				if (strstr($mTo, ','))
				{
					$aParts = explode(',', $mTo);
					foreach ($aParts as $mUser)
					{					
						$aVals['to'] = trim($mUser);
						
						if (empty($aVals['to']))
						{
							continue;
						}
						
						// Make sure we found a user
						if (($iTemp = $this->add($aVals)) && is_numeric($iTemp))
						{
							$aCache[] = $iTemp;	
						}
					}
				}
				else 
				{
					$aVals['to'] = $mTo;
					
					if (empty($aVals['to']))
					{
						continue;
					}
					
					// Make sure we found a user
					if (($iTemp = $this->add($aVals)) && is_numeric($iTemp))
					{
						$aCache[] = $iTemp;	
					}
				}
				
			}			
			
			if ((Phpfox::getUserParam('mail.can_add_attachment_on_mail') && !empty($aVals['attachment'])) && count($aCache))
			{
				$aLastCache = array_reverse($aCache);
				
				foreach ($aCache as $iMailId)
				{
					$this->database()->update($this->_sTable, array('mass_id' => $aLastCache[0]), 'mail_id = ' . (int) $iMailId);	
				}
			}
			
			return $aCache;	
		}		
		
		$aDetails = Phpfox::getService('user')->getUser($aVals['to'], Phpfox::getUserField() . ', u.email, u.language_id, u.user_group_id', (is_numeric($aVals['to']) ? false : true));
		if (!isset($aDetails['user_id']))
		{
			return false;
		}
		
		if (!Phpfox::getService('user.privacy')->hasAccess($aDetails['user_id'], 'mail.send_message'))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('mail.unable_to_send_a_private_message_to_full_name_as_they_have_disabled_this_option_for_the_moment', array('full_name' => $aDetails['full_name'])));
		}		

		// Check if user is allowed to receive messages: http://forums.phpfox.com/project.php?issueid=2216
		if (Phpfox::getService('user.group.setting')->getGroupParam($aDetails['user_group_id'], 'mail.override_mail_box_limit') == false)
		{
			$iMailBoxLimit = Phpfox::getService('user.group.setting')->getGroupParam($aDetails['user_group_id'], 'mail.mail_box_limit');			
			$iCurrentMessages = $this->database()
				->select('COUNT(viewer_user_id)')
				->from($this->_sTable)
				->where('viewer_user_id = ' . (int)$aVals['to'] . ' AND viewer_type_id != 3 AND viewer_type_id != 1')
				->execute('getSlaveField');
				
			if ($iCurrentMessages >= $iMailBoxLimit)
			{
				return Phpfox_Error::set(Phpfox::getPhrase('mail.user_has_reached_their_inbox_limit'));
			}
		}

		if ($aVals['to'] == Phpfox::getUserId() && !Phpfox::getUserParam('mail.can_message_self'))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('mail.you_cannot_message_yourself'));
		}
		// check if user can send message to non friends: http://forums.phpfox.com/project.php?issueid=2216
		if (Phpfox::getUserParam('mail.restrict_message_to_friends') && !(Phpfox::getService('user.group.setting')->getGroupParam($aDetails['user_group_id'],'mail.override_restrict_message_to_friends')))
		{
			(($sPlugin = Phpfox_Plugin::get('mail.service_process_add_1')) ? eval($sPlugin) : false);
			if (isset($sPluginError))
			{
				return false;
			}
			if (!Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aVals['to']))
			return Phpfox_Error::set(Phpfox::getPhrase('mail.you_can_only_message_your_friends'));
		}
		
		
		$aVals = array_merge($aVals, $aDetails);
		
		$oFilter = Phpfox::getLib('parse.input');
		$oParseOutput = Phpfox::getLib('parse.output');
		
		$bHasAttachments = (Phpfox::getUserParam('mail.can_add_attachment_on_mail') && !empty($aVals['attachment']));		
		
		if (isset($aVals['parent_id']))
		{
			$aMail = $this->database()->select('m.mail_id, m.owner_user_id, m.subject, u.email, u.language_id')
				->from($this->_sTable, 'm')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = m.owner_user_id')
				->where('m.mail_id = ' . (int) $aVals['parent_id'] . ' AND viewer_user_id = ' . Phpfox::getUserId())
				->execute('getSlaveRow');
				
			if (!isset($aMail['mail_id']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('mail.not_a_valid_message'));
			}
			
			$aVals['user_id'] = $aMail['owner_user_id'];
			$aVals['subject'] = $aMail['subject'];
			$aVals['email'] = $aMail['email'];
			$aVals['language_id'] = $aMail['language_id'];
		}
		Phpfox::getService('ban')->checkAutomaticBan($aVals['subject'] . ' ' . $aVals['message']);
		$aVals['subject'] = (isset($aVals['subject']) ? $oFilter->clean($aVals['subject'], 255) : null);
		$aInsert = array(
			'parent_id' => (isset($aVals['parent_id']) ? $aVals['parent_id'] : 0),
			'subject' => $aVals['subject'],
			'preview' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message']))), 255),
			'owner_user_id' => Phpfox::getUserId(),
			'viewer_user_id' => $aVals['user_id'],			
			'viewer_is_new' => 1,
			'time_stamp' => PHPFOX_TIME,
			'time_updated' => PHPFOX_TIME,
			'total_attachment' => ($bHasAttachments ? Phpfox::getService('attachment')->getCount($aVals['attachment']) : 0),
		);

		$iId = $this->database()->insert($this->_sTable, $aInsert);		
	
		$this->database()->insert(Phpfox::getT('mail_text'), array(
				'mail_id' => $iId,
				'text' => $oFilter->clean($aVals['message']),
				'text_parsed' => $oFilter->prepare($aVals['message'])
			)
		);

		// Send the user an email
		$sLink = Phpfox::getLib('url')->makeUrl('mail.view', array('id' => $iId));
		Phpfox::getLib('mail')->to($aVals['user_id'])
			->subject(array('mail.full_name_sent_you_a_message_on_site_title', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title')), false, null,$aVals['language_id']))
			->message(array('mail.full_name_sent_you_a_message_subject_subject', array(
						'full_name' => Phpfox::getUserBy('full_name'),
						'subject' => $aVals['subject'],
						'message' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message'])))),
						'link' => $sLink
					)
				)
			)
			->notification('mail.new_message')
			->send();			
		
		// If we uploaded any attachments make sure we update the 'item_id'
		if ($bHasAttachments)
		{
			Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);			
		}				
		
		(($sPlugin = Phpfox_Plugin::get('mail.service_process_add')) ? eval($sPlugin) : false);		
		
		return $iId;
	}	

	/**
	 * This function is the cron job to delete old messages. It sends messages to the trash can.
	 * Old messages are settable in the admin panel in the setting mail.message_age_to_delete and this function
	 * is ran every mail.cron_delete_messages_delay, it can also be completely shut off with the setting enable_cron_delete_old_mail
	 */
	public function cronDeleteMessages()
	{
		
		// an extra check:
		if (!Phpfox::getParam('mail.enable_cron_delete_old_mail')) return false;
		(($sPlugin = Phpfox_Plugin::get('mail.service_process_cronDeleteMessages_start')) ? eval($sPlugin) : false);
		
		$iTime = (Phpfox::getTime() - (Phpfox::getParam('mail.message_age_to_delete') * CRON_ONE_DAY));
		// delete from trashcan the ones already deleted
		$this->database()->update($this->_sTable, array('viewer_type_id' => 3), 'time_updated < ' . $iTime . ' AND viewer_type_id = 1');
		$this->database()->update($this->_sTable, array('owner_type_id' => 3), 'time_updated < ' . $iTime. ' AND owner_type_id = 1');
		
		(($sPlugin = Phpfox_Plugin::get('mail.service_process_cronDeleteMessages_end')) ? eval($sPlugin) : false);		
	}
	
	public function toggleView($iId, $bRemove = false)
	{		
		$this->database()->update(Phpfox::getT('mail'), array('viewer_is_new' => ($bRemove ? 1 : 0)), 'mail_id = ' . (int) $iId .' AND viewer_user_id = ' . Phpfox::getUserId());			
		
		(($sPlugin = Phpfox_Plugin::get('mail.service_process_toggleview')) ? eval($sPlugin) : false);
		
		return true;
	}	
	
	public function delete($iId, $bSent = false)
	{
		$aMail = Phpfox::getService('mail')->getMail($iId);
		if ($aMail['viewer_user_id'] == $aMail['owner_user_id'])
		{
			$this->database()->update($this->_sTable, array(($bSent === false ? 'owner_type_id' : 'viewer_type_id') => 1), 'mail_id = ' . (int) $iId . ' AND ' . ($bSent === false ? 'viewer_user_id' : 'owner_user_id') . ' = ' . Phpfox::getUserId());
		}
		$this->database()->update($this->_sTable, array(($bSent === false ? 'viewer_type_id' : 'owner_type_id') => 1), 'mail_id = ' . (int) $iId . ' AND ' . ($bSent === false ? 'viewer_user_id' : 'owner_user_id') . ' = ' . Phpfox::getUserId());		

		(($sPlugin = Phpfox_Plugin::get('mail.service_process_delete')) ? eval($sPlugin) : false);
		
		return true;
	}

	/**
	 * Delicate function, physically deletes a message from the mail and mail_text tables
	 * @param int $iId
	 * @return true
	 */
	public function adminDelete($iId)
	{
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getUserParam('mail.can_read_private_messages', true); // they need to see it in order to delete it
		Phpfox::getUserParam('mail.can_delete_others_messages', true);
		
		$aMail = $this->database()->select('mail_id, viewer_user_id')
			->from(Phpfox::getT('mail'))
			->where('mail_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aMail['mail_id']))
		{
			return false;
		}

		// do some logging before deleting?
		$this->database()->delete($this->_sTable, 'mail_id = ' . (int)$iId);
		$this->database()->delete(Phpfox::getT('mail_text'), 'mail_id = ' . (int)$iId);		
		
		return true;
	}
	
	public function deleteTrash($iId)
	{		
		$this->database()->update($this->_sTable, array('viewer_type_id' => 3), 'mail_id = ' . (int) $iId . ' AND viewer_user_id = ' . Phpfox::getUserId() . ' AND viewer_type_id = 1');
		$this->database()->update($this->_sTable, array('owner_type_id' => 3), 'mail_id = ' . (int) $iId . ' AND owner_user_id = ' . Phpfox::getUserId() . ' AND owner_type_id = 1');		
		
		(($sPlugin = Phpfox_Plugin::get('mail.service_process_deletetrash')) ? eval($sPlugin) : false);
		
		return true;
	}	
	
	public function undelete($iId)
	{	
		$this->database()->update($this->_sTable, array('viewer_type_id' => 0), 'mail_id = ' . (int) $iId . ' AND viewer_user_id = ' . Phpfox::getUserId() . ' AND viewer_type_id = 1');
		$this->database()->update($this->_sTable, array('owner_type_id' => 0), 'mail_id = ' . (int) $iId . ' AND owner_user_id = ' . Phpfox::getUserId() . ' AND owner_type_id = 1');		
		
		(($sPlugin = Phpfox_Plugin::get('mail.service_process_undelete')) ? eval($sPlugin) : false);
		
		return true;
	}
	
	public function toggleRead($iId)
	{
		$aMail = $this->database()->select('*')
			->from(Phpfox::getT('mail'))
			->where('mail_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aMail['mail_id']))
		{
			return Phpfox_Error::set('Unable to find the message you are trying to mark as read/unread.');
		}
		
		if ($aMail['viewer_user_id'] != Phpfox::getUserId())
		{
			return Phpfox_Error::set('Unable to modify the message you are trying to mark as read/unread.');
		}
		
		if ($aMail['viewer_is_new'])
		{
			$this->database()->update(Phpfox::getT('mail'), array('viewer_is_new' => '0'), 'mail_id = ' . $aMail['mail_id']);
		}
		else
		{
			$this->database()->update(Phpfox::getT('mail'), array('viewer_is_new' => '1'), 'mail_id = ' . $aMail['mail_id']);
		}
		
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
		if ($sPlugin = Phpfox_Plugin::get('mail.service_process__call'))
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