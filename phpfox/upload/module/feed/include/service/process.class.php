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
 * @package  		Module_Feed
 * @version 		$Id: process.class.php 3180 2011-09-26 13:47:10Z Raymond_Benc $
 */
class Feed_Service_Process extends Phpfox_Service 
{	
	private $_bAllowGuest = false;
	private $_iLastId = 0;
	private $_aCallback = array();
	private $_bIsCallback = false;
	private $_bIsNewLoop = false;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('feed');
	}
	
	public function callback($aCallback)
	{
		if (isset($aCallback['module']))
		{
			$this->_bIsCallback = true;
			$this->_aCallback = $aCallback;			
		}
		
		return $this;
	}
	
	public function allowGuest()
	{
		$this->_bAllowGuest = true;
		
		return $this;
	}			
	
	public function add($sType, $iItemId, $iPrivacy = 0, $iPrivacyComment = 0, $iParentUserId = 0, $iOwnerUserId = null)
	{
		//Plugin call
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process_add__start'))
		{
			eval($sPlugin);
		}

		if ((!Phpfox::isUser() && $this->_bAllowGuest === false) || (defined('PHPFOX_SKIP_FEED') && PHPFOX_SKIP_FEED))
		{
			return false;
		}
		
		if ($iParentUserId === null)
        {
			$iParentUserId = 0;
		}
				
		$aInsert = array(
			'privacy' => (int) $iPrivacy,
			'privacy_comment' => (int) $iPrivacyComment,
			'type_id' => $sType,
			'user_id' => ($iOwnerUserId === null ? Phpfox::getUserId() : (int) $iOwnerUserId),
			'parent_user_id' => $iParentUserId,
			'item_id' => $iItemId,
			'time_stamp' => PHPFOX_TIME			
		);
		
		if (defined('PHPFOX_APP_ID'))
		{
			$aInsert['app_id'] = PHPFOX_APP_ID;
		}
		//Plugin call
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process_add__end'))
		{
			eval($sPlugin);
		}

		if ($this->_bIsNewLoop)
		{
			$this->database()->insert(Phpfox::getT('feed'), $aInsert);
		}
		else
		{
			$this->_iLastId = $this->database()->insert(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed'), $aInsert);
		}
		
		if ($this->_bIsCallback && $this->_aCallback['module'] == 'pages' && !$this->_bIsNewLoop && $iParentUserId > 0)
		{			
			$aUser = $this->database()->select('user_id')
				->from(Phpfox::getT('user'))
				->where('profile_page_id = ' . (int) $iParentUserId)
				->execute('getSlaveRow');
			if (isset($aUser['user_id']) && Phpfox::getUserId() == $aUser['user_id'])
			{			
				$this->_bIsNewLoop = true;
				$this->_bIsCallback  = false;
				$this->_aCallback = array();
				$this->add($sType, $iItemId, $iPrivacy, $iPrivacyComment);
			}
		}
		
		return $this->_iLastId;
	}
	
	public function update($sType, $iItemId, $iPrivacy = 0, $iPrivacyComment = 0)
	{		
		$this->database()->update($this->_sTable, array(
				'privacy' => (int) $iPrivacy,
				'privacy_comment' => (int) $iPrivacyComment,
			), 'type_id = \'' . $this->database()->escape($sType) . '\' AND item_id = ' . (int) $iItemId
		);
		
		return true;
	}	
	
	/**
	 * Deletes an entry from the feeds
	 *
	 * @param string $sType module as defined in: type_id
	 * @param integer $iId numeric as defined in item_id
	 */
	public function delete($sType, $iId, $iUser = false)
	{		
		$aFeeds = $this->database()->select('feed_id, user_id')
			->from(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed'))
			->where('type_id = \'' . $sType . '\' AND item_id = ' . (int) $iId . ($iUser != false ? ' AND user_id = ' . (int)$iUser : ''))
			->execute('getRows');
			
		foreach ($aFeeds as $aFeed)
		{			
			// $this->cache()->remove('feed_' . $aFeed['user_id'], 'substr');			
			if ($iUser != false)
			{
				$this->database()->delete(Phpfox::getT('feed'), 'feed_id = ' . $aFeed['feed_id']);
			}
		}
		if ($iUser == false)
		{
			$this->database()->delete(Phpfox::getT('feed'), 'type_id = \'' . $sType . '\' AND item_id = ' . (int) $iId);
		}
	}
	
	public function deleteChild($sType, $iId)
	{		
		$this->database()->delete(Phpfox::getT('feed'), 'type_id = \'' . $sType . '\' AND child_item_id = ' . (int) $iId);
	}
	
	public function deleteFeed($iId, $sModule = null, $iItem = 0)
	{
		$aCallback = null;
		if (!empty($sModule))
		{
			if (Phpfox::hasCallback($sModule, 'getFeedDetails'))
			{
				$aCallback = Phpfox::callback($sModule . '.getFeedDetails', $iItem);
			}
		}
				
		$aFeed = Phpfox::getService('feed')->callback($aCallback)->getFeed($iId);
		if (!isset($aFeed['feed_id']))
		{			
			return false;
		}
		
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process_deletefeed'))
		{
			eval($sPlugin);
		}		
		
		$bCanDelete = false;
		if (Phpfox::getUserParam('feed.can_delete_own_feed') && ($aFeed['user_id'] == Phpfox::getUserId()))
		{
			$bCanDelete = true;
		}
		
		if (defined('PHPFOX_FEED_CAN_DELETE'))
		{
			$bCanDelete = true;
		}
		
		if (Phpfox::getUserParam('feed.can_delete_other_feeds'))
		{
			$bCanDelete = true;
		}		

		if ($bCanDelete === true)
		{
			$this->database()->delete(Phpfox::getT((isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : '') . 'feed'), 'feed_id = ' . (int) $iId);				
			
			// $this->cache()->remove('feed_' . $aFeed['user_id'], 'substr');			
			
			return true;
		}
		
		return false;
	}	

	public function addComment($aVals)
	{		
		if (empty($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}
		
		if (empty($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		if (empty($aVals['parent_user_id']))
		{
			$aVals['parent_user_id'] = 0;
		}
		
		$sStatus = $this->preParse()->prepare($aVals['user_status']);
		
		$iStatusId = $this->database()->insert(Phpfox::getT(($this->_bIsCallback ? $this->_aCallback['table_prefix'] : '') . 'feed_comment'), array(
				'user_id' => (int) Phpfox::getUserId(),
				'parent_user_id' => (int) $aVals['parent_user_id'],
				'privacy' => $aVals['privacy'],
				'privacy_comment' => $aVals['privacy_comment'],
				'content' => $sStatus,
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		if ($this->_bIsCallback)
		{
			$sLink = $this->_aCallback['link'] . 'comment-id_' . $iStatusId . '/';
	
			if (!empty($this->_aCallback['notification']))
			{
				Phpfox::getLib('mail')->to($this->_aCallback['email_user_id'])
					->subject($this->_aCallback['subject'])
					->message(sprintf($this->_aCallback['message'], $sLink))
					->send();			

				Phpfox::getService('notification.process')->add($this->_aCallback['notification'], $iStatusId, $this->_aCallback['email_user_id']);		
			}
			
			return Phpfox::getService('feed.process')->add($this->_aCallback['feed_id'], $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id']);			
		}
		
		$aUser = $this->database()->select('user_name')
			->from(Phpfox::getT('user'))
			->where('user_id = ' . (int) $aVals['parent_user_id'])
			->execute('getRow');
		
		$sLink = Phpfox::getLib('url')->makeUrl($aUser['user_name'], array('comment-id' => $iStatusId));
		
		Phpfox::getLib('mail')->to($aVals['parent_user_id'])
			->subject(Phpfox::getUserBy('full_name') . ' wrote a comment on your wall.')
			->message("" . Phpfox::getUserBy('full_name') . " wrote a comment on your <a href=\"" . $sLink . "\">wall</a>.\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			->send();
			
		Phpfox::getService('notification.process')->add('feed_comment_profile', $iStatusId, $aVals['parent_user_id']);		
		if (isset($aVals['feed_type']))
		{
			return Phpfox::getService('feed.process')->add($aVals['feed_type'], $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id']);
		}
		return Phpfox::getService('feed.process')->add('feed_comment', $iStatusId, $aVals['privacy'], $aVals['privacy_comment'], (int) $aVals['parent_user_id']);
	}
	
	public function getLastId()
	{
		return (int) $this->_iLastId;
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
		$aDeprecated = array(
			'approve',
			'like',
			'rate',	
			'updateCommentText',
			'deleteLikes'
		);
		
		if (in_array($sMethod, $aDeprecated))
		{
			return Phpfox_Error::set('Method deprecated since 2.1.0beta1');	
		}
		
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('feed.service_process__call'))
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