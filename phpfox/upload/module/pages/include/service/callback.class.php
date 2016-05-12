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
 * @version 		$Id: callback.class.php 3332 2011-10-20 12:50:29Z Raymond_Benc $
 */
class Pages_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => Phpfox::getPhrase('pages.pages'),
			'link' => Phpfox::getLib('url')->makeUrl('pages'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_custom-fields.png'))
		);
	}	
	
	public function addPhoto($iId)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		return array(
			'module' => 'pages',
			'item_id' => $iId,
			'table_prefix' => 'pages_'
		);
	}
	
	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('pages.pages') => $aUser['activity_pages']
		);
	}
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
			->from(Phpfox::getT('pages_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}
		
		if ($aNotification['user_id'] == $aRow['user_id'] && isset($aNotification['extra_users']) && count($aNotification['extra_users']))
		{
			$sUsers = Phpfox::getService('notification')->getUsers($aNotification, true);
		}
		else
		{
			$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		}
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');		
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = Phpfox::getPhrase('pages.users_commented_on_full_name_comment', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = Phpfox::getPhrase('pages.users_commented_on_gender_own_comment', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));	
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getPhrase('pages.users_commented_on_one_of_your_comments', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = Phpfox::getPhrase('pages.users_commented_on_one_of_full_name_comments', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'link' => $sLink . 'comment-id_' . $aRow['feed_comment_id'],
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function getPhotoDetails($aPhoto)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('pages')->getPage($aPhoto['group_id']);
			
		if (!isset($aRow['page_id']))
		{
			return false;
		}
		
		Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'breadcrumb_title' => Phpfox::getPhrase('pages.pages'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
			'module_id' => 'pages',
			'item_id' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'photo/',
			'theater_mode' => Phpfox::getPhrase('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
		);
	}
	
	public function uploadVideo($aVals)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		return array(
			'module' => 'pages',
			'item_id' => $aVals['callback_item_id']
		);
	}	
	
	public function convertVideo($aVideo)
	{
		return array(
			'module' => 'pages',
			'item_id' => $aVideo['item_id'],
			'table_prefix' => 'pages_'
		);			
	}	
	
	public function addLink($aVals)
	{
		return array(
			'module' => 'pages',
			'item_id' => $aVals['callback_item_id'],
			'table_prefix' => 'pages_'
		);		
	}	
	
	public function getFeedDisplay($iEvent)
	{
		return array(
			'module' => 'pages',
			'table_prefix' => 'pages_',
			'ajax_request' => 'event.addFeedComment',
			'item_id' => $iEvent
		);
	}	
	
	public function getActivityFeedComment($aItem)
	{
		$aRow = $this->database()->select('fc.*, l.like_id AS is_liked, e.page_id, e.title, pu.vanity_url')
			->from(Phpfox::getT('pages_feed_comment'), 'fc')
			->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'pages_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId())
			->where('fc.feed_comment_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');		

		if (!isset($aRow['page_id']))
		{
			return false;
		}		
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'pages.view_browse_updates'))
		{
			return false;
		}		
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
		
		$aReturn = array(
			'no_share' => true,
			'feed_status' => $aRow['content'],
			'feed_link' => $sLink,
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'pages',
			'like_type_id' => 'pages_comment'			
		);
				
		return $aReturn;		
	}	
	
	public function getActivityFeedItemLiked($aItem)
	{
		$aRow = $this->database()->select('p.page_id, p.title, p.total_like, pu.vanity_url, l.like_id AS is_liked, p.image_path, p.image_server_id')
			->from(Phpfox::getT('pages'), 'p')
			->where('p.page_id = ' . (int) $aItem['item_id'])
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'pages\' AND l.item_id = p.page_id AND l.user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRow');

		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
		
		$aReturn = array(
			'feed_title' => '',
			'feed_info' => Phpfox::getPhrase('pages.liked_the_page_link_title_title', array('link' => $sLink, 'link_title' => Phpfox::getLib('parse.output')->clean($aRow['title']), 'title' => Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.output')->shorten($aRow['title'], 50, '...')))),
			'feed_link' => $sLink,
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
			'time_stamp' => $aItem['time_stamp'],			
			'enable_like' => true,
			'like_type_id' => 'pages'
		);		
		
		if (!empty($aRow['image_path']))
		{
			$sImage = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aRow['image_server_id'],
					'path' => 'pages.url_image',
					'file' => $aRow['image_path'],
					'suffix' => '_120',
					'max_width' => 120,
					'max_height' => 120					
				)
			);
			
			$aReturn['feed_image'] = $sImage;
		}		
		
		return $aReturn;
	}	
	
	public function addEvent($iItem)
	{		
		Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('pages')->getPage($iItem);
		
		if (!isset($aRow['page_id']))
		{
			return false;
		}
		
		return $aRow;
	}
	
	public function viewEvent($iItem)
	{		
		$aRow = $this->addEvent($iItem);		
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'breadcrumb_title' => Phpfox::getPhrase('pages.pages'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
			'module_id' => 'pages',
			'item_id' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_pages' => $sLink . 'event/'
		);		
	}
	
	public function getFeedDetails($iItemId)
	{
		return array(
			'module' => 'pages',
			'table_prefix' => 'pages_',
			'item_id' => $iItemId
		);		
	}	
	
	public function getNotificationInvite($aNotification)
	{
		$aRow = Phpfox::getService('pages')->getPage($aNotification['item_id']);
			
		if (!isset($aRow['page_id']))
		{
			return false;
		}			
			
		$sPhrase = Phpfox::getPhrase('pages.users_invited_you_to_check_out_the_page_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification), 'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
			
		return array(
			'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function deleteLike($iItemId)
	{
		$aRow = Phpfox::getService('pages')->getPage($iItemId);

		if (!isset($aRow['page_id']))
		{
			return false;
		}		
		
		$this->database()->updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'pages', 'page_id = ' . (int) $iItemId);	
		
		$iFriendId = (int) $this->database()->select('user_id')
			->from(Phpfox::getT('user'))
			->where('profile_page_id = ' . (int) $aRow['page_id'])
			->execute('getSlaveField');		
		
		$this->database()->delete(Phpfox::getT('friend'), 'user_id = ' . (int) $iFriendId . ' AND friend_user_id = ' . Phpfox::getUserId());
		$this->database()->delete(Phpfox::getT('friend'), 'friend_user_id = ' . (int) $iFriendId . ' AND user_id = ' . Phpfox::getUserId());
		
		// $this->_loadLikeBlock($iItemId);
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
		
		Phpfox::getLib('ajax')->call('window.location.href = \'' . $sLink. '\';');
	}	
	
	public function addLike($iItemId, $bDoNotSendEmail = false, $iUserId = null)
	{
		$aRow = Phpfox::getService('pages')->getPage($iItemId);

		if (!isset($aRow['page_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'pages', 'page_id = ' . (int) $iItemId);
		
		// if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
				
			if ($iUserId === null)
			{
				if (!$aRow['page_type'])
				{
					Phpfox::getLib('mail')->to($aRow['user_id'])
						->subject(Phpfox::getPhrase('pages.full_name_liked_your_page_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
						->message(Phpfox::getPhrase('pages.full_name_liked_your_page', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
						->send();				

					Phpfox::getService('notification.process')->add('pages_like', $aRow['page_id'], $aRow['user_id']);

					(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('pages_itemLiked', $aRow['page_id']) : null);
				}
			}
			else
			{
				Phpfox::getLib('mail')->to($iUserId)
					->subject(Phpfox::getPhrase('pages.membership_accepted_to_title', array('title' => $aRow['title'])))
					->message(Phpfox::getPhrase('pages.your_membership_to_the_page_link', array('link' => $sLink, 'title' => $aRow['title'])))
					->send();				
				
				Phpfox::getService('notification.process')->add('pages_joined', $aRow['page_id'], $iUserId);
			}
		}		
		
		$iFriendId = (int) $this->database()->select('user_id')
			->from(Phpfox::getT('user'))
			->where('profile_page_id = ' . (int) $aRow['page_id'])
			->execute('getSlaveField');
		
		$bIsApprove = true;
		if ($iUserId === null)
		{
			$iUserId = Phpfox::getUserId();
			$bIsApprove = false;
		}
		
		$this->database()->insert(Phpfox::getT('friend'), array(
				'is_page' => 1,
				'list_id' => 0,
				'user_id' => $iUserId,
				'friend_user_id' => $iFriendId,
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		$this->database()->insert(Phpfox::getT('friend'), array(
				'is_page' => 1,
				'list_id' => 0,
				'user_id' => $iFriendId,
				'friend_user_id' => $iUserId,
				'time_stamp' => PHPFOX_TIME
			)
		);		
		
		if (!$bIsApprove)
		{
			// $this->_loadLikeBlock($iItemId);
			Phpfox::getLib('ajax')->call('window.location.href = \'' . $sLink. '\';');
			
			// $this->database()->delete(Phpfox::getT('pages_invite'), 'page_id = ' . (int) $aRow['page_id'] . ' AND invited_user_id = ' . Phpfox::getUserId());
		}
	}	
	
	public function getVideoDetails($aItem)
	{		
		Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);
			
		if (!isset($aRow['page_id']))
		{
			return false;
		}
		
		Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'breadcrumb_title' => Phpfox::getPhrase('pages.pages'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
			'module_id' => 'pages',
			'item_id' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'video/',
			'theater_mode' => Phpfox::getPhrase('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
		);
	}
	
	public function getMusicDetails($aItem)
	{		
		Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);
			
		if (!isset($aRow['page_id']))
		{
			return false;
		}
		
		Phpfox::getService('pages')->setMode();
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'breadcrumb_title' => Phpfox::getPhrase('pages.pages'),
			'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
			'module_id' => 'pages',
			'item_id' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'music/',
			'theater_mode' => Phpfox::getPhrase('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
		);
	}	
	
	public function uploadSong($iItemId)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		return array(
			'module' => 'pages',
			'item_id' => $iItemId,
			'table_prefix' => 'pages_'
		);			
	}		
	
	public function getNotificationJoined($aNotification)
	{
		$aRow = Phpfox::getService('pages')->getPage($aNotification['item_id']);
		
		if (!isset($aRow['page_id']))
		{
			return false;
		}	
		
		return array(
			'no_profile_image' => true,
			'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
			'message' => Phpfox::getPhrase('pages.your_membership_has_been_accepted_to_join_the_page_title', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...'))),
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);		
	}
	
	public function getNotificationLike($aNotification)
	{
		$aRow = Phpfox::getService('pages')->getPage($aNotification['item_id']);
		
		if (!isset($aRow['page_id']))
		{
			return false;
		}	
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		if (!isset($aRow['gender']))
		{
			$sGender = 'their';
		}
		else
		{
			$sGender = Phpfox::getService('user')->gender($aRow['gender'], 1);
		}
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
			
		$sPhrase = '';
		if ($aRow['type_id'] == '1')
		{
			if ($aNotification['user_id'] == $aRow['user_id'])
			{
				$sPhrase = Phpfox::getPhrase('pages.users_joined_gender_own_page_title', array('users' => $sUsers, 'gender' => $sGender, 'title' => $sTitle));
			}
			elseif ($aRow['user_id'] == Phpfox::getUserId())		
			{
				$sPhrase = Phpfox::getPhrase('pages.users_joined_your_page_title', array('users' => $sUsers, 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = Phpfox::getPhrase('pages.users_joined_full_names_page_title', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
			}			
		}
		else
		{		
			if ($aNotification['user_id'] == $aRow['user_id'])
			{
				$sPhrase = Phpfox::getPhrase('pages.users_liked_gender_own_page_title', array('users' => $sUsers, 'gender' => $sGender, 'title' => $sTitle));	
			}
			elseif ($aRow['user_id'] == Phpfox::getUserId())		
			{
				$sPhrase = Phpfox::getPhrase('pages.users_liked_your_page_title', array('users' => $sUsers, 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = Phpfox::getPhrase('pages.users_liked_full_names_page_title', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
		}
			
		return array(
			'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function addForum($iId)
	{
		Phpfox::getService('pages')->setIsInPage();
		
		$aRow = Phpfox::getService('pages')->getPage($iId);
			
		if (!isset($aRow['page_id']))
		{
			return false;
		}			
		
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
			
		return array(
			'module' => 'pages',
			'item' => $aRow['page_id'],
			'group_id' => $aRow['page_id'],
			'url_home' => $sLink,
			'title' => $aRow['title'],
			'table_prefix' => 'pages_',
			'item_id' => $aRow['page_id']			
		);
	}
	
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['pages.share_updates'] = Phpfox::getPhrase('pages.who_can_post_a_comment');
		$aPerms['pages.view_browse_updates'] = Phpfox::getPhrase('pages.who_can_view_browse_comments');
		
		return $aPerms;
	}
	
	public function checkFeedShareLink()
	{
		return false;
	}	
	
	public function getAjaxCommentVar()
	{
		return null;
	}
	
	public function getCommentItem($iId)
	{		
		$aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('pages_feed_comment'))
			->where('feed_comment_id = ' . (int) $iId)
			->execute('getSlaveRow');		
		
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(Phpfox::getPhrase('pages.unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}		
			
		return $aRow;
	}	
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{		
		$aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.page_id, e.title, u.full_name, u.gender, pu.vanity_url')
			->from(Phpfox::getT('pages_feed_comment'), 'fc')
			->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
			->where('fc.feed_comment_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		$this->database()->updateCounter('pages_feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);		
		
		// Send the user an email
		$sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']) . 'comment-id_' . $aRow['feed_comment_id'] . '/';
		$sItemLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['feed_comment_id'],
				'owner_subject' => Phpfox::getPhrase('pages.full_name_commented_on_a_comment_posted_on_the_page_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])),
				'owner_message' => Phpfox::getPhrase('pages.full_name_commented_on_one_of_your_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'item_link' => $sItemLink, 'title' => $aRow['title'], 'link' => $sLink)),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'pages_comment_feed',
				'mass_id' => 'pages',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getPhrase('pages.full_name_commented_on_one_of_gender_page_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : Phpfox::getPhrase('pages.full_name_commented_on_one_of_other_full_name_s_page_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'other_full_name' => $aRow['full_name']))),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getPhrase('pages.full_name_comment_on_one_of_gender', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'item_link' => $sItemLink, 'title' => $aRow['title'], 'link' => $sLink)) : Phpfox::getPhrase('pages.full_name_commented_on_one_of_other_full_name', array('full_name' => Phpfox::getUserBy('full_name'), 'other_full_name' => $aRow['full_name'], 'item_link' => $sItemLink, 'title' => $aRow['title'], 'link' => $sLink)))
			)
		);
	}	
	
	public function getNotificationComment($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
			->from(Phpfox::getT('pages_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}
		
		if ($aNotification['item_user_id'] == $aRow['user_id'] && isset($aNotification['extra_users']) && count($aNotification['extra_users']))
		{
			$sUsers = Phpfox::getService('notification')->getUsers($aNotification, true);
		}
		else
		{
			$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		}
		// $sGender = Phpfox::getService('user')->gender($aRow['gender'], 1);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = Phpfox::getPhrase('pages.users_commented_on_the_page_title', array('users' => $sUsers, 'title' => $sTitle));			
		
		return array(
			'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']) . 'comment-id_' . $aRow['feed_comment_id'] . '/',
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}
	
	public function getNotificationComment_Feed($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
			->from(Phpfox::getT('pages_feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}
		
		if ($aNotification['user_id'] == $aRow['user_id'] && isset($aNotification['extra_users']) && count($aNotification['extra_users']))
		{
			$sUsers = Phpfox::getService('notification')->getUsers($aNotification, true);
		}
		else
		{
			$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		}		
		$sGender = Phpfox::getService('user')->gender($aRow['gender'], 1);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');		
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($aNotification['extra_users']) && count($aNotification['extra_users']))
			{
				$sPhrase = Phpfox::getPhrase('pages.users_commented_on_span_class_drop_data_user_full_name_s_span_comment_on_the_page_title', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
			}
			else 
			{
				$sPhrase = Phpfox::getPhrase('pages.users_commented_on_gender_own_comment_on_the_page_title', array('users' => $sUsers, 'gender' => $sGender, 'title' => $sTitle));	
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getPhrase('pages.users_commented_on_one_of_your_comments_on_the_page_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = Phpfox::getPhrase('pages.users_commented_on_one_of_full_name', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']) . 'comment-id_' . $aRow['feed_comment_id'] . '/',
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);		
	}
	
	public function getTotalItemCount($iUserId)
	{
		return array(
			'field' => 'total_pages',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('pages'))->where('view_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')		
		);	
	}	
	
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.page_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'pages\' AS item_type_id, item.image_path AS item_photo, item.image_server_id 	 AS item_photo_server')
			->from(Phpfox::getT('pages'), 'item')
			->where('item.view_id = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch) . ' AND item.privacy = 0')
			->union();
	}	
	
	public function getSearchInfo($aRow)
	{
		$aPage = $this->database()->select('p.page_id, p.title, pu.vanity_url, ' . Phpfox::getUserField())
			->from(Phpfox::getT('pages'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
			->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
			->where('p.page_id = ' . (int) $aRow['item_id'])
			->execute('getSlaveRow');
		
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
		$aInfo['item_name'] = Phpfox::getPhrase('pages.page');
		$aInfo['profile_image'] = $aPage;
		
		return $aInfo;
	}	
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => Phpfox::getPhrase('pages.pages')
		);
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
		if ($sPlugin = Phpfox_Plugin::get('pages.service_callback__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
	
	private function _loadLikeBlock($iPage)
	{
		$aPage = Phpfox::getService('pages')->getForView($iPage);
		
		$oAjax = Phpfox::getLib('ajax');		
		
		Phpfox::getLib('template')->assign('aPage', $aPage);
		Phpfox_Component::setPublicParam('aPage', $aPage);
		
		Phpfox::getBlock('pages.like');
		
		$oAjax->html('#js_pages_like_join_holder', $oAjax->getContent(false));
	}
}

?>