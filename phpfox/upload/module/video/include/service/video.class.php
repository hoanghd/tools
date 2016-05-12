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
 * @version 		$Id: video.class.php 3348 2011-10-25 07:27:46Z Miguel_Espinoza $
 */
class Video_Service_Video extends Phpfox_Service
{
	private $_aExt = array(
		'mpg' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'wmv' => 'video/x-ms-wmv',
		'avi' => 'video/avi',
		'mov' => 'video/quicktime',
		'flv' => 'video/x-flv'
		// 'mp4' => 'video/mp4',
		// '3gp' => 'video/3gpp'
	);

	private $_aCallback = false;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('video');
	}

	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;

		return $this;
	}

	public function makeUrl($sUser, $sUrl, $aCallback = null)
	{
		return Phpfox::getLib('url')->makeUrl($sUser, array('video', $sUrl));
	}

	public function getFileExt($bDisplay = false)
	{
		if ($bDisplay === true)
		{
			$sExts = '';
			$iCnt = 0;
			foreach (array_keys($this->_aExt) as $sExt)
			{
				$iCnt++;
				if ($iCnt == count($this->_aExt))
				{
					$sExts .= ' or ';
				}
				elseif ($iCnt != 1)
				{
					$sExts .= ', ';
				}
				$sExts .= strtoupper($sExt);
			}

			return $sExts;
		}

		return array_keys($this->_aExt);
	}

	public function getVideo($sVideo, $bUseId = false)
	{
		$bUseId = true;
		
		if (Phpfox::isModule('track'))
		{
			$this->database()->select("video_track.item_id AS video_is_viewed, ")->leftJoin(Phpfox::getT('video_track'), 'video_track', 'video_track.item_id = v.video_id AND video_track.ip_address = \'' . $this->database()->escape(Phpfox::getIp(true)) . '\'');
		}
		
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = v.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}		
		
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'video\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId());
		}		

		$aVideo = $this->database()->select('v.*, ' . (Phpfox::getParam('core.allow_html') ? 'vt.text_parsed' : 'vt.text') . ' AS text, u.user_name, rate_id AS has_rated, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
			->leftJoin(Phpfox::getT('video_rating'), 'vr', 'vr.item_id = v.video_id AND vr.user_id = ' . Phpfox::getUserId())
			->where(($bUseId ? 'v.video_id = ' . (int) $sVideo : 'v.module_id = \'' . ($this->_aCallback !== false ? $this->_aCallback['module'] : 'video') . '\' AND v.item_id = ' . ($this->_aCallback !== false ? (int) $this->_aCallback['item'] : 0) . ' AND v.title_url = \'' . $this->database()->escape($sVideo) . '\''))
			->execute('getSlaveRow');

		if (!isset($aVideo['video_id']))
		{
			return false;
		}

		if ($aVideo['view_id'] != '0')
		{
			if ($aVideo['view_id'] == '2' && ($aVideo['user_id'] == Phpfox::getUserId() || Phpfox::getUserParam('video.can_approve_videos')))
			{

			}
			else
			{
				return false;
			}
		}

		$aVideo['breadcrumb'] = Phpfox::getService('video.category')->getCategoriesById($aVideo['video_id']);
		$aVideo['bookmark'] = ($this->_aCallback !== false ? Phpfox::getLib('url')->makeUrl($this->_aCallback['url'][0], array_merge($this->_aCallback['url'][1], array('video', $aVideo['title']))) : Phpfox::permalink('video', $aVideo['video_id'], $aVideo['title']));
		$aVideo['embed'] = '';

		if ($aVideo['is_stream'])
		{
			$aEmbedVideo = $this->database()->select('video_url, embed_code')
				->from(Phpfox::getT('video_embed'))
				->where('video_id = ' . $aVideo['video_id'])
				->execute('getslaveRow');

			if (empty($aEmbedVideo['embed_code']))
			{
				if (!Phpfox::getService('video.grab')->get($aEmbedVideo['video_url']))
				{
					return Phpfox_Error::display(Phpfox::getPhrase('video.not_a_valid_video_to_display'));
				}
				$aEmbedVideo['embed_code'] = Phpfox::getService('video.grab')->embed();

				$this->database()->update(Phpfox::getT('video_embed'), array('embed_code' => $aEmbedVideo['embed_code']), 'video_id = ' . $aVideo['video_id']);
			}

			$aVideo['embed_code'] = $aEmbedVideo['embed_code'];
			if (preg_match('/youtube/i', $aEmbedVideo['video_url']) || preg_match('/youtu\.be/i', $aEmbedVideo['video_url']))
			{
				preg_match('/value="http:\/\/(.*?)"/i', $aVideo['embed_code'], $aMatches);
				if (isset($aMatches[1]))
				{
					$sTempUrl = trim($aMatches[1]);
					$aUrlFind = array(
						'&amp;fs=1',
						'&amp;fs=0',
						'&fs=1',
						'&fs=0',

						'&amp;rel=1',
						'&amp;rel=0',
						'&rel=1',
						'&rel=0',

						'&amp;autoplay=1',
						'&amp;autoplay=0',
						'&autoplay=1',
						'&autoplay=0',
					);
					$sNewTempUrl = str_replace($aUrlFind, '', $sTempUrl) . (Phpfox::getParam('video.embed_auto_play') ? '&amp;autoplay=1' : '') . (Phpfox::getParam('video.full_screen_with_youtube') ? '&amp;fs=1' : '') . (Phpfox::getParam('video.disable_youtube_related_videos') ? '&amp;rel=0' : '');
					$aVideo['embed_code'] = str_replace($sTempUrl, $sNewTempUrl, $aVideo['embed_code']);
				}
			}
			
			$aVideo['embed_code'] = preg_replace('/width=\"(.*?)\"/i', 'width="640"', $aVideo['embed_code']);
			$aVideo['embed_code'] = preg_replace('/height=\"(.*?)\"/i', 'height="390"', $aVideo['embed_code']);
			$aVideo['embed_code'] = preg_replace_callback('/<object(.*?)>(.*?)<\/object>/is', array($this, '_embedWmode'), $aVideo['embed_code']);

			$aVideo['embed'] = htmlspecialchars($aEmbedVideo['embed_code']);
		}

		if ($this->_aCallback !== false && isset($this->_aCallback['url_home']) && isset($aVideo['breadcrumb']) && is_array($aVideo['breadcrumb']) && count($aVideo['breadcrumb']))
		{
			$sHomeUrl = '/' . $this->_aCallback['url_home'][0] . '/' . implode('/', $this->_aCallback['url_home'][1]) . '/video/';

			foreach ($aVideo['breadcrumb'] as $iKey => $aCategory)
			{
				$aVideo['breadcrumb'][$iKey][1] = preg_replace('/^http:\/\/(.*?)\/video\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory[1]);
			}
		}

		if (Phpfox::isModule('tag'))
		{
			$aTags = Phpfox::getService('tag')->getTagsById('video' . (empty($aVideo['module_id']) ? '' : '_' . $aVideo['module_id']), $aVideo['video_id']);
			if (isset($aTags[$aVideo['video_id']]))
			{
				$aVideo['tag_list'] = $aTags[$aVideo['video_id']];
			}
		}
		
		$aVideo['total_user_videos'] = $this->database()->select('COUNT(*)')->from($this->_sTable)->where('in_process = 0 AND view_id = 0 AND item_id = 0 AND user_id = ' . (int) $aVideo['user_id'])->execute('getSlaveField');
		if (!isset($aVideo['is_friend']))
		{
			$aVideo['is_friend'] = 1;
		}
		(($sPlugin = Phpfox_Plugin::get('video.service_video_getvideo')) ? eval($sPlugin) : null);
		if (Phpfox::isMobile())
		{
			$aVideo['embed_code'] = preg_replace('/width="([0-9]+)"/', 'width="285"', $aVideo['embed_code']);
			$aVideo['embed_code'] = preg_replace('/height="([0-9]+)"/', 'height="153"', $aVideo['embed_code']);
		}
		return $aVideo;
	}

	public function getForEdit($iId, $bForce = false)
	{
		$aVideo = $this->database()->select('v.*, vt.text, u.user_name')
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
			->where('v.video_id = ' . (int) $iId)
			->execute('getSlaveRow');

		if (isset($aVideo['video_id']))
		{
			if ((($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('video.can_edit_own_video')) || Phpfox::getUserParam('video.can_edit_other_video')) || $bForce === true)
			{
				$aVideo['categories'] = Phpfox::getService('video.category')->getCategoryIds($aVideo['video_id']);
				$aVideo['video_url'] = Phpfox::permalink('video', $aVideo['video_id'], $aVideo['title']);

				if (Phpfox::isModule('tag'))
				{
					$aTags = Phpfox::getService('tag')->getTagsById('video' . (empty($aVideo['module_id']) ? '' : '_' . $aVideo['module_id']), $aVideo['video_id']);
					if (isset($aTags[$aVideo['video_id']]))
					{
						$aVideo['tag_list'] = '';
						foreach ($aTags[$aVideo['video_id']] as $aTag)
						{
							$aVideo['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
						}
						$aVideo['tag_list'] = trim(trim($aVideo['tag_list'], ','));
					}
				}

				return $aVideo;
			}
		}

		return Phpfox_Error::set(Phpfox::getPhrase('video.unable_to_find_the_video_you_plan_to_edit'));
	}

	public function getForProfileBlock($iUserId, $iLimit = 6)
	{
		$oServiceVideoBrowse = Phpfox::getService('video.browse');

		$oServiceVideoBrowse->condition('m.in_process = 0 AND m.view_id = 0 AND m.user_id = ' . (int) $iUserId)
			->size($iLimit)
			->execute();

		return $oServiceVideoBrowse;
	}

	public function getForParentBlock($sModule, $iItem, &$aVideoParent, $iLimit = 6)
	{
		$oServiceVideoBrowse = Phpfox::getService('video.browse');

		$oServiceVideoBrowse->condition('m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'' . $this->database()->escape($sModule) . '\' AND m.item_id = ' . (int) $iItem)
			->callback($aVideoParent)
			->size($iLimit)
			->execute();

		return $oServiceVideoBrowse;
	}

	public function getNew($iLimit = 3)
	{
		$oServiceVideoBrowse = Phpfox::getService('video.browse');

		$oServiceVideoBrowse->condition('m.in_process = 0 AND m.view_id = 0 AND module_id = "video" ')
			->size($iLimit)
			->order('m.time_stamp DESC')
			->execute();

		return $oServiceVideoBrowse;
	}

	public function verify($sIds, $bUseVideoImage = false)
	{
		$aVideos = $this->database()->select('v.*, ve.embed_code, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->leftJoin(Phpfox::getT('video_embed'), 've', 've.video_id = v.video_id')
			->where('v.video_id IN(' . $sIds . ') AND v.user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRows');

		$aCache = array();
		foreach ($aVideos as $aVideo)
		{
			(($sPlugin = Phpfox_Plugin::get('video.service_video_verify')) ? eval($sPlugin) : null);

			if ($bUseVideoImage === true)
			{
				$sImage = Phpfox::getLib('image.helper')->display(array(
							'server_id' => $aVideo['image_server_id'],
							'path' => 'video.url_image',
							'file' => $aVideo['image_path'],
							'suffix' => '_120',
							'max_width' => 120,
							'max_height' => 120
					)
				);

				$aCache[$aVideo['video_id']] = '<a href="' . $this->makeUrl($aVideo['user_name'], $aVideo['title_url']) . '" title="' . Phpfox::getLib('parse.output')->clean($aVideo['title']) . '">' . $sImage . '</a>';
			}
			else
			{
				$aCache[$aVideo['video_id']] = $aVideo['embed_code'];
			}
		}

		return $aCache;
	}

	public function requirementCheck(&$aVals)
	{
		if (!isset($aVals['ffmpeg_path']))
		{
			return Phpfox_Error::set('Must set the path to FFMPEG before enabling uploading of videos.');
		}
		$aOutput= '';
		exec($aVals['ffmpeg_path'] . ' 2>&1', $aOutput);
	
		$bPass = false;
		foreach ($aOutput as $sOutput)
		{
			if (preg_match("/ffmpeg version/i", $sOutput))
			{
				$bPass = true;
				break;
			}
		}
		
		if (!$bPass)
		{
			return Phpfox_Error::set(implode('<br />', $aOutput));	
		}

		exec($aVals['mencoder_path'] . ' 2>&1', $aOutput);		
		
		foreach ($aOutput as $sOutput)
		{
			if (preg_match("/mplayer Team/i", $sOutput))
			{
				$bPass = true;
				break;
			}
		}		

		if (!$bPass)
		{
			return Phpfox_Error::set(implde('<br />', $aOutput));	
		}

		return true;
	}

	public function getSpotlight()
	{
		$sCacheId = $this->cache()->set('video_spotlight');

		// if (!($aVideos = $this->cache()->get($sCacheId)))
		{
			$aVideos = $this->database()->select('v.*, ve.embed_code, ' . Phpfox::getUserField())
				->from($this->_sTable, 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->leftJoin(Phpfox::getT('video_embed'), 've', 've.video_id = v.video_id')
				->where('v.in_process = 0 AND v.view_id = 0 AND v.is_spotlight = 1')
				->execute('getRows');

			foreach ($aVideos as $iKey => $aVideo)
			{
				if ($aVideo['is_stream'])
				{
					$aVideo['embed_code'] = preg_replace('/width="(.*?)"/i', 'class="video_embed"', $aVideo['embed_code']);
					$aVideo['embed_code'] = preg_replace('/height="(.*?)"/i', '', $aVideo['embed_code']);
					$aVideos[$iKey]['link'] = Phpfox::permalink('video', $aVideo['video_id'], $aVideo['title']);

					$aVideos[$iKey]['embed_code'] = $aVideo['embed_code'];
				}
			}

			$this->cache()->save($sCacheId, $aVideos);
		}

		return (isset($aVideos[rand(0, (count($aVideos) - 1))]) ? $aVideos[rand(0, (count($aVideos) - 1))] : false);
	}
	
	public function getRandomSponsored()
	{
		$sCacheId = $this->cache()->set('video_sponsored');
		if (!($aVideos = $this->cache()->get($sCacheId)))
		{
			// what to do with total_view?
			$aVideos = $this->database()->select('s.*, v.*, ve.embed_code, ' . Phpfox::getUserField())
				->from($this->_sTable, 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->join(Phpfox::getT('ad_sponsor'),'s','s.item_id = v.video_id')
				->leftJoin(Phpfox::getT('video_embed'), 've', 've.video_id = v.video_id')
				->where('v.in_process = 0 AND v.view_id = 0 AND v.is_sponsor = 1 AND s.module_id = "video"')
				->execute('getRows');

			foreach ($aVideos as $iKey => $aVideo)
			{
				if ($aVideo['is_stream'])
				{
					$aVideo['embed_code'] = preg_replace('/width="(.*?)"/i', 'width="248"', $aVideo['embed_code']);
					$aVideo['embed_code'] = preg_replace('/height="(.*?)"/i', 'width="183"', $aVideo['embed_code']);

					$aVideos[$iKey]['embed_code'] = $aVideo['embed_code'];
				}
			}

			$this->cache()->save($sCacheId, $aVideos);
		}
		
		$aVideos = Phpfox::getService('ad')->filterSponsor($aVideos);
				
		return (isset($aVideos[rand(0, (count($aVideos) - 1))]) ? $aVideos[rand(0, (count($aVideos) - 1))] : false);

	}

	public function getUserVideos($iUserId)
	{
		$this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.item_id = 0 AND m.privacy IN(%PRIVACY%) AND m.user_id = ' . (int) $iUserId);
		$this->search()->set(array('prepare' => false, 'type' => 'video', 'search_tool' => array('show' => array(Phpfox::getParam('video.total_my_videos')), 'sort' => array('latest' => array('m.time_stamp', 'Latest')))));
		
		$aBrowseParams = array(
			'module_id' => 'video',
			'alias' => 'm',
			'field' => 'video_id',
			'table' => Phpfox::getT('video'),
			'hide_view' => array('pending', 'my')				
		);	

		$this->search()->browse()->params($aBrowseParams)->execute();	
		
		return array($this->search()->browse()->getCount(), $this->search()->browse()->getRows());
	}
	
	public function getFeatured()
	{
		static $aFeatured = null;
		static $iTotal = null;
		
		if ($aFeatured !== null)
		{
			return array($iTotal, $aFeatured);
		}
		
		$aFeatured = array();
		$sCacheId = $this->cache()->set('video_featured');		
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('video'), 'v')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
				->where('v.is_featured = 1')			
				->execute('getSlaveRows');
			
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$iTotal = 0;
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			shuffle($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				if ($iKey === 4)
				{
					break;
				}
				
				$aFeatured[] = $aRow;
			}
		}
		
		return array($iTotal, $aFeatured);
	}

	public function getRelatedVideos($iVideoId, $sTitle, $iPage = 0, $bFindSuggestions = false, $bProcess = false)
	{
		Phpfox::getLib('request')->set('page', $iPage);
				
		$oServiceVideoBrowse = Phpfox::getService('video.browse');
		
		$this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.item_id = 0 AND m.privacy IN(%PRIVACY%) ' . ($bProcess ? '' : 'AND ' . $this->database()->searchKeywords('m.title', $sTitle)));
		$this->search()->set(array('prepare' => false, 'type' => 'video', 'search_tool' => array('show' => array(Phpfox::getParam('video.total_related_videos')), 'sort' => array('latest' => array('m.time_stamp', 'Latest')))));
		
		$aBrowseParams = array(
			'module_id' => 'video',
			'alias' => 'm',
			'field' => 'video_id',
			'table' => Phpfox::getT('video'),
			'hide_view' => array('pending', 'my')				
		);	

		$this->search()->browse()->params($aBrowseParams)->execute();	
			
		$aRows = $this->search()->browse()->getRows();
		
		foreach ($aRows as $iKey => $aRow)
		{
			if ($aRow['video_id'] == $iVideoId)
			{
				unset($aRows[$iKey]);
			}	
		}
		
		if (!count($aRows) && $bFindSuggestions)
		{			
			$this->search()->clear();
			
			return $this->getRelatedVideos($iVideoId, $sTitle, 0, false, true);	
		}
			
		return array($this->search()->browse()->getCount(), $aRows);
	}

	public function getPendingTotal()
	{
		return $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('view_id = 2')
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
		if ($sPlugin = Phpfox_Plugin::get('video.service_video__call'))
		{
			return eval($sPlugin);
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
	
    private function _embedWmode($aMatches)
    {
    	return '<object ' . $aMatches[1] . '><param name="wmode" value="transparent"></param>' . str_replace('<embed ', '<embed  wmode="transparent" ', $aMatches[2]) . '</object>';
    }	
}

?>