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
 * @version 		$Id: feed.class.php 3301 2011-10-17 07:41:33Z Miguel_Espinoza $
 */
class Feed_Service_Feed extends Phpfox_Service 
{	
	private $_aViewMoreFeeds = array();
	private $_aCallback = array();
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('feed');

		(($sPlugin = Phpfox_Plugin::get('feed.service_feed___construct')) ? eval($sPlugin) : false);
	}
	
	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;
		
		return $this;
	}
	
	public function setTable($sTable)
	{
		$this->_sTable = $sTable;
	}

	public function get($iUserid = null, $iFeedId = null, $iPage = 0, $bForceReturn = false)
	{
		$oUrl = Phpfox::getLib('url');
		$oReq = Phpfox::getLib('request');
		$oParseOutput = Phpfox::getLib('parse.output');
		
		if ($oReq->get('get-new'))
		{
			// $bForceReturn = true;
		}
		
		if (($iCommentId = $oReq->getInt('comment-id')))
		{
			if (isset($this->_aCallback['feed_comment']))
			{
				$aCustomCondition = array('feed.type_id = \'' . $this->_aCallback['feed_comment'] . '\' AND feed.item_id = ' . (int) $iCommentId . ' AND feed.parent_user_id = ' . (int) $this->_aCallback['item_id']);
			}
			else
			{
				$aCustomCondition = array('feed.type_id IN(\'feed_comment\', \'feed_egift\') AND feed.item_id = ' . (int) $iCommentId . ' AND feed.parent_user_id = ' . (int) $iUserid);
			}

			$iFeedId = true;
		}
		elseif (($iStatusId = $oReq->getInt('status-id')))
		{
			$aCustomCondition = array('feed.type_id = \'user_status\' AND feed.item_id = ' . (int) $iStatusId . ' AND feed.user_id = ' . (int) $iUserid);
			$iFeedId = true;
		}
		elseif (($iLinkId = $oReq->getInt('link-id')))
		{
			$aCustomCondition = array('feed.type_id = \'link\' AND feed.item_id = ' . (int) $iLinkId . ' AND feed.user_id = ' . (int) $iUserid);
			$iFeedId = true;
		}
		elseif (($iPokeId = $oReq->getInt('poke-id')))
		{
			$aCustomCondition = array('feed.type_id = \'poke\' AND feed.item_id = ' . (int) $iPokeId . ' AND feed.user_id = ' . (int) $iUserid);
			$iFeedId = true;
		}			
		
		$iTotalFeeds = (int) Phpfox::getComponentSetting(($iUserid === null ? Phpfox::getUserId() : $iUserid), 'feed.feed_display_limit_' . ($iUserid !== null ? 'profile' : 'dashboard'), Phpfox::getParam('feed.feed_display_limit'));			
		
		$iOffset = ($iPage * $iTotalFeeds);
		
		(($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_start')) ? eval($sPlugin) : false);

		$aCond = array();
		if (isset($this->_aCallback['module']))
		{
			$aNewCond = array();
			if (($iCommentId = $oReq->getInt('comment-id')))
			{
				if (!isset($this->_aCallback['feed_comment']))
				{
					$aCustomCondition = array('feed.type_id = \'event_comment\' AND feed.item_id = ' . (int) $iCommentId . '');
				}				
			}			
			$aNewCond[] = 'AND feed.parent_user_id = ' . (int) $this->_aCallback['item_id'];
			if ($iUserid !== null && $iFeedId !== null)
			{
				$aNewCond[] = 'AND feed.feed_id = ' . (int) $iFeedId . ' AND feed.user_id = ' . (int) $iUserid;	
			}
			
			$aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT($this->_aCallback['table_prefix'] . 'feed'), 'feed')			
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')			
				->where((isset($aCustomCondition) ? $aCustomCondition : $aNewCond))
				->order('feed.time_stamp DESC')
				->limit($iOffset, $iTotalFeeds)
				->execute('getSlaveRows');					
		}
        elseif (($sIds = $oReq->get('ids')))
        {
			$aParts = explode(',', $oReq->get('ids'));
			$sNewIds = '';
			foreach ($aParts as $sPart)
			{
				$sNewIds .= (int) $sPart . ',';
			}
            $sNewIds = rtrim($sNewIds, ',');
            
            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField())
				->from($this->_sTable, 'feed')			
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')	
                ->where('feed.feed_id IN(' . $sNewIds . ')')            
				->order('feed.time_stamp DESC')
				->execute('getSlaveRows');	            
        }
		elseif ($iUserid !== null && $iFeedId !== null)
		{            
            $aRows = $this->database()->select('feed.*, apps.app_title, ' . Phpfox::getUserField())
				->from($this->_sTable, 'feed')			
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->leftJoin(Phpfox::getT('app'), 'apps', 'apps.app_id = feed.app_id')
				->where((isset($aCustomCondition) ? $aCustomCondition : 'feed.feed_id = ' . (int) $iFeedId . ' AND feed.user_id = ' . (int) $iUserid))
				->order('feed.time_stamp DESC')
				->limit(1)			
				->execute('getSlaveRows');			
		}
		elseif ($iUserid !== null)
		{
			if ($iUserid == Phpfox::getUserId())
			{
				$aCond[] = 'AND feed.privacy IN(0,1,2,3,4)';
			}
			else 
			{
				if (Phpfox::getService('user')->getUserObject($iUserid)->is_friend)
				{
					$aCond[] = 'AND feed.privacy IN(0,1,2)';
				}	
				else if (Phpfox::getService('user')->getUserObject($iUserid)->is_friend_of_friend)
				{
					$aCond[] = 'AND feed.privacy IN(0,2)';
				}
				else 
				{
					$aCond[] = 'AND feed.privacy IN(0)';
				}
			}
			
			$this->database()->select('feed.*')
				->from($this->_sTable, 'feed')
				// ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
				->where(array_merge($aCond, array('AND feed.user_id = ' . (int) $iUserid)))
				->union();
			
			if (Phpfox::isUser())
			{
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')				
					->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
					->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '')
					->where('feed.privacy IN(4) AND feed.user_id = ' . (int) $iUserid)							
					->union();					
			}
			
			$this->database()->select('feed.*')
				->from($this->_sTable, 'feed')
				// ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
				->where(array_merge($aCond, array('AND feed.parent_user_id = ' . (int) $iUserid)))
				->union();
			
			$aRows = $this->database()->select('feed.*, apps.app_title,  ' . Phpfox::getUserField())
				// ->from($this->_sTable, 'feed')
				->unionFrom('feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->leftJoin(Phpfox::getT('app'), 'apps', 'apps.app_id = feed.app_id')
				// ->where($aCond)
				->order('feed.time_stamp DESC')
				->limit($iOffset, $iTotalFeeds)			
				->execute('getSlaveRows');			
		}
		else
		{
			// Users must be active within 7 days or we skip their activity feed
			$iLastActiveTimeStamp = ((int) Phpfox::getParam('feed.feed_limit_days') <= 0 ? 0 : (PHPFOX_TIME - (86400 * Phpfox::getParam('feed.feed_limit_days'))));			
			
			if (Phpfox::getParam('feed.feed_only_friends'))
			{					
				// Get my friends feeds
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')
					->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
					->where('feed.privacy IN(0,1,2) AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')
					->union();
					
				// Get my feeds
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')
					->where('feed.privacy IN(0,1,2,3,4) AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')
					->union();					
			}
			else 
			{
				// Get my friends feeds
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')
					->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
					->where('feed.privacy IN(1,2) AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')
					->union();		

				// Get my friends of friends feeds
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')
					->join(Phpfox::getT('friend'), 'f1', 'f1.user_id = feed.user_id')
					->join(Phpfox::getT('friend'), 'f2', 'f2.user_id = ' . Phpfox::getUserId() . ' AND f2.friend_user_id = f1.friend_user_id')					
					->where('feed.privacy IN(2) AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')
					->union();				
					
				// Get my feeds
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')
					->where('feed.privacy IN(1,2,3,4) AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')
					->union();
					
				// Get public feeds
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')
					->where('feed.privacy IN(0) AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')
					->union();					
					
				// Get feeds based on custom friends lists	
				$this->database()->select('feed.*')
					->from($this->_sTable, 'feed')				
					->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
					->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '')
					->where('feed.privacy IN(4) AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'')							
					->union();				
			}			
				
			$aRows = $this->database()->select('feed.*, f.friend_id AS is_friend, apps.app_title, ' . Phpfox::getUserField())
					->unionFrom('feed')			
					->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')			
					->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
					->leftJoin(Phpfox::getT('app'), 'apps', 'apps.app_id = feed.app_id')
					->order('feed.time_stamp DESC')
					->group('feed.feed_id')
					->limit($iOffset, $iTotalFeeds)			
					->execute('getSlaveRows');		
		}	

		if ($bForceReturn === true)
		{
			return $aRows;
		}
		
		$bFirstCheckOnComments = false;
		if (Phpfox::getParam('feed.allow_comments_on_feeds') && Phpfox::isUser() && Phpfox::isModule('comment'))
		{
			$bFirstCheckOnComments = true;	
		}
		
		$iLoopMaxCount = Phpfox::getParam('feed.group_duplicate_feeds');		
		$aFeedLoop = array();
		$aLoopHistory = array();
		if ($iLoopMaxCount > 0)
		{
			foreach ($aRows as $iKey => $aRow)
			{				
				$sFeedKey = $aRow['user_id'] . $aRow['type_id'] . date('dmyH', $aRow['time_stamp']);	
				
				if (isset($aFeedLoop[$sFeedKey]))
				{
					if (!isset($aLoopHistory[$sFeedKey]))
					{
						$aLoopHistory[$sFeedKey] = 0;
					}
					
					$aLoopHistory[$sFeedKey]++;
	
					if ($aLoopHistory[$sFeedKey] >= ($iLoopMaxCount - 1))
					{				
						$bIsLoop = true;
						
						$this->_aViewMoreFeeds[$sFeedKey][] = $aRow;
					}
					else 
					{
						
						$aFeedLoop[$sFeedKey . $aLoopHistory[$sFeedKey]] = $aRow;	
						
						continue;
					}
				}			
				else 
				{			
					$aFeedLoop[$sFeedKey] = $aRow;	
				}
				
				if (isset($bIsLoop))
				{				
					unset($bIsLoop);
				}			
			}				
		}
		else 
		{
			$aFeedLoop = $aRows;	
		}
		
		$aFeeds = array();
		$aCacheData = array();
		$sLastFriendId = '';
		$sLastPhotoId = 0;
		foreach ($aFeedLoop as $sKey => $aRow)
		{											
			/*
			if ($aRow['type_id'] == 'friend')
			{				
				if ($aRow['item_id'] . $aRow['user_id'] == $sLastFriendId)
				{
					$sLastFriendId = '';

					continue;
				}				
				
				$sLastFriendId = $aRow['user_id'] . $aRow['item_id'];
			}
			else
			{
				$sLastFriendId = '';
			}
			*/
			
			if (($aReturn = $this->_processFeed($aRow, $sKey, $iUserid, $bFirstCheckOnComments)))
			{
				if (isset($aReturn['force_user']))
				{
					$aReturn['user_name'] = $aReturn['force_user']['user_name'];
					$aReturn['full_name'] = $aReturn['force_user']['full_name'];
					$aReturn['user_image'] = $aReturn['force_user']['user_image'];
					$aReturn['server_id'] = $aReturn['force_user']['server_id'];
				}
				
				$aFeeds[] = $aReturn;
			}					
		}		
		
		return $aFeeds;
	}	
			
	public function getLikeForFeed($iFeed)
	{
		$aLikeRows = $this->database()->select('fl.feed_id, ' . Phpfox::getUserField())
			->from(Phpfox::getT('feed_like'), 'fl')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fl.user_id')
			->where('fl.feed_id = ' . (int) $iFeed)
			->execute('getSlaveRows');
						
		$aLikes = array();
		$aLikesCount = array();
		foreach ($aLikeRows as $aLikeRow)
		{
			if (!isset($aLikesCount[$aLikeRow['feed_id']]))
			{
				$aLikesCount[$aLikeRow['feed_id']] = 0;
			}
						
			$aLikesCount[$aLikeRow['feed_id']]++;
						
			if ($aLikesCount[$aLikeRow['feed_id']] > 3)
			{
				continue;
			}
						
			$aLikes[$aLikeRow['feed_id']][] = $aLikeRow;	
		}
					
		return array($aLikesCount, $aLikes);
	}

	/**
	 * We get the redirect URL of the item depending on which module
	 * it belongs to. We use the callback to connect to the correct module.
	 *
	 * @param integer $iId Is the ID# of the feed
	 * @return boolean|string If we are unable to find the correct feed, If we find the correct feed
	 */
	public function getRedirect($iId)
	{
		// Get the feed
		$aFeed = $this->database()->select('feed_id, type_id, item_id, user_id')
			->from($this->_sTable)
			->where('feed_id =' . (int) $iId)
			->execute('getSlaveRow');
		
		
		// Make sure we found a feed
		if (!isset($aFeed['feed_id']))
		{
			return false;
		}
		$aProcessedFeed = $this->_processFeed($aFeed, false, $aFeed['user_id'], false);		
		Phpfox::getLib('url')->send($aProcessedFeed['feed_link'], array(), null, 302);
                /* Apparently in some CGI servers for some reason the redirect
                 * triggers a 500 error when the callback doesnt exist
                 * http://www.phpfox.com/tracker/view/6356/
                 */
                if (!Phpfox::hasCallback($aFeed['type_id'], 'getFeedRedirect'))
                {
                    return false;
                }
		// Run the callback so we get the correct link
		return Phpfox::callback($aFeed['type_id'] . '.getFeedRedirect', $aFeed['item_id'], $aFeed['child_item_id']);
	}
	
	public function getFeed($iId)
	{
		return $this->database()->select('*')
			->from(Phpfox::getT((isset($this->_aCallback['table_prefix']) ? $this->_aCallback['table_prefix'] : '') . 'feed'))
			->where('feed_id =' . (int) $iId)
			->execute('getSlaveRow');
	}
	
	public function shortenText($sText)
	{
		$oParseOutput = Phpfox::getLib('parse.output');
		
		return $oParseOutput->split($oParseOutput->shorten($oParseOutput->parse($sText), 300, 'feed.view_more', true), 40);	
	}
	
	public function shortenTitle($sText)
	{
		$oParseOutput = Phpfox::getLib('parse.output');
		
		return $oParseOutput->shorten($oParseOutput->clean($sText), 60, '...');
	}
	
	public function quote($sText)
	{
		Phpfox::getLib('parse.output')->setImageParser(array('width' => 200, 'height' => 200));

		$sNewText = '<div class="p_4">' . $this->shortenText($sText) . '</div>';
		
		Phpfox::getLib('parse.output')->setImageParser(array('clear' => true));
		
		return $sNewText;
	}
	
	public function getForBrowse($aConds, $sSort = 'feed.time_stamp DESC', $iRange = '', $sLimit = '')
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'feed')
			->where($aConds)
			->execute('getSlaveField');				
			
			$aRows = $this->database()->select('feed.*, fl.feed_id AS is_liked, ' . Phpfox::getUserField('u1', 'owner_') . ', ' . Phpfox::getUserField('u2', 'viewer_'))
				->from($this->_sTable, 'feed')
				->join(Phpfox::getT('user'), 'u1', 'u1.user_id = feed.user_id')
				->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = feed.item_user_id')
				->leftJoin(Phpfox::getT('feed_like'), 'fl', 'fl.feed_id = feed.feed_id AND fl.user_id = ' . Phpfox::getUserId())
				->where($aConds)
				->order($sSort)
				->limit($iRange, $sLimit, $iCnt)
				->execute('getSlaveRows');			
			
			$aFeeds = array();
			foreach ($aRows as $aRow)
			{
				$aRow['link'] = Phpfox::getLib('url')->makeUrl('feed.view', array('id' => $aRow['feed_id']));

				$aParts1 = explode('.', $aRow['type_id']);
				$sModule = $aParts1[0];
				if (strpos($sModule, '_'))
				{
					$aParts = explode('_', $sModule);
					$sModule = $aParts[0];
					if ($sModule == 'comment' && isset($aParts[1]) && !Phpfox::isModule($aParts[1]))
					{
					    continue;
					}
				}				
				
				if (!Phpfox::isModule($sModule))
				{
					continue;
				}
				
				if (($aFeed = Phpfox::callback($aRow['type_id'] . '.getNewsFeed', $aRow)))
				{
					if (isset($aLikes[$aFeed['feed_id']]))
					{
						$aFeed['like_rows'] = $aLikes[$aFeed['feed_id']];
					}
					
					if (isset($aLikesCount[$aFeed['feed_id']]))
					{
						$aFeed['like_count'] = ($aLikesCount[$aFeed['feed_id']] - count($aFeed['like_rows']));
					}					
					
					$aFeeds[] = $aFeed;
				}
			}
			
		return array($iCnt, $aFeeds);
	}
	
	public function processAjax($iId)
	{
		$oAjax = Phpfox::getLib('ajax');
		
		$aFeeds = Phpfox::getService('feed')->get(Phpfox::getUserId(), $iId);
		
		if (!isset($aFeeds[0]))
		{
			$oAjax->alert('This item has successfully been submitted. Before it can be displayed it will have to first be approved by a site Admin.');
			$oAjax->call('$Core.resetActivityFeedForm();');	
				
			return;
		}
		
		Phpfox::getLib('template')->assign(array('aFeed' => $aFeeds[0]))->getTemplate('feed.block.entry');				
				
		$sId = 'js_tmp_comment_' . md5('feed_' . uniqid() . Phpfox::getUserId()) . '';
		
		$oAjax->prepend('#js_new_feed_comment', '<div id="' . $sId . '" class="js_temp_new_feed_entry js_feed_view_more_entry_holder">' . $oAjax->getContent(false) . '</div>');	
		
		$oAjax->call('$(\'#' . $sId . '\').highlightFade();');
		
		$oAjax->removeClass('.js_user_feed', 'row_first');
		$oAjax->call("iCnt = 0; \$('.js_user_feed').each(function(){ iCnt++; if (iCnt == 1) { \$(this).addClass('row_first'); } });");
		$oAjax->call('$Core.resetActivityFeedForm();');	
		$oAjax->call('$Core.loadInit();');
	}
	
	public function getShareLinks()
	{
		$sCacheId = $this->cache()->set('feed_share_link');
		
		if (!($aLinks = $this->cache()->get($sCacheId)))
		{
			$aLinks = $this->database()->select('fs.*')
				->from(Phpfox::getT('feed_share'), 'fs')
				->join(Phpfox::getT('module'), 'm', 'm.module_id = fs.module_id AND m.is_active = 1')
				->order('fs.ordering ASC')
				->execute('getSlaveRows');
				
			foreach ($aLinks as $iKey => $aLink)
			{
				$aLinks[$iKey]['module_block'] = $aLink['module_id'] . '.' . $aLink['block_name'];
			}
				
			$this->cache()->save($sCacheId, $aLinks);
		}
		$aNoDuplicates = array();
		if (!is_array($aLinks) || empty($aLinks))
		{
			return $aLinks;
		}
		foreach ($aLinks as $iKey => $aLink)
		{
			unset($aLink['share_id']);
			if (in_array(serialize($aLink), $aNoDuplicates))
			{
				unset($aLinks[$iKey]);
				continue;
			}
			if (Phpfox::hasCallback($aLink['module_id'], 'checkFeedShareLink') && Phpfox::callback($aLink['module_id'] . '.checkFeedShareLink') === false)
			{
				unset($aLinks[$iKey]);
			}
			$aNoDuplicates[] = serialize($aLink);
		}
		
		return $aLinks;
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
		if ($sPlugin = Phpfox_Plugin::get('feed.service_feed__call'))
		{
			return eval($sPlugin);
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
		
	private function _parseFeed($sLink, $sStr, $sUserName)
	{
		$sLink = stripslashes($sLink);
		$sStr = stripslashes($sStr);
		$sUserName = stripslashes($sUserName);
		
		$bAddSpan = true;
		if (preg_match('/feed\/view/i', $sLink))
		{
			$bAddSpan = false;	
		}
		
		return ($bAddSpan ? '<span class="user_profile_link_span" id="js_user_name_link_' . $sUserName . '">' : '') . '<a href="' . $sLink . '">' . $sStr . '</a>' . ($bAddSpan ? '</span>' : '');
	}		
	
	private function _processFeed($aRow, $sKey, $iUserid, $bFirstCheckOnComments)
	{			
			if (!Phpfox::hasCallback($aRow['type_id'], 'getActivityFeed'))
			{				
				return false;
			}						

			$aFeed = Phpfox::callback($aRow['type_id'] . '.getActivityFeed', $aRow, (isset($this->_aCallback['module']) ? $this->_aCallback : null));			
						
			if ($aFeed === false)
			{
				return false;
			}			
			
			if (isset($this->_aViewMoreFeeds[$sKey]))
			{
				foreach ($this->_aViewMoreFeeds[$sKey] as $iSubKey => $aSubRow)
				{				
					$aFeed['more_feed_rows'][] = $this->_processFeed($aSubRow, $iSubKey, $iUserid, $bFirstCheckOnComments);
				}
			}
			
			if (Phpfox::isModule('like') && isset($aFeed['like_type_id']) && (int) $aFeed['feed_total_like'] > 0)
			{	
				$aFeed['likes'] = Phpfox::getService('like')->getLikesForFeed($aFeed['like_type_id'], (isset($aFeed['like_item_id']) ? $aFeed['like_item_id'] : $aRow['item_id']), ((int) $aFeed['feed_is_liked'] > 0 ? true : false), Phpfox::getParam('feed.total_likes_to_display'));
			}			
			
			if (isset($aFeed['comment_type_id']) && (int) $aFeed['total_comment'] > 0)
			{				
				$aFeed['comments'] = Phpfox::getService('comment')->getCommentsForFeed($aFeed['comment_type_id'], $aRow['item_id'], Phpfox::getParam('comment.total_comments_in_activity_feed'));
			}			
			
			if (isset($aRow['app_title']) && $aRow['app_id'])
			{
				$sLink = '<a href="' . Phpfox::permalink('apps', $aRow['app_id'], $aRow['app_title']) . '">' . $aRow['app_title'] . '</a>';
				$aFeed['app_link'] = $sLink;			
			}
			
			// Check if user can post comments on this feed/item
			$bCanPostComment = false;
			if ($bFirstCheckOnComments)
			{
				$bCanPostComment = true;	
			}			
			if ($iUserid !== null && $iUserid != Phpfox::getUserId())
			{				
				switch ($aRow['privacy_comment'])
				{
					case '1':
						if (!Phpfox::getService('user')->getUserObject($iUserid)->is_friend)
						{
							$bCanPostComment = false;
						}
						break;
					case '2':								
						if (!Phpfox::getService('user')->getUserObject($iUserid)->is_friend && !Phpfox::getService('user')->getUserObject($iUserid)->is_friend_of_friend)
						{
							$bCanPostComment = false;
						}
						break;			
					case '3':
						$bCanPostComment = false;
						break;
				}
			}
			
			if ($iUserid === null)
			{
				if ($aRow['user_id'] != Phpfox::getUserId())
				{
					switch ($aRow['privacy_comment'])
					{	
						case '1':
						case '2':
							if (!$aRow['is_friend'])
							{
								$bCanPostComment = false;
							}
							break;
						case '3':
							$bCanPostComment = false;
							break;
					}
				}
			}
			
			$aRow['can_post_comment'] = $bCanPostComment;
			
			return array_merge($aRow, $aFeed);		
	}
}

?>