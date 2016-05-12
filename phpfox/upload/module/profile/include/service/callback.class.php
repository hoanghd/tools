<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Callbacks
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Profile
 * @version 		$Id: callback.class.php 3346 2011-10-24 15:20:05Z Raymond_Benc $
 */
class Profile_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('user');
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => 'Profile',
			'link' => Phpfox::getLib('url')->makeUrl('profile'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_members-profiles.gif'))
		);
	}	
	
	public function addTrack($iId, $iUserId = null)
	{
		$this->database()->insert(Phpfox::getT('user_track'), array(
				'item_id' => (int) $iId,
				'user_id' => Phpfox::getUserBy('user_id'),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}
	
	public function getLatestTrackUsers($iId, $iUserId)
	{
		$aRows = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('user_track'), 'track')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
			->where('track.item_id = ' . (int) $iId . ' AND track.user_id != ' . (int) $iUserId)
			->order('track.time_stamp DESC')
			->limit(0, 7)
			->execute('getSlaveRows');

		return (count($aRows) ? $aRows : false);		
	}
	
	public function getCommentNewsFeed($aRow)
	{		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');			

		if ($aRow['owner_user_id'] == $aRow['item_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('profile.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_profile_a', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link']
				)
			);
		}
		elseif ($aRow['item_id'] == Phpfox::getUserId())
		{
			$aRow['text'] = Phpfox::getPhrase('profile.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_profile_a', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link']			
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('profile.a_href_user_link_full_name_a_added_a_new_comment_on_a_href_title_link_item_user_name_s_a_profile', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link'],
					'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name'])
				)
			);
		}		

		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
				
		return $aRow;		
	}	
	
	public function getAjaxCommentVar()
	{
		return 'profile.can_post_comment_on_profile';
	}
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		Phpfox::getService('user.field.process')->updateCommentCounter($aVals['item_id']);
		
		$aUser = $this->database()->select('user_id, user_name')
			->from(Phpfox::getT('user'))
			->where('user_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');	
			
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_profile', $aVals['item_id'], $aVals['text_parsed'], $iUserId, $aUser['user_id'], $aVals['comment_id']) : null);		
		
		$sLink = Phpfox::getLib('url')->makeUrl($aUser['user_name']);
		Phpfox::getLib('mail')
			->to($aUser['user_id'])
			->subject(array('profile.user_name_left_you_a_comment_on_site_title', array('user_name' => $sUserName, 'site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('profile.user_name_left_you_a_comment_on_your_profile_message', array(
						'user_name' => $sUserName,
						'link' => $sLink
					)
				)
			)
			->notification('comment.add_new_comment')
			->send();
			
		$aActualUser = Phpfox::getService('user')->getUser($iUserId);			
		Phpfox::getService('notification.process')->add('comment_profile', $aUser['user_id'], $aUser['user_id'], array(
				'title' => '',
				'user_id' => $aActualUser['user_id'],
				'image' => $aActualUser['user_image'],
				'server_id' =>  $aActualUser['server_id']				
			)
		);			
	}
	
	public function updateCommentText($aVals, $sText)
	{
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_profile', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
	}		
	
	public function getCommentNotificationFeed($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('profile.a_href_user_link_full_name_a_wrote_a_comment_on_your_a_href_profile_link_profile_a', array(
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'full_name' => $this->preParse()->clean($aRow['full_name']),
					'profile_link' => Phpfox::getLib('url')->makeUrl('profile')
				)				
			),
			'link' => Phpfox::getLib('url')->makeUrl('profile'),
			'path' => 'core.url_user',
			'suffix' => '_50'
		);	
	}	
	
	public function getFeedRedirect($iId, $iChild = 0)
	{
		$aUser = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('user'), 'u')
			->where('u.user_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if ($iChild > 0)
		{
			return Phpfox::getLib('url')->makeUrl($aUser['user_name'], array('comment' => $iChild, '#comment-view'));
		}			
		return Phpfox::getLib('url')->makeUrl($aUser['user_name']);
	}	
	
	public function getCommentItem($iId)
	{		
		$aUser = $this->database()->select('user_id AS comment_item_id, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('user_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		$aUser['comment_view_id'] = '0';
		
		return $aUser;
	}	
	
	public function getProfileSettings()
	{
		return array(
			'profile.view_profile' => array(
				'phrase' => Phpfox::getPhrase('user.view_your_profile_lowercase')				
			),
			'profile.basic_info' => array(
				'phrase' => Phpfox::getPhrase('user.view_your_basic_information')				
			),
			'profile.profile_info' => array(
				'phrase' => Phpfox::getPhrase('user.view_your_profile_information')				
			),
			'profile.view_location' => array(
				'phrase' => Phpfox::getPhrase('user.view_your_location')				
			)
		);		
	}
	
	public function deleteComment($iId)
	{
		Phpfox::getService('user.field.process')->updateCommentCounter($iId, true);
	}		
	
	public function getBlocksIndex()
	{
		return array(
			'table' => 'user_design_order',
			'field' => 'user_id'
		);
	}
	
	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}	
	
	public function getRssTitle($iId)
	{
		$aUser = Phpfox::getService('user')->getUser($iId, 'u.full_name');
		
		return Phpfox::getPhrase('profile.comments_on') . ': ' . Phpfox::getLib('parse.output')->clean($aUser['full_name']);
	}
	
	public function getDetailOnCssUpdate()
	{		
		return array(
			'table' => 'user_css',
			'field' => 'user_id',
			'value' => Phpfox::getUserId(),
			'table_hash' => 'user_field',
			'table_hash_field' => 'css_hash',
			'table_code' => 'user_css_code'			
		);
	}
	
	public function getNewsFeedInfo($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('profile.service_callback_getnewsfeedinfo_start')){eval($sPlugin);}
		$aRow['text'] = Phpfox::getPhrase((empty($aRow['owner_gender']) ? 'profile.full_name_s_profile_has_been_updated' : 'profile.a_href_user_link_full_name_a_updated_their_profile'), array(
				'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
				'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1)
			)
		);		
		
		$aRow['icon'] = 'misc/application_edit.png';
		
		return $aRow;
	}
	
	public function getNewsFeedDesign($aRow)
	{
		$aRow['text'] = Phpfox::getPhrase((empty($aRow['owner_gender']) ? 'profile.full_name_s_profile_design_has_been_updated' : 'profile.a_href_user_link_full_name_a_updated_their_profile_design'), array(
				'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
				'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1)
			)
		);
		
		$aRow['icon'] = 'misc/color_swatch.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}

	public function getDetailOnThemeUpdate()
	{
		return array(
			'table' => 'user_field',
			'field' => 'designer_style_id',
			'action' => 'user_id',
			'value' => Phpfox::getUserId(),
			'javascript' => '$(\'.style_submit_box_theme\').hide(); $(\'.style_box\').removeClass(\'style_box_active\'); $(\'.style_box\').each(function(){ if($(this).hasClass(\'style_box_test\')) $(this).removeClass(\'style_box_test\').addClass(\'style_box_active\');  {} });'
		);	
	}
	
	public function getDetailOnBlockUpdate()
	{
		return array(
			'table' => 'user_design_order',
			'field' => 'user_id',
			'value' => Phpfox::getUserId()
		);
	}
	
	public function getDetailOnOrderUpdate()
	{
		return array(
			'table' => 'user_design_order',
			'field' => 'user_id',
			'value' => Phpfox::getUserId()
		);		
	}
	
	public function getItemName($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">' . Phpfox::getPhrase('profile.on_name_s_profile', array('name' => $sName)) . '</a>';
	}
	
	public function getCommentNewsFeedMy($aRow)
	{	
		if ($aRow['type_id'] == 'comment_profile_my_feedLike')
		{
			if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
			{
				$aRow['text'] = Phpfox::getPhrase('comment.a_href_user_link_full_name_a_likes_their_own_a_href_link_coment_a', array(
						'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
						'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
						'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
						'link' => Phpfox::getLib('url')->makeUrl($aRow['content'], array('feed' => $aRow['item_id'], 'flike' => 'fcomment'))
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('comment.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_comment_a', array(
						'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
						'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
						'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
						'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
						'link' => Phpfox::getLib('url')->makeUrl($aRow['content'], array('feed' => $aRow['item_id'], 'flike' => 'fcomment'))					
					)
				);
			}
			
			$aRow['icon'] = 'misc/thumb_up.png';
		}
		else 
		{
			$aRow['text'] = $aRow['content'];
			$aRow['owner_user_link'] = Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']);
			$aRow['viewer_user_link'] = Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']);
		}
		
		return $aRow;
	}
	
	public function getNewsFeedDesign_FeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('profile.a_href_user_link_full_name_a_liked_their_own_profile_a_href_link_design_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('profile.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_profile_a_href_link_design_a', array(
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
	
	public function getFeedRedirectDesign_FeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}	
	
	public function getNotificationFeedDesign_NotifyLike($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('profile.a_href_user_link_full_name_a_likes_your_recent_profile_a_href_link_design_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'])					
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'))			
		);		
	}
	
	public function sendLikeEmailDesign($iItemId)
	{
		return Phpfox::getPhrase('profile.a_href_user_link_full_name_a_likes_your_recent_profile_a_href_link_design_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'))					
				)
			);
	}		
	
	public function getCommentNewsFeedMy_Feedlike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('comment.a_href_user_link_full_name_a_likes_their_own_a_href_link_coment_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
					'link' => Phpfox::getLib('url')->makeUrl($aRow['content'], array('feed' => $aRow['item_id'], 'flike' => 'fcomment'))
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('comment.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_comment_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => Phpfox::getLib('url')->makeUrl($aRow['content'], array('feed' => $aRow['item_id'], 'flike' => 'fcomment'))					
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';

		return $aRow;	
	}
	
	public function getFeedRedirectMy($iId)
	{		
		return $this->getFeedRedirect($iId) . 'feed_' . Phpfox::getLib('request')->getInt('id') . '/flike_fcomment/';
	}
	
	public function getCommentNotificationFeedMy($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('comment.a_href_user_link_full_name_a_likes_your_a_href_link_comment_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' =>  Phpfox::getLib('url')->makeUrl('feed.view', array('id' => $aRow['item_id']))
				)
			),
			'link' =>  Phpfox::getLib('url')->makeUrl('feed.view', array('id' => $aRow['item_id']))
		);			
	}

	public function getCommentNotificationFeedMy_NotifyLike($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('comment.a_href_user_link_full_name_a_likes_your_a_href_link_comment_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' =>  Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'), array('feed' => $aRow['item_id'], 'flike' => 'fcomment'))	
				)
			),
			'link' =>  Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'), array('feed' => $aRow['item_id'], 'flike' => 'fcomment'))
		);		
	}
	
	public function getAjaxProfileController()
	{
		return 'profile.index';
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
		if ($sPlugin = Phpfox_Plugin::get('profile.service_callback___call'))
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