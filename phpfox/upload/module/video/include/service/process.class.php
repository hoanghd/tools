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
 * @version 		$Id: process.class.php 3249 2011-10-07 12:39:23Z Miguel_Espinoza $
 */
class Video_Service_Process extends Phpfox_Service 
{
	private $_aCategories = array();
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('video');	
	}
	
	public function addShareVideo($aVals, $bReturnId = false)
	{		
		define('PHPFOX_FORCE_IFRAME', true);
		
		if (isset($aVals['category']) && count($aVals['category']))
		{
			foreach ($aVals['category'] as $iCategory)
			{		
				if (empty($iCategory))
				{
					continue;
				}
				
				if (!is_numeric($iCategory))
				{
					continue;
				}			
				
				$this->_aCategories[] = $iCategory;
			}
		}
		
		if (!($sEmbed = Phpfox::getService('video.grab')->embed()))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('video.unable_to_embed_this_video_due_to_privacy_settings'));
		}
		
		if ($sPlugin = Phpfox_Plugin::get('video.service_process_addsharevideo__start'))
		{
			eval($sPlugin);
		}		
		
		if (!isset($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		if (!isset($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}		
		
		$sModule = 'video';
		$iItem = 0;	
		$aCallback = null;
		if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo'))
		{
			$aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);	
			$sModule = $aCallback['module'];
			$iItem = $aCallback['item_id'];			
		}		
		
		$aSql = array(
			'is_stream' => 1,
			'view_id' => (($sModule == 'video' && Phpfox::getUserParam('video.approve_video_before_display')) ? 2 : 0),
			'module_id' => $sModule,
			'item_id' => (int) $iItem,
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
			'user_id' => Phpfox::getUserId(),				
			'time_stamp' => PHPFOX_TIME
		);		
		
		if ($sTitle = Phpfox::getService('video.grab')->title())
		{
			$bAddedTitle = true;
			$aSql['title'] = $this->preParse()->clean($sTitle, 255);
		}
		
		if ($sDuration = Phpfox::getService('video.grab')->duration())
		{
			$aSql['duration'] = $sDuration;
		}		
		
		$iId = $this->database()->insert($this->_sTable, $aSql);
		
		if (!$iId)
		{
			return false;
		}
		
		$aUpdate = array();
		
		if (Phpfox::getService('video.grab')->image($iId))
		{
			$sImageLocation = Phpfox::getLib('file')->getBuiltDir(Phpfox::getParam('video.dir_image')) . md5($iId) . '%s.jpg';
			
			$aUpdate['image_path'] = str_replace(Phpfox::getParam('video.dir_image'), '', $sImageLocation);
			$aUpdate['image_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');			
		}
		
		if (!isset($bAddedTitle))
		{
			$aUpdate['title'] = $iId;
			$sTitle = $iId;
		}
		
		if (count($aUpdate))
		{
			$this->database()->update($this->_sTable, $aUpdate, 'video_id = ' . $iId);
		}
		
		$this->database()->insert(Phpfox::getT('video_embed'), array(
				'video_id' => $iId,
				'video_url' => $aVals['url'],
				'embed_code' => $sEmbed
			)
		);
		
		if (($sDescription = Phpfox::getService('video.grab')->description()))
		{
			$this->database()->insert(Phpfox::getT('video_text'), array(
					'video_id' => $iId,
					'text' => $this->preParse()->clean($sDescription),
					'text_parsed' => $this->preParse()->prepare($sDescription)		
				)
			);
		}		
		
		if (!Phpfox::getService('video.grab')->hasImage())
		{
			$bReturnId = true;
		}
		
		if (isset($this->_aCategories) && count($this->_aCategories))
		{
			foreach ($this->_aCategories as $iCategoryId)
			{
				$this->database()->insert(Phpfox::getT('video_category_data'), array('video_id' => $iId, 'category_id' => $iCategoryId));
			}		
		}
		
		$aCallback = null;
		if ($sModule != 'video' && Phpfox::hasCallback($sModule, 'convertVideo'))
		{
			$aCallback = Phpfox::callback($sModule . '.convertVideo', array('item_id' => $iId));	
		}			
		
		((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Phpfox::getService('feed.process')->callback($aCallback)->add('video', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), ($aCallback === null ? 0 : $aVals['callback_item_id'])) : null);
			
		// Update user activity
		Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'video');
		
		if ($aVals['privacy'] == '4')
		{
			Phpfox::getService('privacy.process')->add('video', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));			
		}		
		
		if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
		{
			Phpfox::getService('tag.process')->add('video', $iId, Phpfox::getUserId(), $aVals['tag_list']);
		}			

		// Plugin call
		if ($sPlugin = Phpfox_Plugin::get('video.service_process_addsharevideo__end'))
		{
			eval($sPlugin);
		}
		
		return $iId;
	}
	
	public function process($sFileName, $sModule = null, $iItem = null)
	{
		if (empty($sFileName))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('video.select_a_video_to_upload'));
		}
		
		$sExts = implode('|', Phpfox::getService('video')->getFileExt());
		
		if (!preg_match("/^(.*?)\.({$sExts})$/i", $sFileName))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('video.not_a_valid_file_we_only_allow_sallow', array('sAllow' => implode(', ', Phpfox::getService('video')->getFileExt()))));
		}
		
		if ($sModule !== null && $iItem !== null && Phpfox::hasCallback($sModule, 'uploadVideo'))
		{
			$aCallback = Phpfox::callback($sModule . '.uploadVideo', $iItem);
			
			if ($aCallback !== false)
			{
				$sModule = $aCallback['module'];
				$iItem = $aCallback['item'];
			}
			else 
			{
				$sModule = null;
				$iItem = null;
			}
		}
		else if (defined('PHPFOX_GROUP_VIEW'))
		{
			$sModule = 'group';
		}
		$iId = $this->database()->insert($this->_sTable, array(
				'view_id' => 1,
				'module_id' => ($sModule !== null ? $sModule : 'video'),
				'item_id' => ($iItem !== null ? (int) $iItem : 0),
				'privacy' => 0,				
				'user_id' => Phpfox::getUserId(),				
				'time_stamp' => PHPFOX_TIME
			)
		);
		
		if (!$iId)
		{
			return false;
		}
		
		return $iId;
	}
	
	public function add($aVals, $aVideo = null)
	{
		if ($aVideo === null)
		{
			if (!isset($_FILES['video']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('video.select_a_video'));
			}		
			
			$aUploadVideo = Phpfox::getLib('file')->load('video', Phpfox::getService('video')->getFileExt(), Phpfox::getUserParam('video.video_file_size_limit'));		

			if ($aUploadVideo === false)
			{
				return false;
			}		
		}
		
		if (!empty($aVals['video_title']))
		{
			$aVals['title'] = $aVals['video_title'];
		}
		
		$aCallback = null;
		if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo'))
		{
			$aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);	
		}
		
		$aSql = array(
			'user_id' => Phpfox::getUserId(),
			'parent_user_id' => (isset($aVals['parent_user_id']) ? (int) $aVals['parent_user_id'] : '0'),
			'in_process' => 1,
			'view_id' => 0,
			'item_id' => ($aCallback === null ? (isset($aVals['parent_user_id']) ? (int) $aVals['parent_user_id'] : '0') : $aCallback['item_id']),
			'module_id' => ($aCallback === null ? 'video' : $aCallback['module']),
			'title' => (empty($aVals['title']) ? null : $this->preParse()->clean($aVals['title'], 255)),
			'privacy' => (int) (isset($aVals['privacy']) ? $aVals['privacy'] : 0),
			'privacy_comment' => (int) (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0),
			'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
			'file_ext' => $aVideo['ext'],
			'time_stamp' => PHPFOX_TIME
		);
		
		if (empty($aVals['title']))
		{
			$aSql['title'] = preg_replace("/^(.*?)\.(.*?)$/i", "\\1", $aVideo['name']);
		}		
				
		$iId = $this->database()->insert($this->_sTable, $aSql);
		
		if ($aVideo === null)
		{		
			$sFileName = Phpfox::getLib('file')->upload('video', Phpfox::getParam('video.dir'), $iId, true, 0644, true, false);
		}
		
		if (!empty($aVals['status_info']))
		{
			$aVals['text'] = $aVals['status_info'];
		}
		
		if ($aVideo === null)
		{
			$this->database()->update($this->_sTable, array('destination' => $sFileName, 'file_ext' => $aUploadVideo['ext']), 'video_id = ' . (int) $iId);
		}
		
		$this->database()->insert(Phpfox::getT('video_text'), array(
				'video_id' => $iId,
				'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
				'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))				
			)
		);
		
		if (isset($aVals['privacy']) && $aVals['privacy'] == '4')
		{
			Phpfox::getService('privacy.process')->add('video', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));			
		}		
		
		if (isset($aVals['category']) && count($aVals['category']))
		{
			foreach ($aVals['category'] as $iCategory)
			{		
				if (empty($iCategory))
				{
					continue;
				}
				
				if (!is_numeric($iCategory))
				{
					continue;
				}			
				
				$this->_aCategories[] = $iCategory;
			}		
			
			foreach ($this->_aCategories as $iCategoryId)
			{
				$this->database()->insert(Phpfox::getT('video_category_data'), array('video_id' => $iId, 'category_id' => $iCategoryId));
			}		
		}
		
		if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
		{
			Phpfox::getService('tag.process')->add('video', $iId, Phpfox::getUserId(), $aVals['tag_list']);
		}		
		
		// plugin call
		if ($sPlugin = Phpfox_Plugin::get('video.service_process_add__end'))
		{
			eval($sPlugin);
		}
                
		return $iId;
	}
	
	public function update($iId, $aVals)
	{		
		if (isset($aVals['category']) && count($aVals['category']))
		{
			foreach ($aVals['category'] as $iCategory)
			{		
				if (empty($iCategory))
				{
					continue;
				}
				
				if (!is_numeric($iCategory))
				{
					continue;
				}			
				
				$this->_aCategories[] = $iCategory;
			}
		}		
		
		$aVideo = $this->database()->select('v.video_id, v.privacy, v.privacy_comment, v.view_id, v.is_viewed, v.user_id, vt.video_id AS text_id, v.module_id')
			->from($this->_sTable, 'v')
			->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
			->where('v.video_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aVideo['video_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('video.unable_to_find_the_video_you_plan_to_edit'));
		}
		
		Phpfox::getService('ban')->checkAutomaticBan(isset($aVals['title']) ? $aVals['title'] : '' . isset($aVals['text']) ? $aVals['text'] : '');
		
		if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('video.can_edit_own_video')) || Phpfox::getUserParam('video.can_edit_other_video'))
		{					
			if (Phpfox::getLib('parse.format')->isEmpty($aVals['title']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('video.provide_a_title_for_this_video'));
			}		
			
			$aSql = array(
				'title' => $this->preParse()->clean($aVals['title'], 255)		
			);
			
			if (isset($aVals['privacy']))
			{
				$aSql['privacy'] = (int) $aVals['privacy'];
				$aSql['privacy_comment'] = (int) $aVals['privacy_comment'];
			}
			else
			{
				$aVals['privacy'] = $aVideo['privacy'];
				$aVals['privacy_comment'] = $aVideo['privacy_comment'];
			}
			
			if ($aVideo['is_viewed'] == '0')
			{
				$aSql['is_viewed'] = '1';				
			}
			
			if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
			{
				$aImage = Phpfox::getLib('file')->load('image', array(
						'jpg',
						'gif',
						'png'
					), (Phpfox::getUserParam('video.max_size_for_video_photos') === 0 ? null : (Phpfox::getUserParam('video.max_size_for_video_photos') / 1024))
				);
				
				if ($aImage !== false)
				{
					$iFileSizes = 0;
					
					$oImage = Phpfox::getLib('image');
			
					$sFileName = Phpfox::getLib('file')->upload('image', Phpfox::getParam('video.dir_image'), $aVideo['video_id']);
							
					$aSql['image_path'] = $sFileName;
					$aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');					
					
					$iSize = 120;			
					$oImage->createThumbnail(Phpfox::getParam('video.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('video.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);			
					$iFileSizes += filesize(Phpfox::getParam('video.dir_image') . sprintf($sFileName, '_' . $iSize));
					@unlink(Phpfox::getParam('video.dir_image') . sprintf($sFileName, ''));
					
					// Update user space usage
					Phpfox::getService('user.space')->update($aVideo['user_id'], 'video', $iFileSizes);					
				}		
				else 
				{
					return false;
				}
			}		
			
			$this->database()->update($this->_sTable, $aSql, 'video_id = ' . $aVideo['video_id']);
			$this->database()->update(Phpfox::getT('video_text'), array(
					'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
					'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
				), 'video_id = ' . $aVideo['video_id']
			);
			
			$this->database()->delete(Phpfox::getT('video_category_data'), 'video_id = ' . (int) $iId);
			if (isset($this->_aCategories) && count($this->_aCategories))
			{
				foreach ($this->_aCategories as $iCategoryId)
				{
					$this->database()->insert(Phpfox::getT('video_category_data'), array('video_id' => $iId, 'category_id' => $iCategoryId));
				}		
			}
			
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('video', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);
			
			if (Phpfox::getUserParam('tag.can_add_tags_on_blogs') && Phpfox::isModule('tag') && !defined('PHPFOX_GROUP_VIEW'))
			{
				Phpfox::getService('tag.process')->update('video' . ($aVideo['module_id'] != 'video' ? '_' . $aVideo['module_id'] : ''), $iId, $aVideo['user_id'], (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
			}			
			
			if ($aVals['privacy'] == '4')
			{
				Phpfox::getService('privacy.process')->update('video', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
			}
			else 
			{
				Phpfox::getService('privacy.process')->delete('video', $iId);
			}			
			
			return true;
		}
		
		return Phpfox_Error::set(Phpfox::getPhrase('video.invalid_permissions'));
	}
	
	public function isViewed($iId)
	{
		$this->database()->update(Phpfox::getT('video'), array('is_viewed' => '1'), 'video_id = ' . (int) $iId);
	}
	
	public function delete($iId = null, &$aVideo = null)
	{
		if ($aVideo === null)
		{
			$aVideo = $this->database()->select('v.video_id, v.is_sponsor, v.user_id, v.destination, v.image_path')
				->from($this->_sTable, 'v')
				->where(($iId === null ? 'v.view_id = 1 AND v.user_id = ' . Phpfox::getUserId() : 'v.video_id = ' . (int) $iId))
				->execute('getRow');
				
			if (!isset($aVideo['video_id']))
			{
				return Phpfox_Error::set(Phpfox::getPhrase('video.unable_to_find_the_video_you_plan_to_delete'));
			}
		}
		else 
		{
			$bOverPass = true;
		}
		
		if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('video.can_delete_own_video')) || Phpfox::getUserParam('video.can_delete_other_video') || isset($bOverPass))
		{		
			$iFileSize = 0;			
						
			if (!empty($aVideo['destination']))
			{
				$sVideo = Phpfox::getParam('video.dir') . sprintf($aVideo['destination'], '');
				if (file_exists($sVideo))
				{
					$iFileSize += filesize($sVideo);
					
					@unlink($sVideo);				
				}
			}
			
			if (!empty($aVideo['image_path']))
			{
				$sImage = Phpfox::getParam('video.dir_image') . sprintf($aVideo['image_path'], '');
				if (file_exists($sImage))
				{
					$iFileSize += filesize($sImage);
					if ($iFileSize > 0 )
					{
							@unlink($sImage);
					}
				}
			}
			
			if ($iFileSize > 0)
			{
				Phpfox::getService('user.space')->update($aVideo['user_id'], 'video', $iFileSize, '-');	
			}
			
			(Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $aVideo['video_id'], 'video') : null);		
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('video', $aVideo['video_id']) : null);
			(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_video', $aVideo['video_id']) : null);			
			// Update user activity
			Phpfox::getService('user.activity')->update($aVideo['user_id'], 'video', '-');
			
			(Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($aVideo['user_id'], $aVideo['video_id'], 'video') : null);
			
			$this->database()->delete(Phpfox::getT('video'), 'video_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('video_category_data'), 'video_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('video_rating'), 'item_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('video_text'), 'video_id = ' . $aVideo['video_id']);
			$this->database()->delete(Phpfox::getT('video_embed'), 'video_id = ' . $aVideo['video_id']);
			if (isset($aVideo['is_sponsor']) && $aVideo['is_sponsor'] == 1)
			{
				$this->cache()->remove('video_sponsored');
			}
			return true;
		}
		
		return Phpfox_Error::set(Phpfox::getPhrase('video.invalid_permissions'));
	}
	
	public function deleteImage($iId)
	{
		$aVideo = $this->database()->select('v.video_id, v.user_id, v.destination, v.image_path')
			->from($this->_sTable, 'v')
			->where('v.video_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aVideo['video_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('video.unable_to_find_the_video_image_you_plan_to_delete'));
		}
		
		if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('video.can_delete_own_video')) || Phpfox::getUserParam('video.can_delete_other_video'))
		{		
			$iFileSize = 0;			
			if (!empty($aVideo['image_path']))
			{
				$sImage = Phpfox::getParam('video.dir_image') . sprintf($aVideo['image_path'], '');
				if (file_exists($sImage))
				{
					$iFileSize += filesize($sImage);
					
					@unlink($sImage);				
				}
			}
			
			if ($iFileSize > 0)
			{
				Phpfox::getService('user.space')->update($aVideo['user_id'], 'video', $iFileSize, '-');	
			}			
			
			$this->database()->update($this->_sTable, array('image_path' => null, 'image_server_id' => 0), 'video_id = ' . $aVideo['video_id']);
			
			return true;
		}
		
		return Phpfox_Error::set(Phpfox::getPhrase('video.invalid_permissions'));
	}	
	
	public function feature($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('video.can_feature_videos_', true);
		
		$this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')), 'video_id = ' . (int) $iId);
		
		$this->cache()->remove('video_featured');
		
		return true;
	}
	
	public function spotlight($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('video.can_spotlight_videos', true);
		
		$this->database()->update($this->_sTable, array('is_spotlight' => ($iType ? '1' : '0')), 'video_id = ' . (int) $iId);
		
		$this->cache()->remove('video_spotlight');
		
		return true;
	}	

	public function sponsor($iId, $iType)
	{
	    if (!Phpfox::getUserParam('video.can_sponsor_video') && !Phpfox::getUserParam('video.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK'))
	    {
			return Phpfox_Error::set('Hack attempt?');
	    }
	    
	    $iType = (int)$iType;
	    if ($iType != 0 && $iType != 1)
	    {
			return Phpfox_Error::set('iType: ' . d($iType, true));
	    }
	    
	    if ($sPlugin = Phpfox_Plugin::get('video.service_process_sponsor__end')){return eval($sPlugin);}
	    
	    $this->database()->update($this->_sTable, array('is_featured' => 0, 'is_sponsor' => $iType), 'video_id = ' . (int)$iId);
	    
		$this->cache()->remove('video_sponsored');
	    
	    return true;	    
	}
	
	public function approve($iId)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('video.can_approve_videos', true);
		
		$aVideo = $this->database()->select('v.video_id, v.view_id, v.title, v.privacy, v.privacy_comment, v.image_path, v.image_server_id, ' . Phpfox::getUserField())
			->from($this->_sTable, 'v')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
			->where('v.video_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aVideo['video_id']))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('video.unable_to_find_the_video_you_want_to_approve'));
		}
		
		if ($aVideo['view_id'] == '0')
		{
			return false;
		}
		
		$this->database()->update($this->_sTable, array('view_id' => '0', 'time_stamp' => PHPFOX_TIME), 'video_id = ' . $aVideo['video_id']);
		
		if (Phpfox::isModule('notification'))
		{
			Phpfox::getService('notification.process')->add('video_approved', $aVideo['video_id'], $aVideo['user_id']);
		}

		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('video', $aVideo['video_id'], $aVideo['title']);
		Phpfox::getLib('mail')->to($aVideo['user_id'])
			->subject(array('video.your_video_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('video.your_video_has_been_approved_on_site_title_n_nto_view_this_video_follow_the_link_below_n_a_href', array('site_title' => Phpfox::getParam('core.site_title'), 'sLink' => $sLink)))
			->notification('video.video_is_approved')
			->send();			
			
		((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Phpfox::getService('feed.process')->add('video', $iId, $aVideo['privacy'], $aVideo['privacy_comment'], 0, $aVideo['user_id']) : null);
			
		// Update user activity
		Phpfox::getService('user.activity')->update($aVideo['user_id'], 'video');

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
		if ($sPlugin = Phpfox_Plugin::get('video.service_process__call'))
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