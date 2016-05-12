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
 * @package  		Module_Music
 * @version 		$Id: callback.class.php 3358 2011-10-25 15:00:49Z Raymond_Benc $
 */
class Music_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		// if the notification module is disabled we fallback the length to shorten to _iFallbackLength
		$this->_iFallbackLength = 50;
	}

	public function enableSponsor($aParams)
	{
	    if ($aParams['section'] == 'album')
	    {
		return Phpfox::getService('music.process')->sponsorAlbum($aParams['item_id'], 1);
	    }
	    if ($aParams['section'] == 'song')
	    {
		return Phpfox::getService('music.process')->sponsorSong($aParams['item_id'], 1);
	    }
	}

	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('music.music_songs') => $aUser['activity_music_song']
		);
	}
	
	
	public function getLink($aParams)
	{	    
		
	    if ($aParams['section'] == 'song')
	    {
		// get the owner of this song
		$aUser = $this->database()->select('u.user_name, ma.name_url, ms.title_url')
		    ->from(Phpfox::getT('music_song'),'ms')
		    ->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
		    ->leftjoin(Phpfox::getT('music_album'),'ma', 'ma.album_id = ms.album_id') // it could be a Registered user and not a musician so album is not needed
		    ->where('ms.song_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
		if (empty($aUser))
		{
		    return false;
		}
		return Phpfox::getLib('url')->makeUrl($aUser['user_name'] . '.music.' . (isset($aUser['name_url']) ? $aUser['name_url'] : '') . '.'.$aUser['title_url'] );
	    }
	    if ($aParams['section'] == 'album')
	    {
		// get the owner of this song
		$aUser = $this->database()->select('u.user_name, ma.name_url')
		    ->from(Phpfox::getT('music_album'),'ma')
		    ->join(Phpfox::getT('user'), 'u', 'u.user_id = ma.user_id')
		    ->where('ma.album_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
		if (empty($aUser))
		{
		    return false;
		}
		return Phpfox::getLib('url')->makeUrl($aUser['user_name'] . '.music.' . (isset($aUser['name_url']) ? $aUser['name_url'] : ''));
	    }
	}
	
	public function getProfileLink()
	{
		return 'profile.music';
	}	
	
	public function getAjaxCommentVarAlbum()
	{
		return 'music.can_add_comment_on_music_album';
	}
	
	public function getAjaxCommentVarSong()
	{
		return 'music.can_add_comment_on_music_song';
	}
	
	public function getCommentNewsFeedSong($aRow)
	{		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_song', array(
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
				$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_song_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link']			
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_n', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link'],
						'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name']),
						'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
					)
				);
			}
		}
		
		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		
		return $aRow;
	}	
	
	public function getCommentItemSong($iId)
	{
		$aRow = $this->database()->select('song_id AS comment_item_id, user_id AS comment_user_id')
			->from(Phpfox::getT('music_song'))
			->where('song_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		$aRow['comment_view_id'] = 1;
			
		return $aRow;
	}	
	
	public function addCommentSong($aVals, $iUserId = null, $sUserName = null)
	{
		$aRow = $this->database()->select('m.song_id, m.title, u.full_name, u.user_id, u.gender, u.user_name')
			->from(Phpfox::getT('music_song'), 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
			->where('m.song_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['song_id']))
		{
			return Phpfox_Error::trigger('Invalid callback on a song.');
		}
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		$this->database()->updateCounter('music_song', 'total_comment', 'song_id', $aRow['song_id']);		
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('music', $aRow['song_id'], $aRow['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['song_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your song "' . $this->preParse()->clean($aRow['title'], 100) . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your song \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_music_song',
				'mass_id' => 'music_song',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' song.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aRow['full_name'] . '\'s song.'),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aRow['gender'], 1) . " song \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aRow['full_name'] . "'s song \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);			
	}	
	
	public function updateCommentTextSong($aVals, $sText)
	{
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_music_song', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
	}		
	
	public function getCommentItemAlbum($iId)
	{
		$aRow = $this->database()->select('album_id AS comment_item_id, user_id AS comment_user_id')
			->from(Phpfox::getT('music_album'))
			->where('album_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		$aRow['comment_view_id'] = 1;
			
		return $aRow;
	}	
	
	public function addCommentAlbum($aVals, $iUserId = null, $sUserName = null)
	{
		$aRow = $this->database()->select('m.album_id, m.name, u.full_name, u.user_id, u.gender, u.user_name')
			->from(Phpfox::getT('music_album'), 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
			->where('m.album_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['album_id']))
		{
			return Phpfox_Error::trigger('Invalid callback on music album.');
		}
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		$this->database()->updateCounter('music_album', 'total_comment', 'album_id', $aRow['album_id']);		
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('music.album', $aRow['album_id'], $aRow['name']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['album_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your album "' . $this->preParse()->clean($aRow['name'], 100) . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your album \"<a href=\"" . $sLink . "\">" . $aRow['name'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_music_album',
				'mass_id' => 'music_album',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' album.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aRow['full_name'] . '\'s album.'),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aRow['gender'], 1) . " album \"<a href=\"" . $sLink . "\">" . $aRow['name'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aRow['full_name'] . "'s album \"<a href=\"" . $sLink . "\">" . $aRow['name'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);
	}	
	
	public function updateCommentTextAlbum($aVals, $sText)
	{
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_music_album', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
	}		
	
	public function getItemNameSong($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">On ' . $sName . '\'s song.</a>';
	}	
	
	public function getItemNameAlbum($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">On ' . $sName . '\'s album.</a>';
	}	
	
	public function getCommentNewsFeedAlbum($aRow)
	{		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_music', array(
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
				$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_music_album', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link']				
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('music.added_a_new_comment_on_a_href_item_user_link_item_user_name_s_album', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link'],
						'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name']),
						'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
					)
				);
			}
		}

		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		
		return $aRow;
	}	
	
	public function getFeedRedirectAlbum($iId)
	{
		$aRow = $this->database()->select('m.album_id, name')
			->from(Phpfox::getT('music_album'), 'm')
			->where('m.album_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['album_id']))
		{
			return false;
		}
			
		return Phpfox::permalink('music.album', $aRow['album_id'], $aRow['name']);
	}	
	
	public function getReportRedirectAlbum($iId)
	{
		return $this->getFeedRedirectAlbum($iId);
	}
	
	public function getReportRedirectSong($iId)
	{
		return $this->getFeedRedirectSong($iId);
	}	
	
	public function getFeedRedirectSong($iId)
	{
		$aRow = $this->database()->select('m.song_id, m.title')
			->from(Phpfox::getT('music_song'), 'm')
			->where('m.song_id = ' . (int) $iId)
			->execute('getSlaveRow');;
			
		if (!isset($aRow['song_id']))
		{
			return false;
		}
			
		return Phpfox::permalink('music', $aRow['song_id'], $aRow['title']);
	}	
	
	public function deleteCommentSong($iId)
	{
		$this->database()->updateCounter('music_song', 'total_comment', 'song_id', $iId, true);
	}	
	
	public function deleteCommentAlbum($iId)
	{
		$this->database()->updateCounter('music_album', 'total_comment', 'album_id', $iId, true);
	}

	public function getRatingDataAlbum($iId)
	{
		return array(
			'field' => 'album_id',
			'table' => 'music_album',
			'table_rating' => 'music_album_rating'
		);
	}
	
	public function getRatingDataSong($iId)
	{
		return array(
			'field' => 'song_id',
			'table' => 'music_song',
			'table_rating' => 'music_song_rating'
		);
	}	
	
	public function getBlockDetailsSong()
	{
		return array(
			'title' => Phpfox::getPhrase('music.songs')
		);
	}
	
	public function getBlockDetailsProfile()
	{
		return array(
			'title' => Phpfox::getPhrase('music.favorite_songs')
		);
	}	

	public function hideBlockSong($sType)
	{
		return array(
			'table' => 'user_design_order'
		);		
	}		
	
	public function hideBlockProfile($sType)
	{
		return array(
			'table' => 'user_design_order'
		);		
	}		
	
	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		// delete albums (it runs a delete on the songs as well)
		$aAlbums = $this->database()
			->select('album_id')
			->from(Phpfox::getT('music_album'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		foreach ($aAlbums as $aAlbum)
		{
			Phpfox::getService('music.album.process')->delete($aAlbum['album_id']);
		}
		
		// delete songs
		$aSongs = $this->database()
			->select('song_id')
			->from(Phpfox::getT('music_song'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aSongs as $aSong)
		{
			Phpfox::getService('music.process')->delete($aSong['song_id']);
		}
		

		// delete genre_user
		$this->database()->delete(Phpfox::getT('music_genre_user'), 'user_id = ' . (int)$iUser);
		$this->database()->delete(Phpfox::getT('music_user'), 'user_id = ' . (int)$iUser);
		$this->database()->delete(Phpfox::getT('music_user_value'), 'user_id = ' . (int)$iUser);
	}
	
	public function getNotificationFeedSongApproved($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('music.your_song_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
			'link' => Phpfox::getLib('url')->makeUrl('music.browse.song', array('redirect' => $aRow['item_id']))
		);		
	}
	
	public function getNotificationFeedSong_Album($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('music.a_href_user_link_full_name_a_likes_your_a_href_link_music_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('music', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('music', array('redirect' => $aRow['item_id']))			
		);			
	}

	public function getItemView()
	{
		if (Phpfox::getLib('request')->get('req3') != '')
		{
			return true;
		}
	}

	public function pendingApproval()
	{
		return array(
			'phrase' => Phpfox::getPhrase('music.music'),
			'value' => Phpfox::getService('music')->getPendingTotal(),
			'link' => Phpfox::getLib('url')->makeUrl('music', array('view' => 'pending'))
		);
	}	
	
	public function legacyRedirect($aRequest)
	{
		if (isset($aRequest['req2']))
		{
			switch ($aRequest['req2'])
			{
				case 'stylemusic':
					if (isset($aRequest['genre']))
					{				
						$aItem = Phpfox::getService('core')->getLegacyUrl(array(
							'url_field' => 'name_url',
								'table' => 'music_genre',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['genre'],
								'user_id' => false
							)
						);

						if ($aItem !== false)
						{
							return array('music', $aItem['name_url']);
						}											
					}				
					break;	
				case 'album':
					if (isset($aRequest['id']))
					{				
						$aItem = Phpfox::getService('core')->getLegacyUrl(array(
							'url_field' => 'name_url',
								'table' => 'music_album',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['id']
							)
						);
						
						if ($aItem !== false)
						{
							return array($aItem['user_name'], array('music', $aItem['name_url']));
						}											
					}					
					break;		
			}
		}		
		
		return 'music';	
	}
	
	public function getDashboardLinks()
	{
		if (!Phpfox::getUserParam('music.can_upload_music_public'))
		{
			return false;
		}
		
		return array(
			'submit' => array(
				'phrase' => Phpfox::getPhrase('music.upload_a_song'),
				'link' => 'music.upload',
				'image' => 'module/music_add.png'
			),
			'edit' => array(
				'phrase' => Phpfox::getPhrase('music.manage_songs'),
				'link' => 'music.browse.song.view_my',
				'image' => 'module/music_edit.png'
			)
		);
	}	
	
	public function reparserList()
	{
		return array(
			'name' => Phpfox::getPhrase('music.music_album_text'),
			'table' => 'music_album_text',
			'original' => 'text',
			'parsed' => 'text_parsed',
			'item_field' => 'album_id'
		);
	}

	public function getNewsFeedSong($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('music.service_callback_getnewsfeedsong_start')){eval($sPlugin);}
		$aRow['text'] = Phpfox::getPhrase('music.full_name_uploaded_a_new_song', array(
				'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'profile_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
				'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
				'link' => $aRow['link']
			)
		);
		$aRow['icon'] = 'module/music.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}
	
	public function getNewsFeedSong_Album($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('music.song_album_service_callback_getnewsfeed_start')){eval($sPlugin);}
		$aContent = unserialize($aRow['content']);

		$aRow['text'] = Phpfox::getPhrase('music.full_name_uploaded_a_new_song_to_the_album', array(
				'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'profile_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
				'title' => Phpfox::getService('feed')->shortenTitle($aContent['title']),
				'album_title' => Phpfox::getService('feed')->shortenTitle($aContent['album']['name']),
				'album_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name'], array('music', $aContent['album']['name_url'])),
				'link' => $aRow['link']
			)
		);
		$aRow['icon'] = 'module/music.png';
		$aRow['enable_like'] = true;

		return $aRow;
	}
	
	public function getFeedRedirectSong_Feedlike($iId, $iChild)
	{
		return $this->getFeedRedirectSong($iChild);
	}
	
	public function getFeedRedirectSong_Album_FeedLike($iId, $iChild)
	{		
		return $this->getFeedRedirectSong($iChild);
	}
	
	public function getFeedRedirectSong_Album($iId)
	{
		return $this->getFeedRedirectSong($iId);
	}
	
	public function verifyFavoriteSong($iItemId)
	{
		return true;
	}
	
	public function verifyFavoriteAlbum($iItemId)
	{
		return true;
	}
	
	public function getFavoriteSong($aFavorites)
	{
		$oServiceMusicBrowse = Phpfox::getService('music.song.browse');	
		$oServiceMusicBrowse->condition('m.song_id IN(' . implode(',', $aFavorites) . ') AND m.view_id = 0')
			->execute();	
			
		$aSongs = $oServiceMusicBrowse->get();

		foreach ($aSongs as $iKey => $aSong)
		{
			$aSongs[$iKey]['link'] = Phpfox::getLib('url')->makeUrl($aSong['user_name'], array(
					'music',
					(!empty($aSong['album_url']) ? $aSong['album_url'] : 'view'),
					$aSong['title_url']
				)
			);						
		}
		
		return array(
			'title' => Phpfox::getPhrase('music.songs'),
			'items' => $aSongs
		);			
	}
	
	public function getFavoriteAlbum($aFavorites)
	{
		$oServiceMusicBrowse = Phpfox::getService('music.album.browse');	
		$oServiceMusicBrowse->condition('m.album_id IN(' . implode(',', $aFavorites) . ') AND m.view_id = 0')
			->execute();	
			
		$aAlbums = $oServiceMusicBrowse->get();		

		foreach ($aAlbums as $iKey => $aAlbum)
		{
			$aAlbums[$iKey]['title'] = $aAlbum['name'];
			$aAlbums[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aAlbum['server_id'],
					'path' => 'music.url_image',
					'file' => $aAlbum['image_path'],
					'suffix' => '_120',
					'max_width' => 75,
					'max_height' => 75
				)
			);				
			$aAlbums[$iKey]['link'] = Phpfox::getLib('url')->makeUrl($aAlbum['user_name'], array(
					'music',
					$aAlbum['name_url']					
				)
			);				
		}
		
		return array(
			'title' => Phpfox::getPhrase('music.music_albums'),
			'items' => $aAlbums
		);			
	}
	
	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('music.songs'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('music_song'))
				->where('view_id = 0 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}

	/**
	  * @param int $iId video_id
	  * @return array in the format:
	     * array(
	     *	'title' => 'item title',		    <-- required
	     *  'link'  => 'makeUrl()'ed link',		    <-- required
	     *  'paypal_msg' => 'message for paypal'	    <-- required
	     *  'item_id' => int			    <-- required
	     *  'user_id;   => owner's user id		    <-- required
	     *	'error' => 'phrase if item doesnt exit'	    <-- optional
	     *	'extra' => 'description'		    <-- optional
	     *	'image' => 'path to an image',		    <-- optional
	     *	'image_dir' => 'photo.url_photo|...	    <-- optional (required if image)
	     *	'server_id' => value from DB		    <-- optional (required if image)
	     * )
	    */
	public function getToSponsorAlbumInfo($iId)
	{
	    $aAlbum = $this->database()->select('ma.name, ma.image_path as image, ma.server_id, ma.album_id, album_id as item_id, ma.user_id')
		    ->from(Phpfox::getT('music_album'), 'ma')
		    ->where('ma.album_id = ' . (int)$iId)
		    ->execute('getSlaveRow');
		    
	    if (empty($aAlbum))
	    {
			return array('error' => Phpfox::getPhrase('music.sponsor_error_album_not_found'));
	    }
	    
	    $aAlbum['title'] = Phpfox::getPhrase('music.album_sponsor_title', array('sAlbumTitle' => $aAlbum['name']));
	    $aAlbum['paypal_msg'] = Phpfox::getPhrase('music.album_sponsor_paypal_message', array('sAlbumTitle' => $aAlbum['name']));
	    //$aAlbum['link'] = Phpfox::getLib('url')->makeUrl('profile.music.'.$aAlbum['name_url']);
		$aAlbum['link'] = Phpfox::permalink('music', $aAlbum['item_id'], $aAlbum['title']);
	    $aAlbum['image_dir'] = 'music.url_image';
	    $aAlbum['image'] = sprintf($aAlbum['image'],'_200');
	    
	    return $aAlbum;
	}

	public function getToSponsorSongInfo($iId)
	{
	    $aSong = $this->database()->select('ms.user_id, ms.title, ms.song_id as item_id, ma.name, ma.image_path as image, ma.server_id, ma.album_id')
		    ->from(Phpfox::getT('music_song'), 'ms')
		    ->leftjoin(Phpfox::getT('music_album'), 'ma', 'ms.album_id = ma.album_id')
		    ->where('ms.song_id = ' . (int)$iId)
		    ->execute('getSlaveRow');

	    if (empty($aSong))
	    {
			return array('error' => Phpfox::getPhrase('music.sponsor_error_song_not_found'));
	    }
	    
	    $aSong['title'] = Phpfox::getPhrase('music.song_sponsor_title', array('sSongTitle' => $aSong['title']));
	    $aSong['paypal_msg'] = Phpfox::getPhrase('music.song_sponsor_paypal_message', array('sSongTitle' => $aSong['title']));
	    //$aSong['link'] = (isset($aSong['name_url']) && !empty($aSong['name_url'])) ? Phpfox::getLib('url')->makeUrl('profile.music.'.$aSong['name_url'] . '.' . $aSong['title_url']) : Phpfox::getLib('url')->makeUrl('profile.music.view.'.$aSong['title_url']);
		$aSong['link'] = Phpfox::permalink('music', $aSong['item_id'], $aSong['title']);
	    
	    return $aSong;
	}

	public function updateCounterList()
	{
		$aList = array();		
		
		$aList[] = array(
			'name' => Phpfox::getPhrase('music.music_album_track_count'),
			'id' => 'music-album'			
		);			

		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('music_album'))
			->execute('getSlaveField');
			
		$aRows = $this->database()->select('g.album_id, COUNT(gi.song_id) AS total_items')
			->from(Phpfox::getT('music_album'), 'g')
			->leftJoin(Phpfox::getT('music_song'), 'gi', 'gi.album_id = g.album_id')
			->group('g.album_id')
			->limit($iPage, $iPageLimit, $iCnt)
			->execute('getSlaveRows');
			
		foreach ($aRows as $aRow)
		{
			$this->database()->update(Phpfox::getT('music_album'), array('total_track' => $aRow['total_items']), 'album_id = ' . (int) $aRow['album_id']);
		}
			
		return $iCnt;
	}

	public function getNewsFeedSong_Album_Feedlike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_their_own_a_href_link_song_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_song_a', array(
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
	
	public function getNewsFeedSong_FeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_their_own_a_href_link_song_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_song_a', array(
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
	
	public function getNotificationFeedSong_Album_NotifyLike($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_your_a_href_link_song_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('music.browse.song', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('music.browse.song', array('redirect' => $aRow['item_id']))			
		);			
	}

	public function sendLikeEmailSong_Album($iItemId, $aFeed = array())
	{		
		if (isset($aFeed['user_name']) && $aFeed['user_name'] != '' && isset($aFeed['feed_id']) && $aFeed['feed_id'] != '')
		{
			return Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_your_a_href_link_song_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl($aFeed['user_name'], array('feed' => $aFeed['feed_id'])) . '#feed'
				)
			);
		}
		return Phpfox::getPhrase('music.a_href_user_link_full_name_a_liked_your_a_href_link_song_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('music.browse.song', array('redirect' => $iItemId))
				)
			);		
	}	
	
	public function getNotificationFeedSong_NotifyLike($aRow)
	{
		return $this->getNotificationFeedSong_Album_NotifyLike($aRow);	
	}
	
	public function sendLikeEmailSong($iItemId, $aFeed)
	{
		return $this->sendLikeEmailSong_Album($iItemId, $aFeed);
	}	
	
	public function getRedirectCommentSong($iId)
	{
		return $this->getFeedRedirectSong($iId);
	}	

	public function getRedirectCommentAlbum($iId)
	{
		return $this->getFeedRedirectAlbum($iId);
	}

	public function getSqlTitleField()
	{
		return array(
			array(
				'table' => 'music_album',
				'field' => 'name'
			),
			array(
				'table' => 'music_genre',
				'field' => 'name'
			),
			array(
				'table' => 'music_song',
				'field' => 'title'
			),			
		);
	}

	public function getActivityFeedSong($aItem, $bIsAlbum = false)
	{				
		if ($bIsAlbum)
		{
			$this->database()->select('ma.name AS album_name, ma.album_id, u.gender, ')
				->leftJoin(Phpfox::getT('music_album'), 'ma', 'ma.album_id = ms.album_id')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ma.user_id');
		}
		
		$this->database()->select('mp.play_id AS is_on_profile, ')->leftJoin(Phpfox::getT('music_profile'), 'mp', 'mp.song_id = ms.song_id AND mp.user_id = ' . Phpfox::getUserId());
		
		$aRow = $this->database()->select('ms.song_id, ms.title, ms.description, ms.total_play, ms.privacy, ms.time_stamp, ms.total_comment, ms.total_like, ms.user_id, l.like_id AS is_liked')
			->from(Phpfox::getT('music_song'), 'ms')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'music_song\' AND l.item_id = ms.song_id AND l.user_id = ' . Phpfox::getUserId())	
			->where('ms.song_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');		
			
		if (!isset($aRow['song_id']))
		{
			return false;
		}
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'music.view_browse_music'))
		{
			return false;
		}			
		
		if ($bIsAlbum && empty($aRow['album_name']))
		{
			$bIsAlbum = false;	
		}
		
		$iTitleLength = (Phpfox::isModule('notification') ? (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ) : 50);
		$aReturn = array(
			'feed_title' => Phpfox::getLib('parse.output')->shorten($aRow['title'], $iTitleLength, '...'),
			'feed_status' => $aRow['description'],
			'feed_info' => ($bIsAlbum ? Phpfox::getPhrase('feed.shared_a_song_from_gender_album_a_href_album_link_album_name_a', array('gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'album_link' => Phpfox::getLib('url')->permalink('music.album', $aRow['album_id'], $aRow['album_name']), 'album_name' => Phpfox::getLib('parse.output')->shorten($aRow['album_name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...'))) : Phpfox::getPhrase('feed.shared_a_song')),
			'feed_link' => Phpfox::permalink('music', $aRow['song_id'], $aRow['title']),
			'feed_content' => ($aRow['total_play'] > 1 ? $aRow['total_play'] . ' plays' : '1 play'),
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/music.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'music_song',
			'like_type_id' => 'music_song',
			'feed_custom_width' => '38px',
			'song' => array(
				'privacy' => $aRow['privacy'],
				'song_id' => $aRow['song_id'],
				'user_id' => $aRow['user_id'],
				'is_on_profile' => $aRow['is_on_profile']
			)
		);

		$aReturn['feed_image'] = Phpfox::getLib('image.helper')->display(array(
				'theme' => 'misc/play_button.png',
				// 'return_url' => true		
			)
		);
		
		$aReturn['feed_image_onclick'] = '$.ajaxCall(\'music.playInFeed\', \'id=' . $aRow['song_id'] . '&amp;feed_id=' . $aItem['feed_id'] . '\', \'GET\'); return false;';
		$aReturn['feed_image_onclick_no_image'] = true;
		
		return $aReturn;
	}	
	
	public function checkFeedShareLink()
	{
		if (Phpfox::isMobile())
		{
			return false;
		}
		
		if (!Phpfox::getUserParam('music.can_upload_music_public'))
		{
			return false;
		}
	}
	
	public function addLikeSong($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('song_id, title, user_id')
			->from(Phpfox::getT('music_song'))
			->where('song_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
			
		if (!isset($aRow['song_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'music_song\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'music_song', 'song_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('music', $aRow['song_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('music.full_name_liked_your_song_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(Phpfox::getPhrase('music.full_name_liked_your_song_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			Phpfox::getService('notification.process')->add('music_song_like', $aRow['song_id'], $aRow['user_id']);				
		}		
	}
	
	public function deleteLikeSong($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'music_song\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'music_song', 'song_id = ' . (int) $iItemId);
	}	
	
	public function getNotificationSong_Like($aNotification)
	{
		$aRow = $this->database()->select('ms.song_id, ms.title, ms.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('music_song'), 'ms')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
			->where('ms.song_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['song_id']))
		{
			return false;
		}			
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('music', $aRow['song_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}		
	
	public function getCommentNotificationSong($aNotification)
	{
		$aRow = $this->database()->select('l.song_id, l.title, u.user_id, u.gender, u.user_name, u.full_name')	
			->from(Phpfox::getT('music_song'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.song_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('music', $aRow['song_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}	
	
	public function addLikeAlbum($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('album_id, name, user_id')
			->from(Phpfox::getT('music_album'))
			->where('album_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
		
		if (!isset($aRow['album_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'music_album\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'music_album', 'album_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('music.album', $aRow['album_id'], $aRow['name']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('music.full_name_liked_your_album_name', array('full_name' => Phpfox::getUserBy('full_name'), 'name' => $aRow['name'])))
				->message(Phpfox::getPhrase('music.full_name_liked_your_album_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'name' => $aRow['name'])))
				->send();
					
			Phpfox::getService('notification.process')->add('music_album_like', $aRow['album_id'], $aRow['user_id']);				
		}		
	}
	
	public function deleteLikeAlbum($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'music_album\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'music_album', 'album_id = ' . (int) $iItemId);
	}	
	
	public function getNotificationAlbum_Like($aNotification)
	{
		$aRow = $this->database()->select('ms.album_id, ms.name, ms.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('music_album'), 'ms')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ms.user_id')
			->where('ms.album_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['album_id']))
		{
			return false;
		}			
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('music.album', $aRow['album_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function getCommentNotificationAlbum($aNotification)
	{
		$aRow = $this->database()->select('l.album_id, l.name, u.user_id, u.gender, u.user_name, u.full_name')	
			->from(Phpfox::getT('music_album'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.album_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> album "' . Phpfox::getLib('parse.output')->shorten($aRow['name'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('music.album', $aRow['album_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}		
	
	public function getActivityFeedAlbum($aItem)
	{
		return $this->getActivityFeedSong($aItem, true);
	}
	
	public function getNotificationSongApproved($aNotification)
	{
		$aRow = $this->database()->select('b.song_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('music_song'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.song_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	

		if (!isset($aRow['song_id']))
		{
			return false;
		}
		
		$sPhrase = 'Your song "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength ), '...') . '" has been approved.';
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('music', $aRow['song_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
			'no_profile_image' => true
		);			
	}
	
	public function getAjaxProfileController()
	{
		return 'music.index';
	}	
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_song']))
			{
				return false;
			}

			if (isset($aUser['total_song']) && (int) $aUser['total_song'] === 0)
			{
				return false;
			}	
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('profile.music'),
			'url' => 'profile.music',
			'total' => (int) (isset($aUser['total_song']) ? $aUser['total_song'] : 0),
			'icon' => 'feed/music.png'
		);	
		
		return $aMenus;
	}

	public function getTotalItemCountSong($iUserId)
	{
		return array(
			'field' => 'total_song',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('music_song'))->where('view_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
		);	
	}	
		
	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.song_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'music\' AS item_type_id, \'\' AS item_photo, \'\' AS item_photo_server')
			->from(Phpfox::getT('music_song'), 'item')
			->where('item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch))
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('music.song', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = 'Song';
		
		return $aInfo;
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => 'Songs'
		);
	}	
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'music.default_privacy_setting' => array(
				'phrase' => Phpfox::getPhrase('user.songs')								
			)
		);
	}	
	
	public function getPageMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'music.view_browse_music'))
		{
			return null;
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('pages.music'),
			'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'music/',
			'icon' => 'feed/music.png',
			'landing' => 'music'
		);
		
		return $aMenus;
	}	
	
	public function canViewPageSection($iPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($iPage, 'music.view_browse_music'))
		{
			return false;
		}
		
		return true;
	}	
	
	public function getPageSubMenu($aPage)
	{
		if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'music.share_music'))
		{
			return null;
		}		
		
		return array(
			array(
				'phrase' => 'Upload a Song',
				'url' => Phpfox::getLib('url')->makeUrl('music.upload', array('module' => 'pages', 'item' => $aPage['page_id']))
			)
		);
	}	
	
	public function getPagePerms()
	{
		$aPerms = array();
		
		$aPerms['music.share_music'] = 'Who can share songs?';
		$aPerms['music.view_browse_music'] = 'Who can view/browse songs?';
		
		return $aPerms;
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
		if ($sPlugin = Phpfox_Plugin::get('music.service_callback__call'))
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