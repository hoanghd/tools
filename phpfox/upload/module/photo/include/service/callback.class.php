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
 * @package  		Module_Photo
 * @version 		$Id: callback.class.php 3358 2011-10-25 15:00:49Z Raymond_Benc $
 */
class Photo_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('photo');
		// if the notification module is disabled we cannot get the length to shorten, so we fallback to _iFallbackLength.
		$this->_iFallbackLength = 50;
	}
	
	public function getApiSupportedMethods()
	{
		$aMethods = array();
		
		$aMethods[] = array(
			'call' => 'addPhoto',
			'requires' => array(
				'photo' => '@FILE_PATH;type=MIME_TYPE',
				'photo_name' => 'BASE_FILE_NAME'
			),
			'detail' => 'Uploads a photo on behalf of the user.',
			'type' => 'POST',			
			'response' => '{"api":{"total":0,"pages":0,"current_page":0},"output":{"original":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d.jpg","75px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d_75.jpg","100px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d_100.jpg","150px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d_150.jpg","240px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d_240.jpg","500px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d_500.jpg","1024px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/2011\/08\/39740c5e356fdd6f66cfc973d4aebf6d_1024.jpg"}}'			 
		);		
		
		$aMethods[] = array(
			'call' => 'getPhotos',
			'requires' => array(
				'user_id' => 'user_id'
			),
			'detail' => 'Get all the photos for a user. If you do not pass the #{USER_ID} we will return information about the user that is currently logged in.',
			'type' => 'GET',			
			'response' => '{"api":{"total":1,"pages":0,"current_page":0},"output":[{"photo_100px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/c81e728d9d4c2f636f067f89cc14862c_100.jpg","photo_240px":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/c81e728d9d4c2f636f067f89cc14862c_240.jpg","photo_original":"http:\/\/[DOMAIN_REPLACE]\/file\/pic\/photo\/c81e728d9d4c2f636f067f89cc14862c.jpg"}]}'			 
		);		
		
		return array(
			'module' => 'photo', 
			'module_info' => '', 
			'methods' => $aMethods
		);
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => 'Photos',
			'link' => Phpfox::getLib('url')->makeUrl('photo'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_photos.png'))
		);
	}	

	public function enableSponsor($aParams)
	{
	    return Phpfox::getService('photo.process')->sponsor($aParams['item_id'], 1);
	}

	public function getLink($aParams)
	{
	    $aPhoto = $this->database()->select('p.title, p.photo_id')
		    ->from(Phpfox::getT('photo'),'p')
		    ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
		    ->where('p.photo_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
	    if (empty($aPhoto))
	    {
			return false;
	    }
	    //return Phpfox::getLib('url')->makeUrl($aPhoto['user_name'].'.photo.view.' . $aPhoto['title_url'] );
		return Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
	}
	
	public function getProfileLink()
	{
		return 'profile.photo';
	}

	public function getAjaxCommentVar()
	{
		return 'photo.can_post_on_photos';
	}		
	
	public function getTagLink()
	{
		return Phpfox::getLib('url')->makeUrl('photo.tag');
	}	
	
	public function getTagType()
	{
		return 'photo';
	}

	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('photo_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('photo'))
			->where('photo_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set('Unable to post a comment on this item due to privacy settings.');
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}	
	
	public function deleteComment($iId)
	{
		Phpfox::getService('photo.process')->updateCounter($iId, 'total_comment', true);	
	}	
	
	public function getFeedRedirectAlbum($iId)
	{
		$aAlbum = $this->database()->select('a.album_id, a.name, a.name_url, u.user_name')
			->from(Phpfox::getT('photo_album'), 'a')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = a.user_id')
			->where('a.album_id = ' . (int) $iId)
			->execute('getRow');
		
		if (!isset($aAlbum['album_id']))
		{
			return false;
		}
			
		return Phpfox::getLib('url')->makeUrl($aAlbum['user_name'], array('photo', $aAlbum['name_url']));		
	}
	
	public function getFeedRedirectAlbum_FeedLike($iId, $iChild = 0)
	{
		return $this->getFeedRedirectAlbum($iChild);	
	}	
	
	public function getFeedRedirectFeedLike($iId, $iChild = 0)
	{
		return $this->getFeedRedirect($iChild);	
	}
	
	public function getFeedRedirect($iId, $iChild = 0)
	{
		$aPhoto = $this->database()->select('p.photo_id, p.title')
			->from($this->_sTable, 'p')
			->where('p.photo_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aPhoto['photo_id']))
		{
			return false;
		}		
		
		return Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);
	}	
	
	public function getNewsFeedAlbum($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('photo.service_callback_getnewsfeedalbum_start')){eval($sPlugin);}
		$aPart = unserialize($aRow['content']);
		$aRow['album_name'] = $aPart['name'];
		$aRow['photo_images'] = $aPart['images'];

		
		$aRow['text'] = Phpfox::getPhrase('photo.feed_user_add_photos', array(
				'owner_full_name' => $aRow['owner_full_name'],
				'owner_link' => Phpfox::getLib('url')->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
				'total' => count($aRow['photo_images']),
				'album_name' => Phpfox::getService('feed')->shortenTitle($aRow['album_name']),
				'photo_images' => $aRow['photo_images'],
				'link' => $aRow['link'],
				'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1)
			)
		);
		
		$sImages = '';
		foreach ($aRow['photo_images'] as $aImage)
		{
			$sImage = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aImage['server_id'],
					'path' => 'photo.url_photo',
					'file' => $aImage['destination'],
					'suffix' => '_100',
					'max_width' => 100,
					'max_height' => 100,
					'style' => 'vertical-align:top; padding-right:5px;'
				)
			);
			
			if (preg_match('/misc\/noimage/', $sImage))
			{
				continue;
			}			
			
			if (isset($aImage['link']))
			{				
				$aRow['link'] = Phpfox::getLib('url')->makeUrl($aImage['link'][0], array($aImage['link'][1], $aImage['link'][2], $aImage['link'][3]));
			}			
			if (isset($aImage['mature']))
			{
				if (($aImage['mature'] == 0 || (($aImage['mature'] == 1 || $aImage['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aRow['user_id'] == Phpfox::getUserId())
				{
					$sImages .= '<a href="' . $aRow['link'] . '">' . $sImage . '</a>';
				}
				else
				{
					$sImages .= '<a href="' . $aRow['link'] . '"' . ($aImage['mature'] == 1 ? 'onclick="tb_show(\'' . Phpfox::getPhrase('photo.warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&amp;width=350&amp;link=' . $aRow['link'] . '\')); return false;"' : '') . '><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/no_access.png') . '" alt="" width="100px" /></a>';
				}
			}
					//$sImages .= '<a href="' . $aRow['link'] . '">' . $sImage . '</a>';
		}
		$aRow['text'] .= '<div class="p_4">' . $sImages . '</div>';		
		
		$aRow['icon'] = 'module/photo.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}
	
	public function getNewsFeed($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('photo.service_callback_getnewsfeed_start')){eval($sPlugin);}
		$aImages = unserialize($aRow['content']);
		
		if (count($aImages) == 1)
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_profile_link_owner_full_name_a_uploaded_a_new_photo', array(
					'profile_link' => Phpfox::getLib('url')->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
					'owner_full_name' => $this->preParse()->clean($aRow['owner_full_name'])
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_profile_link_owner_full_name_a_uploaded_new_photos', array(
					'profile_link' => Phpfox::getLib('url')->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
					'owner_full_name' => $this->preParse()->clean($aRow['owner_full_name'])			
				)
			);
		}
		
		$sImages = '';
		foreach ($aImages as $aImage)
		{
			$sImage = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aImage['server_id'],
					'path' => 'photo.url_photo',
					'file' => $aImage['destination'],
					'suffix' => '_100',
					'max_width' => 100,
					'max_height' => 100,
					'style' => 'vertical-align:top; padding-right:5px;'
				)
			);
			
			if (preg_match('/misc\/noimage/', $sImage))
			{
				continue;
			}			
			
			if (isset($aImage['link']))
			{				
				$aRow['link'] = Phpfox::getLib('url')->makeUrl(implode('.', $aImage['link']));
			}			
			
			if (isset($aImage['mature']))
			{
				if (($aImage['mature'] == 0 || (($aImage['mature'] == 1 || $aImage['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aRow['user_id'] == Phpfox::getUserId())
				{
					$sImages .= '<a href="' . $aRow['link'] . '">' . $sImage . '</a>';
				}
				else 
				{
					$sImages .= '<a href="' . $aRow['link'] . '"' . ($aImage['mature'] == 1 ? 'onclick="tb_show(\'' . Phpfox::getPhrase('photo.warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&amp;width=350&amp;link=' . $aRow['link'] . '\')); return false;"' : '') . '><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/no_access.png') . '" alt="" width="100px" /></a>';
				}
			}
			else 
			{
				$sImages .= '<a href="' . $aRow['link'] . '">' . $sImage . '</a>';
			}
		}
		$aRow['text'] .= '<div class="p_4">' . $sImages . '</div>';		
		
		$aRow['icon'] = 'module/photo.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}	

	/**
	  * @param int $iId video_id
	  * @return array in the format:
	     * array(
	     *	'title' => 'item title',		    <-- required
	     *  'link'  => 'makeUrl()'ed link',		    <-- required
	     *  'paypal_msg' => 'message for paypal'	    <-- required
	     *  'item_id' => int			    <-- required
	     *	'error' => 'phrase if item doesnt exit'	    <-- optional
	     *	'extra' => 'description'		    <-- optional
	     *	'image' => 'path to an image',		    <-- optional
	     *  'image_dir' => 'photo.url_photo|...	    <-- optional (required if image)
	     *	'server_id' => database value		    <-- optional (required if image)
	     * )
	    */
	public function getToSponsorInfo($iId)
	{
	    $aImage = $this->database()->select('p.user_id, p.title, p.photo_id as item_id, p.server_id, p.destination as image, u.user_name')
		    ->from($this->_sTable, 'p')
		    ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
		    ->where('p.photo_id = ' . (int) $iId)
		    ->execute('getSlaveRow');

	    if (empty($aImage))
	    {
			return array('error' => Phpfox::getPhrase('photo.sorry_the_photo_you_are_looking_for_no_longer_exists',array('link'=> Phpfox::getLib('url')->makeUrl('profile.photo'))));
	    }
	    
	    $aImage['link'] = Phpfox::permalink('photo',$aImage['item_id'], $aImage['title']);;//Phpfox::getLib('url')->makeUrl('photo.'.$aImage['photo_id'].'.'.$aImage['title']);
	    $aImage['paypal_msg'] = Phpfox::getPhrase('photo.sponsor_paypal_message', array('sPhotoTitle' => $aImage['title']));
	    $aImage['image_dir'] = 'photo.url_photo';
	    $aImage['title'] = Phpfox::getPhrase('photo.sponsor_title', array('sPhotoTitle' => $aImage['title']));
	    
	    return $aImage;
	}
	
	public function getCommentNewsFeed($aRow)
	{	
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		
		
		$aPart = unserialize($aRow['content']);
		$aRow['content'] = $aPart['content'];
		$aRow['image_path'] = $aPart['destination'];
		
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_photo', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link']
				)
			);
		}
		else 
		{
			if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
			{
				$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_photo_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link']						
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_added_a_new_comment_on', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link'],
						'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name']),
						'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
					)
				);
			}
		}		
	
			
		$sImage = '';
		if (!empty($aRow['image_path']))
		{
			$sImage = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aPart['server_id'],
					'path' => 'photo.url_photo',
					'file' => $aRow['image_path'],
					'suffix' => '_75',
					'max_width' => 75,
					'max_height' => 75,
					'style' => 'vertical-align:top; padding-right:5px;'					
				)
			);
			if (isset($aPart['mature']))
			{
				if (($aPart['mature'] == 0 || // its public
						(
							($aPart['mature'] == 1 || $aPart['mature'] == 2) && // its restricted
							Phpfox::getUserId() && // is user
							(Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age')))) || // user is older
							$aRow['user_id'] == Phpfox::getUserId() // owner and viewer are the same
						)
				{
					$sImage = '<a href="' . $aRow['link'] . '">' . $sImage . '</a>';
				}
				else if ($aPart['mature'] == 1)
				{
					$sImage = '<a href="' . $aRow['link'] . '" onclick="tb_show(\'' . Phpfox::getPhrase('photo.warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&amp;width=350&amp;link=' . $aRow['link'] . '\')); return false;"><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/no_access.png') . '" alt="" width="100px" /></a>';
				}
				else
				{
					$sImage = '<a href="' . $aRow['link'] . '"' . '><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/no_access.png') . '" alt="" width="100px" /></a>';
				}
			}
			else
			{
				$sImage = '<a href="' . $aRow['link'] . '">' . $sImage . '</a>';
			}
		}		
					
		$aRow['text'] .= '<div class="p_4"><div class="go_left">' . $sImage . '</div><div style="margin-left:75px;">' . Phpfox::getService('feed')->quote($aRow['content']) . '</div><div class="clear"></div></div>';
		
		return $aRow;
	}
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{		
		$aRow = $this->database()->select('u.full_name, u.gender, u.user_id, u.user_name, p.photo_id, p.title')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.photo_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
		
		$this->database()->updateCounter('photo', 'total_comment', 'photo_id', $aRow['photo_id']);		
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('photo', $aRow['photo_id'], $aRow['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['photo_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your photo "' . $this->preParse()->clean($aRow['title'], 100) . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your photo \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_photo',
				'mass_id' => 'photo',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' photo.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aRow['full_name'] . '\'s photo.'),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aRow['gender'], 1) . " photo \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aRow['full_name'] . "'s photo \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);	
	}
	
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('photo_id, title, user_id')
			->from(Phpfox::getT('photo'))
			->where('photo_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['photo_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'photo\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'photo', 'photo_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('custom.full_name_liked_your_photo_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(Phpfox::getPhrase('custom.full_name_liked_your_photo_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			if (Phpfox::isModule('notification'))
			{
				Phpfox::getService('notification.process')->add('photo_like', $aRow['photo_id'], $aRow['user_id']);				
			}
		}
	}
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('b.photo_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('photo'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.photo_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$aRow['title'] = Phpfox::getLib('parse.output')->split($aRow['title'], 20);
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('photo', $aRow['photo_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'photo\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'photo', 'photo_id = ' . (int) $iItemId);	
	}		
	
	public function updateCommentText($aVals, $sText)
	{
		
	}

	public function getCommentNotification($aNotification)
	{		
		$aRow = $this->database()->select('p.photo_id, p.title, u.user_id, u.gender, u.user_name, u.full_name')	
			->from(Phpfox::getT('photo'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.photo_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}

		return array(
			'link' => Phpfox::getLib('url')->permalink('photo', $aRow['photo_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}		
	
	public function getItemName($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">' . Phpfox::getPhrase('photo.on_name_s_photo', array('name' => $sName)) . '</a>';
	}	

	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}	
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}	
	
	public function getReportRedirectAlbum($iId)
	{
		return $this->getFeedRedirectAlbum($iId);
	}
	
	public function getCommentItemName()
	{
		return 'photo';
	}	
	
	public function processCommentModeration($sAction, $iId)
	{
		// Is this comment approved?
		if ($sAction == 'approve')
		{
			// Update the blog comment count
			Phpfox::getService('photo.process')->updateCounter($iId, 'total_comment');
			
			// Get the blogs details so we can add it to our news feed
			$aPhoto = $this->database()->select('u.full_name, u.user_id, u.user_name, p.title_url, p.destination, ct.text_parsed, p.album_id, pa.name_url AS album_url, c.comment_id, c.user_id AS comment_user_id')			
				->from($this->_sTable, 'p')								
				->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'photo\' AND c.item_id = p.photo_id')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
				->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
				->where('p.photo_id = ' . (int) $iId)
				->execute('getSlaveRow');
				
			// Add to news feed
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('comment_photo', $iId, $aPhoto['text_parsed'], Phpfox::getUserBy('user_name'), $aPhoto['user_id'], $aPhoto['full_name'], $aPhoto['destination'], $aPhoto['comment_id']) : null);
			
			// Send the user an email			
			$sLink = ($aPhoto['album_id'] ? Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'), array('photo', $aPhoto['album_url'], $aPhoto['title_url'])) : Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name'), array('photo', 'view', $aPhoto['title_url'])));					
			
			Phpfox::getLib('mail')->to($aPhoto['comment_user_id'])
				->subject(array('photo.full_name_approved_your_comment_on_site_title', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title'))))
				->message(array('photo.full_name_approved_your_comment_on_site_title_message', array(
							'full_name' => Phpfox::getUserBy('full_name'),
							'site_title' => Phpfox::getParam('core.site_title'),
							'link' => $sLink
						)
					)
				)
				->notification('comment.approve_new_comment')
				->send();
		}
	}	
	
	public function getWhatsNew()
	{
		return array(
			'photo.photos' => array(
				'ajax' => '#photo.getNew?id=js_new_item_holder',
				'id' => 'photo',
				'block' => 'photo.new'
			)
		);
	}
	
	public function getTagCloud()
	{
		return array(
			'link' => 'photo',
			'category' => 'photo'
		);
	}

	public function globalSearch($sQuery, $bIsTagSearch = false)
	{
		$sCondition = 'p.view_id = 0 AND p.group_id = 0 AND p.privacy = 0';
		if ($bIsTagSearch === false)
		{
			$sCondition .= ' AND (p.title LIKE \'%' . $this->database()->escape($sQuery) . '%\' OR pi.description LIKE \'%' . $this->database()->escape($sQuery) . '%\')';
		}
		
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.photo_id AND tag.category_id = \'photo\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'');
		}		
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'p')
			->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
			->where($sCondition)
			->execute('getSlaveField');		
			
		if ($bIsTagSearch == true)
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = p.photo_id AND tag.category_id = \'photo\' AND tag.tag_url = \'' . $this->database()->escape($sQuery) . '\'')->group('p.photo_id');
		}			
		
		$aRows = $this->database()->select('p.title, p.title_url, p.time_stamp, p.destination, p.server_id, p.album_id, pa.name_url AS album_url, ' . Phpfox::getUserField())
			->from($this->_sTable, 'p')			
			->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')			
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
			->where($sCondition)
			->limit(10)
			->order('p.time_stamp DESC')
			->execute('getSlaveRows');
			
		if (count($aRows))
		{
			$aResults = array();
			$aResults['total'] = $iCnt;
			$aResults['menu'] = Phpfox::getPhrase('photo.photos');
			
			if ($bIsTagSearch == true)
			{
				$aResults['form'] = '<div><input type="button" value="' . Phpfox::getPhrase('photo.view_more_photos') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('photo', array('tag', $sQuery)) . '\';" /></div>';
			}
			else 
			{			
				$aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('photo') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="submit" value="' . Phpfox::getPhrase('photo.view_more_photos') . '" class="search_button" /></div></form>';
			}
			
			foreach ($aRows as $iKey => $aRow)
			{
				$aResults['results'][$iKey] = array(			
					'title' => $aRow['title'],	
					'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('photo', ($aRow['album_id'] ? $aRow['album_url'] : 'view'), $aRow['title_url'])),
					'image' => Phpfox::getLib('image.helper')->display(array(
							'server_id' => $aRow['server_id'],
							'title' => $aRow['title'],
							'path' => 'photo.url_photo',
							'file' => $aRow['destination'],
							'suffix' => '_75',
							'max_width' => 75,
							'max_height' => 75
						)
					),
					'extra_info' => Phpfox::getPhrase('photo.a_href_photo_section_link_photo_a_uploaded_on_time_stamp_by_a_href_user_link', array(
							'photo_section_link' => Phpfox::getLib('url')->makeUrl('photo'),
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
	
	public function verifyFavorite($iItemId)
	{
		$aItem = $this->database()->select('i.photo_id')
			->from(Phpfox::getT('photo'), 'i')
			->where('i.photo_id = ' . (int) $iItemId . ' AND i.view_id = 0 AND i.privacy IN(0,1)')
			->execute('getSlaveRow');
			
		if (!isset($aItem['photo_id']))
		{
			return false;
		}

		return true;
	}		

	public function verifyFavoriteAlbum($iItemId)
	{
		$aItem = $this->database()->select('i.album_id')
			->from(Phpfox::getT('photo_album'), 'i')
			->where('i.album_id = ' . (int) $iItemId . ' AND i.view_id = 0 AND i.privacy IN(0,1)')
			->execute('getSlaveRow');
			
		if (!isset($aItem['album_id']))
		{
			return false;
		}

		return true;
	}	
	
	public function getFavoriteAlbum($aFavorites)
	{
		$aItems = $this->database()->select('i.name AS title, i.album_id, i.time_stamp, i.name_url AS album_url, p.title_url, p.destination, p.server_id AS photo_server_id, ' . Phpfox::getUserField())
			->from(Phpfox::getT('photo_album'), 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
			->leftJoin(Phpfox::getT('photo'), 'p', 'p.album_id = i.album_id AND i.view_id = 0 AND p.is_cover = 1')
			->where('i.album_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')
			->execute('getSlaveRows');
			
		foreach ($aItems as $iKey => $aItem)
		{
			$aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aItem['photo_server_id'],
					'path' => 'photo.url_photo',
					'file' => $aItem['destination'],
					'suffix' => '_75',
					'max_width' => 75,
					'max_height' => 75
				)
			);			
			
			$aItems[$iKey]['link'] = Phpfox::getLib('url')->makeUrl($aItem['user_name'], array('photo', $aItem['album_url']));
		}		
		
		return array(
			'title' => Phpfox::getPhrase('photo.photo_albums'),
			'items' => $aItems
		);
	}
	
	public function getFavorite($aFavorites)
	{
		$aItems = $this->database()->select('i.title, i.album_id, i.time_stamp, p.name_url AS album_url, i.title_url, i.destination, i.server_id AS photo_server_id, ' . Phpfox::getUserField())
			->from(Phpfox::getT('photo'), 'i')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
			->leftJoin(Phpfox::getT('photo_album'), 'p', 'p.album_id = i.album_id')
			->where('i.photo_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')
			->execute('getSlaveRows');
			
		foreach ($aItems as $iKey => $aItem)
		{
			$aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aItem['photo_server_id'],
					'path' => 'photo.url_photo',
					'file' => $aItem['destination'],
					'suffix' => '_75',
					'max_width' => 75,
					'max_height' => 75
				)
			);			
			
			$aItems[$iKey]['link'] = ($aItem['album_id'] ? Phpfox::getLib('url')->makeUrl($aItem['user_name'], array('photo', $aItem['album_url'], $aItem['title_url'])) : Phpfox::getLib('url')->makeUrl($aItem['user_name'], array('photo', 'view', $aItem['title_url'])));			
		}		
		
		return array(
			'title' => Phpfox::getPhrase('photo.photos'),
			'items' => $aItems
		);
	}	
	
	public function getDashboardLinks()
	{
		return array(
			'submit' => array(
				'phrase' => Phpfox::getPhrase('photo.upload_a_photo'),
				'link' => 'photo.upload',
				'image' => 'misc/photo_feature.png'
			),
			'edit' => array(
				'phrase' => Phpfox::getPhrase('photo.manage_photos'),
				'link' => 'profile.photo',
				'image' => 'misc/photos.png'
			)			
		);
	}	
	
	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('photo.photos') => $aUser['activity_photo']
		);
	}	

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		// get the ids for the photos of this user
		$aPhotos = $this->database()
			->select('p.photo_id, pa.album_id, pb.battle_id, pt.tag_id')
			->from($this->_sTable, 'p')
			->leftjoin(Phpfox::getT('photo_album'), 'pa', 'pa.user_id = ' . (int)$iUser)
			->leftjoin(Phpfox::getT('photo_battle'), 'pb', 'pb.user_id = ' . (int)$iUser)
			->leftjoin(Phpfox::getT('photo_tag'), 'pt', 'pt.user_id = ' . (int)$iUser)
			->where('p.user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aPhotos as $aPhoto)
		{
			if (isset($aPhoto['photo_id']))
			{
				Phpfox::getService('photo.process')->delete($aPhoto['photo_id']);
			}
			if (isset($aPhoto['album_id']))
			{
				Phpfox::getService('photo.album.process')->delete($aPhoto['album_id']);
			}
			if (isset($aPhoto['tag_id']))
			{
				// delete tags added by this user
				Phpfox::getService('photo.tag.process')->delete($aPhoto['tag_id']);
			}
		}

		Phpfox::getService('photo.battle.process')->deleteByUser($iUser);
		Phpfox::getService('photo.rate.process')->deleteByUser($iUser);
		
	}
	
	public function groupMenu($sGroupUrl, $iGroupId)
	{
		if (!Phpfox::getService('group')->hasAccess($iGroupId, 'can_use_photo'))
		{
			return false;
		}		
		
		return array(
				Phpfox::getPhrase('photo.photos') => array(
					'active' => 'photo',
					'url' => Phpfox::getLib('url')->makeUrl('group', array($sGroupUrl, 'photo')
				)
			)
		);
	}	

	public function getItemView()
	{
		if (Phpfox::getLib('request')->get('req3') == 'view')
		{
			return true;
		}
	}
	
	public function getGroupPosting()
	{
		return array(
			Phpfox::getPhrase('photo.upload_photos') => 'can_upload_photo'
		);
	}
	
	public function getGroupAccess()
	{
		return array(
			Phpfox::getPhrase('photo.view_photos') => 'can_use_photo'
		);
	}	
	
	public function getProfileSettings()
	{
		return array(
			'photo.display_on_profile' => array(
				'phrase' => Phpfox::getPhrase('user.view_photos_within_your_profile'),
				'default' => '0'				
			)
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
			'title' => Phpfox::getPhrase('photo.photos')
		);
	}		

	public function legacyRedirect($aRequest)
	{
		if (isset($aRequest['req2']))
		{
			switch ($aRequest['req2'])
			{
				case 'view':
					if (isset($aRequest['id']))
					{
						$aItem = Phpfox::getService('core')->getLegacyUrl(array(
								'url_field' => 'title_url',
								'table' => 'photo',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['id']
							)
						);
						
						if ($aItem !== false)
						{
							return array($aItem['user_name'], array('photo', 'view', $aItem['title_url']));
						}
					}
					break;
				case 'rate':
					return 'photo.rate';
					break;
				case 'battle':
					return 'photo.battle';
					break;					
				default:
					return 'photo';
					break;
			}
		}
		
		return false;
	}
	
	public function addTrack($iId, $iUserId = null)
	{
		$this->database()->insert(Phpfox::getT('photo_track'), array(
				'item_id' => (int) $iId,
				'user_id' => Phpfox::getUserBy('user_id'),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}	
	
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('photo.photos'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('photo'))
				->where('view_id = 0 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}

	public function updateCounterList()
	{
		$aList = array();		
		
		$aList[] = array(
			'name' => Phpfox::getPhrase('photo.photo_count_for_photo_albums'),
			'id' => 'photo-album'			
		);		
		
		$aList[] = array(
			'name' => Phpfox::getPhrase('photo.update_tags_photo'),
			'id' => 'photo-tag-update'			
		);

		$aList[] = array(
			'name' => Phpfox::getPhrase('photo.update_photo_thumbnails'),
			'id' => 'photo-thumbnail'			
		);	
		
		$aList[] = array(
			'name' => 'Update User Photo Count',
			'id' => 'photo-count'			
		);			
		
		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{		
		if ($iId == 'photo-tag-update')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('tag'))
				->where('category_id = \'photo\'')
				->execute('getSlaveField');			
				
			$aRows = $this->database()->select('m.tag_id, oc.photo_id AS tag_item_id')
				->from(Phpfox::getT('tag'), 'm')
				->where('m.category_id = \'photo\'')
				->leftJoin(Phpfox::getT('photo'), 'oc', 'oc.photo_id = m.item_id')
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
		elseif ($iId == 'photo-thumbnail')
		{
			@ini_set('memory_limit', '100M');
			
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('photo'))
				->where($this->database()->isNotNull('destination'))
				->execute('getSlaveField');			
				
			$aRows = $this->database()->select('photo_id, destination')
				->from(Phpfox::getT('photo'))
				->where($this->database()->isNotNull('destination'))
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');		
		
			$oImage = Phpfox::getLib('image');
			foreach ($aRows as $aRow)
			{				
				if (preg_match("/\{file\/pic\/(.*)\/(.*)\.jpg\}/i", $aRow['destination'], $aMatches))
				{
					$sPath = PHPFOX_DIR;
					$sFileName = str_replace(array('{', '.jpg}'), array('', '%s.jpg'), $aRow['destination']);			
				}
				else 
				{
					$sPath = Phpfox::getParam('photo.dir_photo');
					$sFileName = $aRow['destination'];
				}							
				
				if (file_exists($sPath . sprintf($sFileName, '')))
				{					
					foreach(Phpfox::getParam('photo.photo_pic_sizes') as $iSize)
					{					
						if ($oImage->createThumbnail($sPath . sprintf($sFileName, ''), $sPath . sprintf($sFileName, '_' . $iSize), $iSize, $iSize) === false)
						{							
							continue;
						}						
						
						if (Phpfox::getParam('photo.enabled_watermark_on_photos'))
						{
						 	$oImage->addMark($sPath . sprintf($sFileName, '_' . $iSize));
						}					
					}
					
					if (Phpfox::getParam('photo.enabled_watermark_on_photos'))					
					{						
					 	$oImage->addMark($sPath . sprintf($sFileName, ''));
					}
				}				
			}
			
			return $iCnt;
		}	
		elseif ($iId == 'photo-count')
		{
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('user'))
				->execute('getSlaveField');					

			$aRows = $this->database()->select('u.user_id')
				->from(Phpfox::getT('user'), 'u')
				->limit($iPage, $iPageLimit, $iCnt)
				->group('u.user_id')
				->execute('getSlaveRows');

			foreach ($aRows as $aRow)
			{
				$iTotalPhotos = $this->database()->select('COUNT(f.photo_id)')
					->from(Phpfox::getT('photo'), 'f')
					->where('f.view_id = 0 AND f.group_id = 0 AND f.type_id = 0 AND f.user_id = ' . $aRow['user_id'])
					->execute('getSlaveField');		

				$this->database()->update(Phpfox::getT('user_field'), array('total_photo' => $iTotalPhotos), 'user_id = ' . $aRow['user_id']);			
			}			
		}
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('photo_album'))
			->execute('getSlaveField');
			
		$aRows = $this->database()->select('g.album_id, COUNT(gi.photo_id) AS total_items')
			->from(Phpfox::getT('photo_album'), 'g')
			->leftJoin(Phpfox::getT('photo'), 'gi', 'gi.album_id = g.album_id')
			->group('g.album_id')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');
			
		foreach ($aRows as $aRow)
		{
			$this->database()->update(Phpfox::getT('photo_album'), array('total_photo' => $aRow['total_items']), 'album_id = ' . (int) $aRow['album_id']);
		}
			
		return $iCnt;
	}

	public function deleteGroup($iId)
	{
		$aRows = $this->database()->select('*')
			->from($this->_sTable)
			->where('group_id = ' . (int) $iId)
			->execute('getRows');
			
		foreach ($aRows as $aRow)
		{
			Phpfox::getService('photo.process')->delete($aRow['photo_id'], true);
		}
		
		return true;
	}

	public function getNewsFeedAlbum_FeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_their_own_a_href_link_photo_album_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_photo_album_a', array(
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
	
	public function getNewsFeedFeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_their_own_a_href_link_photo_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_photo_a', array(
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
	
	public function getNotificationFeedAlbum_NotifyLike($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_your_a_href_link_photo_album_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('photo', array('aredirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('photo', array('aredirect' => $aRow['item_id']))			
		);			
	}
	
	public function getNotificationFeedNotifyLike($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_your_a_href_link_photo_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('photo', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('photo', array('redirect' => $aRow['item_id']))			
		);			
	}
	
	public function sendLikeEmailAlbum($iItemId)
	{
		return Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_your_a_href_link_photo_album_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('photo', array('aredirect' => $iItemId))
				)
			);		
	}
	
	public function sendLikeEmail($iItemId)
	{
		return Phpfox::getPhrase('photo.a_href_user_link_full_name_a_liked_your_a_href_link_photo_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('photo', array('redirect' => $iItemId))
				)
			);			
	}
	
	public function getActivityPointField()
	{
		return array(
			Phpfox::getPhrase('photo.photos') => 'activity_photo'
		);
	}	
	
	public function pendingApproval()
	{
		return array(
			'phrase' => Phpfox::getPhrase('photo.photos'),
			'value' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('photo'))->where('view_id = 1')->execute('getSlaveField'),
			'link' => Phpfox::getLib('url')->makeUrl('photo', array('view' => 'approval'))
		);
	}

	public function getSqlTitleField()
	{
		return array(
			'table' => 'photo',
			'field' => 'title'
		);
	}		
	
	public function getActivityFeed($aItem, $aCallback = null)
	{		
		if ($aCallback === null)
		{
			$this->database()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = photo.parent_user_id');
		}

		$aRow = $this->database()->select('photo.*, l.like_id AS is_liked, pi.description, pfeed.photo_id AS extra_photo_id, pa.album_id, pa.name')
			->from($this->_sTable, 'photo')			
			->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = photo.photo_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'photo\' AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId())
			->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'pfeed.feed_id = ' . (int) $aItem['feed_id'])
			->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = photo.album_id')
			->where('photo.photo_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
		
		if (!isset($aRow['photo_id']))
		{
			return false;
		}
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'photo.view_browse_photos'))
		{
			return false;
		}		
			
		$sImage = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['server_id'],
				'path' => 'photo.url_photo',
				'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aRow, array('full_name' => $aItem['full_name']))),
				'suffix' => '_100',
				'max_width' => 100,
				'max_height' => 100,
				'class' => 'photo_holder'
			)
		);
		
		$aListPhotos = array();	
		if ($aRow['extra_photo_id'] > 0)
		{
			$aPhotos = $this->database()->select('p.photo_id, p.title, p.server_id, p.destination')	
				->from(Phpfox::getT('photo_feed'), 'pfeed')
				->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pfeed.photo_id')
				->where('pfeed.feed_id = ' . (int) $aItem['feed_id'])
				->limit(3)
				->order('p.time_stamp DESC')
				->execute('getSlaveRows');
			
			foreach ($aPhotos as $aPhoto)
			{			
				$aListPhotos[] = '<a href="' . Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']) . '" class="thickbox photo_holder_image" rel="' . $aPhoto['photo_id'] . '">' . Phpfox::getLib('image.helper')->display(array(
						'server_id' => $aPhoto['server_id'],
						'path' => 'photo.url_photo',
						'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aPhoto, array('full_name' => $aItem['full_name']))),
						'suffix' => '_100',
						'max_width' => 100,
						'max_height' => 100,
						'class' => 'photo_holder'			
					)
				) . '</a>';
			}	
			
			$aListPhotos = array_merge($aListPhotos, array('<a href="' . Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']) . '" class="thickbox photo_holder_image" rel="' . $aRow['photo_id'] . '">' . $sImage . '</a>'));
		}
			
		$aReturn = array(
			'feed_title' => '',
			'feed_image' => (count($aListPhotos) ? $aListPhotos : $sImage),
			'feed_status' => $aRow['description'],
			'feed_link' => Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']),
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/photo.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'photo',
			'like_type_id' => 'photo',
			'custom_css' => 'thickbox photo_holder_image',
			'custom_rel' => $aRow['photo_id']
		);	
		
		if ($aRow['module_id'] == 'pages')
		{
			$aRow['parent_user_id'] = '';
			$aRow['parent_user_name'] = '';
		}
		
		if (empty($aRow['parent_user_id']))
		{
			if ($aRow['album_id'])
			{
				$aReturn['feed_info'] = Phpfox::getPhrase('feed.added_new_photos_to_gender_album_a_href_link_name_a', array('gender' => Phpfox::getService('user')->gender($aItem['gender'], 1), 'link' => Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['name']), 'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...')));
			}
			else
			{
				$aReturn['feed_info'] = (count($aListPhotos) ? Phpfox::getPhrase('feed.shared_a_few_photos') : Phpfox::getPhrase('feed.shared_a_photo'));			
			}
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
				$aReturn['feed_mini_content'] = '' . Phpfox::getService('user')->getFirstName($aItem['full_name']) . ' posted <a href="' . Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']) . '">a photo</a> on <a href="' . Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']) . '">' . $aRow['parent_full_name'] . '</a>\'s <a href="' . Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']) . '">wall</a>.';
				
				unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_content']);
			}		
		}
		
		return $aReturn;
	}	

	public function tabHasItems($iUser)
	{
		$iCount = $this->database()->select('COUNT(user_id)')
				->from(Phpfox::getT('photo'))
				->where('user_id = ' . (int)$iUser)
				->execute('getSlaveField');
		return $iCount > 0;
	}
	
	public function addLikeAlbum($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('album_id, name, user_id')
			->from(Phpfox::getT('photo_album'))
			->where('album_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['album_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'photo_album\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'photo_album', 'album_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['name']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('custom.full_name_liked_your_photo_album_name', array('full_name' => Phpfox::getUserBy('full_name'), 'name' => $aRow['name'])))
				->message(Phpfox::getPhrase('custom.full_name_liked_your_photo_album_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'name' => $aRow['name'])))
				->send();
					
			Phpfox::getService('notification.process')->add('photo_album_like', $aRow['album_id'], $aRow['user_id']);				
		}
	}
	
	public function getNotificationAlbum_Like($aNotification)
	{
		$aRow = $this->database()->select('b.album_id, b.name, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('photo_album'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.album_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own photo album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your photo album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> photo album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('photo.album', $aRow['album_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function deleteLikeAlbum($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'photo_album\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'photo_album', 'album_id = ' . (int) $iItemId);	
	}	
	
	public function addCommentAlbum($aVals, $iUserId = null, $sUserName = null)
	{	
		$aAlbum = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, b.name, b.album_id, b.privacy')
			->from(Phpfox::getT('photo_album'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.album_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		$this->database()->updateCounter('photo_album', 'total_comment', 'album_id', $aVals['item_id']);		
		
		// Send the user an email
		$sLink = Phpfox::permalink('photo_album', $aAlbum['album_id'], $aAlbum['name']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aAlbum['user_id'],
				'item_id' => $aAlbum['album_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your photo album "' . $aAlbum['name'] . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your photo album \"<a href=\"" . $sLink . "\">" . $aAlbum['name'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_photo_album',
				'mass_id' => 'photo_album',
				'mass_subject' => (Phpfox::getUserId() == $aAlbum['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aAlbum['gender'], 1) . ' photo album.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aAlbum['full_name'] . '\'s photo album.'),
				'mass_message' => (Phpfox::getUserId() == $aAlbum['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aAlbum['gender'], 1) . " photo album \"<a href=\"" . $sLink . "\">" . $aAlbum['name'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aAlbum['full_name'] . "'s photo album \"<a href=\"" . $sLink . "\">" . $aAlbum['name'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
	}	
	
	public function getAjaxCommentVarAlbum()
	{
		return null;
	}	
	
	public function getCommentItemAlbum($iId)
	{
		$aRow = $this->database()->select('album_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('photo_album'))
			->where('album_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set('Unable to post a comment on this item due to privacy settings.');
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}	
	
	public function getCommentNotificationAlbum($aNotification)
	{
		$aRow = $this->database()->select('b.album_id, b.name, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('photo_album'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.album_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' photo album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your photo album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> photo album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('photo.album', $aRow['album_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}	
	
	public function getActivityFeedAlbum($aItem, $aCallback = null)
	{	
		$aRow = $this->database()->select('pa.*, l.like_id AS is_liked, pai.description')
			->from(Phpfox::getT('photo_album'), 'pa')			
			->join(Phpfox::getT('photo_album_info'), 'pai', 'pai.album_id = pa.album_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'photo_album\' AND l.item_id = pa.album_id AND l.user_id = ' . Phpfox::getUserId())
			->where('pa.album_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['album_id']))
		{
			return false;
		}
			
		$aPhotos = $this->database()->select('p.photo_id, p.title, p.server_id, p.destination')	
			->from(Phpfox::getT('photo'), 'p')
			->where('p.album_id = ' . $aRow['album_id'] . ' AND p.view_id = 0')
			->limit(3)
			->order('p.time_stamp DESC')
			->execute('getSlaveRows');
		
		$aListPhotos = array();	
		foreach ($aPhotos as $aPhoto)
		{			
			$aListPhotos[] = '<a href="' . Phpfox::permalink('photo', $aPhoto['photo_id'], $aPhoto['title']) . '" class="thickbox photo_holder_image" rel="' . $aPhoto['photo_id'] . '">' . Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aPhoto['server_id'],
					'path' => 'photo.url_photo',
					'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aPhoto, array('full_name' => $aItem['full_name']))),
					'suffix' => '_100',
					'max_width' => 100,
					'max_height' => 100,
					'class' => 'photo_holder'			
				)
			) . '</a>';
		}	
			
		$aReturn = array(
			'feed_title' => '',
			'feed_info' => Phpfox::getPhrase('feed.added_new_photos_to_gender_album_a_href_link_name_a', array('gender' => Phpfox::getService('user')->gender($aItem['gender'], 1), 'link' => Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['name']), 'name' => Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...'))),
			'feed_image' => $aListPhotos,
			'feed_status' => $aRow['description'],
			'feed_link' => Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['name']),
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/photo.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'photo_album',
			'like_type_id' => 'photo_album',
			'feed_custom_width' => '400px'
		);	
		
		return $aReturn;
	}	
	
	public function getActivityFeedTag($aItem, $aCallback = null)
	{
		$aRow = $this->database()->select('p.photo_id, p.title, p.time_stamp, p.server_id, p.destination, p.user_id AS photo_user_id, p.privacy, f.friend_id AS is_friend, pi.description, ' . Phpfox::getUserField() . ', ' . Phpfox::getUserField('u2', 'photo_'))
			->from(Phpfox::getT('photo_tag'), 'pt')
			->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pt.photo_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ' . (int) $aItem['parent_user_id'])
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id')
			->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = p.user_id AND f.friend_user_id = " . Phpfox::getUserId())
			->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
			->where('pt.tag_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['photo_id']))
		{
			return false;
		}
		
		$bCanViewItem = true;
		if ($aRow['privacy'] > 0)
		{
			$bCanViewItem = Phpfox::getService('privacy')->check('photo', $aRow['photo_id'], $aRow['photo_user_id'], $aRow['privacy'], $aRow['is_friend'], true);
		}
		
		$sLink = Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']);
		
		if ($aItem['user_id'] == $aRow['photo_user_id'])
		{
			$sPhrase = Phpfox::getPhrase('feed.tagged_a_href_row_link_full_name_a_on_gender_a_href_link_photo_a', array('row_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']), 'full_name' => $aRow['full_name'], 'gender' => Phpfox::getService('user')->gender($aItem['gender'], 1), 'link' => $sLink));
		}
		else 
		{
			$sPhrase = Phpfox::getPhrase('feed.tagged_a_href_row_link_full_name_a_on_a_href_photo_user_name_photo_full_name_a_a_href_link_photo_a', array('row_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']), 'full_name' => $aRow['full_name'], 'photo_user_name' => Phpfox::getLib('url')->makeUrl($aRow['photo_user_name']), 'photo_full_name' => $aRow['photo_full_name'], 'link' => $sLink));
		}

		$aReturn = array(
			'feed_info' => $sPhrase,
			'feed_link' => Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']),
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/group.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => false,
		);	
			
		if ($bCanViewItem)
		{
			$aReturn['feed_image'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aRow['server_id'],
					'path' => 'photo.url_photo',
					'file' => Phpfox::getService('photo')->getPhotoUrl($aRow),
					'suffix' => '_100',
					'max_width' => 100,
					'max_height' => 100,
					'class' => 'photo_holder'
				)
			);			

			$aReturn['feed_title'] = $aRow['title'];
			$aReturn['feed_content'] = $aRow['description'];
		}
		
		return $aReturn;	
	}
	
	public function getNotificationTag($aNotification)
	{
		$aRow = $this->database()->select('b.photo_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('photo'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.photo_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = 'You tagged yourself in your photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' tagged you in <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> photo "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('photo', $aRow['photo_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);			
	}
	
	public function getAjaxProfileController()
	{
		return 'photo.index';
	}
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_photo']))
			{
				return false;
			}

			if (isset($aUser['total_photo']) && (int) $aUser['total_photo'] === 0)
			{
				return false;
			}
		}
		
		$aSubMenu = array();
		if ($this->request()->get('req2') == 'photo')
		{
			$aSubMenu[] = array(
				'phrase' => Phpfox::getPhrase('profile.albums'),
				'url' => Phpfox::getLib('url')->makeUrl($aUser['user_name'].'.photo.albums'),
				'total' => Phpfox::getService('photo.album')->getAlbumCount($aUser['user_id'])
			);		
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('profile.photos'),
			'url' => 'profile.photo',
			'total' => (int) (isset($aUser['total_photo']) ? $aUser['total_photo'] : 0),
			'icon' => 'feed/photo.png',
			'sub_menu' => $aSubMenu
		);		
		
		return $aMenus;
	}
	
	public function getTotalItemCount($iUserId)
	{
		return array(
			'field' => 'total_photo',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('photo'))->where('view_id = 0 AND group_id = 0 AND type_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
		);	
	}		

	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.photo_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'photo\' AS item_type_id, item.destination AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('photo'), 'item')
			->where('item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch) . '')
			->union();
	}	
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('photo', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = 'Photo';

		$aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => $aRow['item_photo'],
				'path' => 'photo.url_photo',
				'suffix' => '_150',
				'max_width' => '150',
				'max_height' => '150'				
			)
		);
		
		return $aInfo;
	}	
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => 'Photos'
		);
	}	
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'photo.default_privacy_setting' => array(
				'phrase' => Phpfox::getPhrase('user.photos')								
			)
		);
	}	
	
	public function getPageSubMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'photo.share_photos'))
		{
			return null;
		}		
		
		return array(
			array(
				'phrase' => 'Upload a New Image',
				'url' => Phpfox::getLib('url')->makeUrl('photo.add', array('module' => 'pages', 'item' => $aPage['page_id']))
			)
		);
	}	
	
	public function getPageMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'photo.view_browse_photos'))
		{
			return null;
		}			
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('pages.photos'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'photo/',
			'icon' => 'feed/photo.png',
			'landing' => 'photo'
		);
		
		return $aMenus;
	}		
	
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['photo.share_photos'] = 'Who can share a photo?';
		$aPerms['photo.view_browse_photos'] = 'Who can view/browse photos?';
		
		return $aPerms;
	}
	
	public function canViewPageSection($iPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($iPage, 'photo.view_browse_photos'))
		{
			return false;
		}
		
		return true;
	}
	
	public function checkFeedShareLink()
	{
		if (Phpfox::isMobile())
		{
			return false;
		}
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'photo.share_photos'))
		{
			return false;
		}
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
		if ($sPlugin = Phpfox_Plugin::get('photo.service_callback__call'))
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