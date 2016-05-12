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
 * @package  		Module_Video
 * @version 		$Id: callback.class.php 3358 2011-10-25 15:00:49Z Raymond_Benc $
 */
class Video_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('video');
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => 'Videos',
			'link' => Phpfox::getLib('url')->makeUrl('video'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_videos.png'))
		);
	}	

	public function enableSponsor($aParams)
	{
	    return Phpfox::getService('video.process')->sponsor((int)$aParams['item_id'], 1);
	}

	public function getProfileLink()
	{
		return 'profile.video';
	}	
	
	public function getAjaxCommentVar()
	{
		return 'video.can_add_comment_on_video';
	}	
	
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('video_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('video'))
			->where('video_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set('Unable to post a comment on this item due to privacy settings.');
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}	
	
	public function getTagCloud()
	{
		return array(
			'link' => 'video',
			'category' => 'video'
		);
	}	

	/**
	 * Returns information related to a video for sponsoring purposes
	 * @param int $iId video_id
	 * @return array in the format:
	 * array(
	 *	'title' => 'item title',		    <-- required
	 *	'link'  => 'makeUrl()'ed link',		    <-- required
	 *	'paypal_msg' => 'message for paypal'	    <-- required
	 *	'item_id' => int			    <-- required
	 *	'error' => 'phrase if item doesnt exit'	    <-- optional
	 *	'extra' => 'description'		    <-- optional
	 *	'image' => 'path to an image',		    <-- optional
	 *	'image_dir' => 'photo.url_photo|...	    <-- optional (required if image)
	 *	'server_id' => 'value from DB'		    <-- optional (required if image)
	 * );
	 */
	public function getToSponsorInfo($iId)
	{
	    $aVideo = $this->database()->select('v.user_id, v.title, v.video_id as item_id, vt.text_parsed as extra,
		       v.image_path as image, v.image_server_id as server_id')
		    ->from(Phpfox::getT('video'),'v')
		    ->leftjoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
		    ->where('v.video_id = ' . (int)$iId)
		    ->execute('getSlaveRow');
		
	    if (empty($aVideo))
	    {
			return array('error' => Phpfox::getPhrase('video.sponsor_error_not_found'));
	    }
	    
	    //$aVideo['link'] = Phpfox::getLib('url')->makeUrl('profile.video.'.$aVideo['title_url']);
		$aVideo['link'] = Phpfox::permalink('ad.sponsor', $aVideo['item_id'], $aVideo['title']);
	    $aVideo['paypal_msg'] = Phpfox::getPhrase('video.sponsor_paypal_message', array('sVideoTitle' => $aVideo['title']));//'Video Sponsor ' . $aVideo['title'];
	    $aVideo['title'] = Phpfox::getPhrase('video.sponsor_title', array('sVideoTitle' => $aVideo['title']));
	    $aVideo['image_dir'] = 'video.url_image';
	    $aVideo['image'] = sprintf($aVideo['image'],'_120');
		
	    return $aVideo;
	}

	public function getLink($aParams)
	{
	    $aVideo = $this->database()->select('u.user_name, v.title_url')
		    ->from(Phpfox::getT('video'),'v')
		    ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
		    ->where('v.video_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
	    if (empty($aVideo))
	    {
		return false;
	    }
	    return Phpfox::getLib('url')->makeUrl($aVideo['user_name'].'.video.' . $aVideo['title_url'] );
	}

	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		$aRow = $this->database()->select('m.video_id, m.title, u.full_name, u.gender, u.user_id, u.user_name')
			->from($this->_sTable, 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
			->where('m.video_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['video_id']))
		{
			return Phpfox_Error::trigger('Invalid callback on video.');
		}
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		$this->database()->updateCounter('video', 'total_comment', 'video_id', $aRow['video_id']);		
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('video', $aRow['video_id'], $aRow['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['video_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your video "' . $this->preParse()->clean($aRow['title'], 100) . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your video \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_video',
				'mass_id' => 'video',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' video.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aRow['full_name'] . '\'s video.'),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aRow['gender'], 1) . " video \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aRow['full_name'] . "'s video \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);			
	}	
	
	public function updateCommentText($aVals, $sText)
	{
		
	}	
	
	public function getItemName($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">' . Phpfox::getPhrase('video.on_name_s_video', array('name' => $sName)) . '</a>';
	}	
	
	public function deleteComment($iId)
	{
		$this->database()->updateCounter('video', 'total_comment', 'video_id', $iId, true);
	}	
	
	public function getCommentNewsFeed($aRow)
	{		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		
		
		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('video.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_video_a', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link']
				)
			);
		}
		else 
		{
			if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
			{
				$aRow['text'] = Phpfox::getPhrase('video.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_video_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link']				
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('video.a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_name_s_a_a_href_title_link_video_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link'],
						'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),
						'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name'])
					)
				);
			}
		}
			
		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		
		return $aRow;
	}	
	
	public function getFeedRedirect($iId, $iChild = 0)
	{
		$aRow = $this->database()->select('m.video_id, m.title')
			->from($this->_sTable, 'm')
			->where('m.video_id = ' . (int) $iId)
			->execute('getSlaveRow');;
			
		if (!isset($aRow['video_id']))
		{
			return false;
		}
		
		return Phpfox::permalink('video', $aRow['video_id'], $aRow['title']);
	}	
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function getNewsFeed($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('video.service_callback_getnewsfeed_start')){eval($sPlugin);}
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		
		
		$aRow['text'] = Phpfox::getPhrase('video.a_href_user_link_owner_full_name_a_added_a_new_video_a_href_title_link_title_a', array(
				'owner_full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
				'title_link' => $aRow['link']
			)
		);
		
		$aRow['icon'] = 'module/video.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}	
	
	public function groupMenu($sGroupUrl, $iGroupId)
	{
		if (!Phpfox::getService('group')->hasAccess($iGroupId, 'can_use_video'))
		{
			return false;
		}			
		
		return array(
				Phpfox::getPhrase('friend.videos') => array(
					'active' => 'video',
					'url' => Phpfox::getLib('url')->makeUrl('group', array($sGroupUrl, 'video')
				)
			)
		);
	}

	public function getWhatsNew()
	{
		return array(
			'video.videos' => array(
				'ajax' => '#video.getNew?id=js_new_item_holder',
				'id' => 'video',
				'block' => 'video.new'
			)
		);
	}

	public function getRatingData($iId)
	{
		return array(
			'field' => 'video_id',
			'table' => 'video',
			'table_rating' => 'video_rating'
		);
	}

	public function hideBlockProfile($sType)
	{
		return array(
			'table' => ($sType == 'profile' ? 'user_design_order' : '')
		);		
	}	
	
	public function getBlockDetailsProfile()
	{
		return array(
			'title' => Phpfox::getPhrase('video.videos')
		);
	}	

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aVideos = $this->database()
			->select('video_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		if (empty($aVideos))
		{
			return false;
		}
		foreach ($aVideos as $aVideo)
		{
			Phpfox::getService('video.process')->delete($aVideo['video_id']);
		}
	}
	
	public function getGroupPosting()
	{
		return array(
			Phpfox::getPhrase('video.upload_videos') => 'can_upload_video'
		);
	}	
	
	public function getGroupAccess()
	{
		return array(
			Phpfox::getPhrase('video.view_videos') => 'can_use_video'
		);
	}		
	
	public function getItemView()
	{
		if (Phpfox::getLib('request')->get('req3') != '')
		{
			return true;
		}
	}		
	
	public function addTrack($iId, $iUserId = null)
	{
		$this->database()->insert(Phpfox::getT('video_track'), array(
				'item_id' => (int) $iId,
				'user_id' => Phpfox::getUserBy('user_id'),
				'ip_address' => Phpfox::getIp(true),
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		$this->database()->updateCounter('video', 'total_view', 'video_id', $iId);
	}

	public function getNotificationFeedApproved($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('video.your_video_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
			'link' => Phpfox::getLib('url')->makeUrl('video', array('redirect' => $aRow['item_id'])),
			'path' => 'video.url_image',
			'suffix' => '_120'
		);		
	}
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'video.display_on_profile' => array(
				'phrase' => Phpfox::getPhrase('video.videos'),
				'default' => '0'				
			)
		);
	}

	public function pendingApproval()
	{
		return array(
			'phrase' => Phpfox::getPhrase('video.videos'),
			'value' => Phpfox::getService('video')->getPendingTotal(),
			'link' => Phpfox::getLib('url')->makeUrl('video', array('view' => 'pending'))
		);
	}
	
	public function legacyRedirect($aRequest)
	{
		if (isset($aRequest['listid']))
		{							
			$aItem = Phpfox::getService('core')->getLegacyUrl(array(
					'url_field' => 'name_url',
					'table' => 'video_category',
					'field' => 'upgrade_item_id',
					'id' => $aRequest['listid'],
					'user_id' => false
				)
			);					
						
			if ($aItem !== false)
			{
				return array('video', array('category', $aItem['name_url']));
			}														
		}
		
		if (isset($aRequest['id']))
		{							
			$aItem = Phpfox::getService('core')->getLegacyUrl(array(
					'url_field' => 'title_url',
					'table' => 'video',
					'field' => 'upgrade_item_id',
					'id' => $aRequest['id']					
				)
			);					
						
			if ($aItem !== false)
			{
				return array($aItem['user_name'], array('video', $aItem['title_url']));
			}														
		}		
		
		return 'video';
	}
	
	public function verifyFavorite($iItemId)
	{
		$aItem = $this->database()->select('i.video_id')
			->from(Phpfox::getT('video'), 'i')
			->where('i.video_id = ' . (int) $iItemId . ' AND i.view_id = 0')
			->execute('getSlaveRow');
			
		if (!isset($aItem['video_id']))
		{
			return false;
		}

		return true;
	}		
	
	public function getFavorite($aFavorites)
	{
		$oServiceVideoBrowse = Phpfox::getService('video.browse');		
		
		$oServiceVideoBrowse->condition('m.video_id IN(' . implode(',', $aFavorites) . ') AND m.in_process = 0 AND m.view_id = 0')
			->execute();	
			
		$aVideos = $oServiceVideoBrowse->get();
		
		foreach ($aVideos as $iKey => $aVideo)
		{
			$aVideos[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aVideo['image_server_id'],
					'path' => 'video.url_image',
					'file' => $aVideo['image_path'],
					'suffix' => '_120',
					'max_width' => 75,
					'max_height' => 75
				)
			);				
		}
		
		return array(
			'title' => Phpfox::getPhrase('video.videos'),
			'items' => $aVideos
		);		
	}
	
	public function getDashboardLinks()
	{
		$aLinks = array(
			'edit' => array(
				'phrase' => Phpfox::getPhrase('video.manage_videos'),
				'link' => 'profile.video',
				'image' => 'module/video_edit.png'
			)
		);
		$aUpload = array();
		if (Phpfox::getParam('video.show_share_and_upload_video_on_dashboard') != 'upload')
		{
			$aUpload[] = array(
				'phrase' => Phpfox::getPhrase('video.share_a_video'),
				'link' => 'video.share',
				'image' => 'module/video_add.png'
			);
		}
		
		if (Phpfox::getParam('video.allow_video_uploading') && (Phpfox::getParam('video.show_share_and_upload_video_on_dashboard') != 'share'))
		{
			$aUpload[] = array(
				'phrase' => Phpfox::getPhrase('video.upload_a_video'),
				'link' => 'video.upload',
				'image' => 'module/video_add.png'
			);
		}

		$aLinks['submit'] = $aUpload;		
		return $aLinks;
			
		
	}	
	
	public function reparserList()
	{
		return array(
			'name' => Phpfox::getPhrase('video.video_text'),
			'table' => 'video_text',
			'original' => 'text',
			'parsed' => 'text_parsed',
			'item_field' => 'video_id'
		);
	}

	public function getCommentNotificationFeed($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('video.full_name_wrote_a_comment_on_your_video', array(
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'full_name' => $aRow['full_name'],
					'link' => Phpfox::getLib('url')->makeUrl('video', array('redirect' => $aRow['item_id'])),
					'title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...')	
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('video', array('redirect' => $aRow['item_id'])),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);	
	}

	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('video.videos') => $aUser['activity_video']
		);
	}		

	public function globalSearch($sQuery, $bIsTagSearch = false)
	{
		$sCondition = 'p.in_process = 0 AND p.view_id = 0 AND p.item_id = 0 AND p.privacy = 0';
		if ($bIsTagSearch === false)
		{
			$sCondition .= ' AND (p.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR pi.text_parsed LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
		}
		
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.video_id AND tag.category_id = \'video\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
		}		
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('video_text'), 'pi', 'pi.video_id = p.video_id')
			->where($sCondition)
			->execute('getSlaveField');		
			
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.video_id AND tag.category_id = \'video\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('p.video_id');
		}			

		$aRows = $this->database()->select('p.title, p.title_url, p.time_stamp, p.image_path, p.image_server_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')			
			->join(Phpfox::getT('video_text'), 'pi', 'pi.video_id = p.video_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')						
			->where($sCondition)
			->limit(10)
			->order('p.time_stamp DESC')
			->execute('getSlaveRows');

		if (count($aRows))
		{
			$aResults = array();
			$aResults['total'] = $iCnt;
			$aResults['menu'] = Phpfox::getPhrase('video.videos');
			
			if ($bIsTagSearch == true)
			{
				$aResults['form'] = '<div><input type="button" value="' . Phpfox::getPhrase('video.view_more_videos') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('video', array('tag', $sQuery)) . '\';" /></div>';
			}
			else 
			{
				$aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('video') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[keyword]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="submit" value="' . Phpfox::getPhrase('video.view_more_videos') . '" class="search_button" /></div></form>';
			}			
			
			foreach ($aRows as $iKey => $aRow)
			{
				$aResults['results'][$iKey] = array(
					'title' => $aRow['title'],
					'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('video', $aRow['title_url'])),
					'image' => Phpfox::getLib('image.helper')->display(array(
							'server_id' => $aRow['image_server_id'],
							'title' => $aRow['title'],
							'path' => 'video.url_image',
							'file' => $aRow['image_path'],
							'suffix' => '_120',
							'max_width' => 75,
							'max_height' => 75
						)
					),
					'extra_info' => Phpfox::getPhrase('video.video_added_on_time_stamp_by_full_name', array(
							'link' => Phpfox::getLib('url')->makeUrl('video'),
							'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_stamp']),
							'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
							'full_name' => $this->preParse()->clean($aRow['full_name'])
						)
					)
				);
			}
			
			return $aResults;
		}
	}

	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('video.videos'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('video'))
				->where('view_id = 0 AND in_process = 0 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}
	
	public function updateCounterList()
	{
		$aList = array();		
		
		$aList[] =	array(
			'name' => Phpfox::getPhrase('video.update_video_view_count'),
			'id' => 'video-view-count'
		);	
		
		$aList[] =	array(
			'name' => Phpfox::getPhrase('video.update_tags_videos'),
			'id' => 'video-tag-update'
		);			

		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{		
		if ($iId == 'video-tag-update')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('tag'))
				->where('category_id = \'video\'')
				->execute('getSlaveField');			
				
			$aRows = $this->database()->select('m.tag_id, oc.video_id AS tag_item_id')
				->from(Phpfox::getT('tag'), 'm')
				->where('m.category_id = \'video\'')
				->leftJoin(Phpfox::getT('video'), 'oc', 'oc.video_id = m.item_id')
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');			
			
			foreach ($aRows as $aRow)
			{
				if (empty($aRow['tag_item_id']))
				{
					$this->database()->delete(Phpfox::getT('tag'), 'tag_id = ' . $aRow['tag_id']);
				}
			}
			
			return $iCnt;
		}
		
		if (!file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php'))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('comment.your_old_v1_6_21_setting_file_must_exist', array('file' => 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php')));
		}
		
		require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php');
		
		$sTable = (isset($_CONF['db']['prefix']) ? $_CONF['db']['prefix'] : '') . 'videos';
		
		if (!$this->database()->tableExists($sTable))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('comment.the_database_table_table_does_not_exist', array('table' => $sTable)));
		}
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('video'))
			->execute('getSlaveField');			
			
		$aRows = $this->database()->select('m.video_id, m.total_view, oc.vid_total')
			->from(Phpfox::getT('video'), 'm')
			->join($sTable, 'oc', 'oc.vid_id = m.upgrade_item_id')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');
			
		foreach ($aRows as $aRow)
		{
			$this->database()->update(Phpfox::getT('video'), array('total_view' => ($aRow['total_view'] + $aRow['vid_total'])), 'video_id = ' . (int) $aRow['video_id']);
		}
			
		return $iCnt;
	}

	public function deleteGroup($iId)
	{
		$aRows = $this->database()->select('*')
			->from($this->_sTable)
			->where('module_id = \'group\' AND item_id = ' . (int) $iId)
			->execute('getRows');
			
		foreach ($aRows as $aRow)
		{
			Phpfox::getService('video.process')->delete($aRow['video_id'], $aRow);
		}
		
		return true;
	}		
	
	public function getFeedRedirectFeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}
	
	public function getNewsFeedFeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('video.a_href_user_link_full_name_a_likes_their_own_a_href_link_video_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('video.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_video_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';

		return $aRow;				
	}		

	public function getNotificationFeedNotifyLike($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('video.a_href_user_link_full_name_a_likes_your_a_href_link_video_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('video', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('video', array('redirect' => $aRow['item_id']))			
		);				
	}	
	
	public function sendLikeEmail($iItemId)
	{
		return Phpfox::getPhrase('video.a_href_user_link_full_name_a_likes_your_a_href_link_video_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('video', array('redirect' => $iItemId))
				)
			);
	}			
	
	public function getActivityPointField()
	{
		return array(
			Phpfox::getPhrase('video.videos') => 'activity_video'
		);
	}

	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}	
	
	public function getSqlTitleField()
	{
		return array(
			array(
				'table' => 'video',
				'field' => 'title'
			),
			array(
				'table' => 'video_category',
				'field' => 'name'
			)
		);
	}	

	public function tabHasItems($iUser)
	{
		$iCount = $this->database()->select('COUNT(user_id)')
				->from($this->_sTable)
				->where('user_id = ' . (int)$iUser)
				->execute('getSlaveField');
		return $iCount > 0;
	}

	public function getActivityFeed($aItem, $aCallback = null)
	{				
		if ($aCallback === null)
		{
			$this->database()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = v.parent_user_id');
		}
		
		$aRow = $this->database()->select('v.video_id, v.module_id, v.title, v.time_stamp, v.total_comment, v.total_like, v.image_path, v.image_server_id, l.like_id AS is_liked, vt.text_parsed')
			->from(Phpfox::getT('video'), 'v')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'video\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId())
			->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
			->where('v.video_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');		
		
		if (!isset($aRow['video_id']))
		{
			return false;
		}
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'video.view_browse_videos'))
		{
			return false;
		}			
		
		$aReturn = array(
			'feed_title' => $aRow['title'],
			// 'feed_info' => 'shared a video.',
			'feed_link' => Phpfox::permalink('video', $aRow['video_id'], $aRow['title']),
			'feed_content' => $aRow['text_parsed'],
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/video.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'video',
			'like_type_id' => 'video'
		);
		
		if ($aRow['module_id'] == 'pages')
		{
			$aRow['parent_user_id'] = '';
			$aRow['parent_user_name'] = '';
		}		
		
		if (empty($aRow['parent_user_id']))
		{
			$aReturn['feed_info'] = Phpfox::getPhrase('feed.shared_a_video');
		}	
		
		if ($aCallback === null)
		{			
			if (!empty($aRow['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST))
			{
				$aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
			}
			
			if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId())
			{
				$aReturn['feed_mini'] = true;
				$aReturn['feed_mini_content'] = Phpfox::getPhrase('feed.full_name_posted_a_href_link_a_video_a_on_a_href_profile_parent_full_name_a_s_a_href_profile_link_wall_a', array('full_name' => Phpfox::getService('user')->getFirstName($aItem['full_name']), 'link' => Phpfox::permalink('video', $aRow['video_id'], $aRow['title']), 'profile' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']), 'parent_full_name' => $aRow['parent_full_name'], 'profile_link' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name'])));
				$aReturn['feed_title'] = '';
				unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_content']);
			}		
		}		
		
		if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId())
		{
			
		}
		else
		{
			if (!empty($aRow['image_path']))
			{
				$sImage = Phpfox::getLib('image.helper')->display(array(
						'server_id' => $aRow['image_server_id'],
						'path' => 'video.url_image',
						'file' => $aRow['image_path'],
						'suffix' => '_120',
						'max_width' => 120,
						'max_height' => 120					
					)
				);

				$aReturn['feed_image'] = $sImage;
			}

			$aReturn['feed_image_onclick'] = '$Core.box(\'video.play\', 700, \'id=' . $aRow['video_id'] . '&amp;feed_id=' . $aItem['feed_id'] . '&amp;popup=true\', \'GET\'); return false;';
		}
		
		return $aReturn;
	}	
	
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('video_id, title, user_id')
			->from(Phpfox::getT('video'))
			->where('video_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
			
		if (!isset($aRow['video_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'video\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'video', 'video_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('video', $aRow['video_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('video.full_name_liked_your_video_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(Phpfox::getPhrase('video.full_name_liked_your_video_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			Phpfox::getService('notification.process')->add('video_like', $aRow['video_id'], $aRow['user_id']);				
		}		
	}
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'video\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'video', 'video_id = ' . (int) $iItemId);
	}	
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('video'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.video_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['video_id']))
		{
			return false;
		}			
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('video', $aRow['video_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('l.video_id, l.title, u.user_id, u.gender, u.user_name, u.full_name')	
			->from(Phpfox::getT('video'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.video_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('video', $aRow['video_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}	
	
	public function getNotificationApproved($aNotification)
	{
		$aRow = $this->database()->select('v.video_id, v.title, v.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('video'), 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.video_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['video_id']))
		{
			return false;
		}
		
		$sPhrase = 'Your video "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '" has been approved.';
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('video', $aRow['video_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
			'no_profile_image' => true
		);			
	}
	
	public function getAjaxProfileController()
	{
		return 'video.index';
	}
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_video']))
			{
				return false;
			}

			if (isset($aUser['total_video']) && (int) $aUser['total_video'] === 0)
			{
				return false;
			}
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('profile.videos'),
			'url' => 'profile.video',
			'total' => (int) (isset($aUser['total_video']) ? $aUser['total_video'] : 0),
			'icon' => 'feed/video.png'
		);		
		
		return $aMenus;
	}
	
	public function getTotalItemCount($iUserId)
	{
		return array(
			'field' => 'total_video',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('video'))->where('in_process = 0 AND view_id = 0 AND item_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
		);	
	}	
	
	public function checkFeedShareLink()
	{
		if (Phpfox::isMobile())
		{
			return false;
		}		
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'video.share_videos'))
		{
			return false;
		}		
		
		if (!Phpfox::getParam('video.allow_video_uploading') || !Phpfox::getUserParam('video.can_upload_videos'))
		{
			return false;
		}
	}
	
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.video_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'video\' AS item_type_id, item.image_path AS item_photo, item.image_server_id AS item_photo_server')
			->from(Phpfox::getT('video'), 'item')
			->where('item.in_process = 0 AND item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch))
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('video', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = 'Video';
		
		$aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => $aRow['item_photo'],
				'path' => 'video.url_image',
				'suffix' => '_120',
				'max_width' => '120',
				'max_height' => '120'
				// 'return_url' => true
			)
		);		
		
		return $aInfo;
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => 'Videos'
		);
	}
	
	public function getPageSubMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'video.share_videos'))
		{
			return null;
		}		
		
		return array(
			array(
				'phrase' => 'Upload/Share a Video',
				'url' => Phpfox::getLib('url')->makeUrl('video.add', array('module' => 'pages', 'item' => $aPage['page_id']))
			)
		);
	}	
	
	public function getPageMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'video.view_browse_videos'))
		{
			return null;
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('video.videos'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'video/',
			'icon' => 'feed/video.png',
			'landing' => 'video'
		);
		
		return $aMenus;
	}	
	
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['video.share_videos'] = 'Who can share videos?';
		$aPerms['video.view_browse_videos'] = 'Who can view/browse videos?';
		
		return $aPerms;
	}
	
	public function canViewPageSection($iPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($iPage, 'video.view_browse_videos'))
		{
			return false;
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
		if ($sPlugin = Phpfox_Plugin::get('video.service_callback__call'))
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