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
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 3296 2011-10-12 13:29:57Z Raymond_Benc $
 */
class Video_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('video.can_upload_videos', true);
		
		$sModule = $this->request()->get('module', false);
		$iItem =  $this->request()->getInt('item', false);		
		
		$aCallback = false;
		if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getVideoDetails'))
		{			
			if (($aCallback = Phpfox::callback($sModule . '.getVideoDetails', array('item_id' => $iItem))))
			{			
				$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);	
				if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'video.share_videos'))
				{
					return Phpfox_Error::display('Unable to view this item due to privacy settings.');
				}				
			}
		}		
		
		if (($aVals = $this->request()->get('val')))
		{
			if (($iFlood = Phpfox::getUserParam('video.flood_control_videos')) !== 0)
			{
				$aFlood = array(
					'action' => 'last_post', // The SPAM action
					'params' => array(
						'field' => 'time_stamp', // The time stamp field
						'table' => Phpfox::getT('video'), // Database table we plan to check
						'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
						'time_stamp' => $iFlood * 60 // Seconds);	
					)
				);
							 			
				// actually check if flooding
				if (Phpfox::getLib('spam')->check($aFlood))
				{
					Phpfox_Error::set(Phpfox::getPhrase('video.you_are_sharing_a_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());	
				}
			}					
					
			if (Phpfox_Error::isPassed())
			{			
				if (Phpfox::getService('video.grab')->get($aVals['url']))
				{			
					if ($iId = Phpfox::getService('video.process')->addShareVideo($aVals))
					{
						$aVideo = Phpfox::getService('video')->getForEdit($iId);
						
						if (Phpfox::getService('video.grab')->hasImage())
						{					
							if (isset($aVals['module']) && isset($aVals['item']) && Phpfox::hasCallback($aVals['module'], 'uploadVideo'))
							{
								$aCallback = Phpfox::callback($aVals['module'] . '.uploadVideo', $aVals['item']);
					
								if ($aCallback !== false)
								{
									$this->url()->send($aCallback['url_home'], array('video', $sTitle), Phpfox::getPhrase('video.video_successfully_added'));
								}
							}
							
							$this->url()->permalink('video', $aVideo['video_id'], $aVideo['title'], true, Phpfox::getPhrase('video.video_successfully_added'));
						}
						else 
						{
							$this->url()->send('video.edit.photo', array('id' => $aVideo['video_id']), Phpfox::getPhrase('video.video_successfull_added_however_you_will_have_to_manually_upload_a_photo_for_it'));
						}
					}
				}
			}
			
			$sModule = (isset($aVals['module']) ? $aVals['module'] : false);
			$iItem =  (isset($aVals['item']) ? $aVals['item'] : false);
		}
		
		$sMethod = Phpfox::getParam('video.video_enable_mass_uploader') && $this->request()->get('method','') != 'simple' ? 'massuploader' : 'simple';
		$sMethodUrl = str_replace(array('method_simple/','method_massuploader/'), '',$this->url()->getFullUrl()) . 'method_' . ($sMethod == 'simple' ? 'massuploader' : 'simple') . '/';				
	
		if ($sMethod == 'massuploader')
		{
			$iMaxFileSize = (Phpfox::getUserParam('video.video_file_size_limit') === 0 ? null : ((Phpfox::getUserParam('video.video_file_size_limit') / 1) * 1048576));
			$this->template()->setPhrase(array(							
						'photo.you_can_upload_a_jpg_gif_or_png_file',
						'core.name',
						'core.status',
						'core.in_queue',
						'core.upload_failed_your_file_size_is_larger_then_our_limit_file_size'
					)
				)
				->setHeader(array(
				'massuploader/swfupload.js' => 'static_script',
				'massuploader/upload.js' => 'static_script',
				'<script type="text/javascript">
					$oSWF_settings =
					{
						object_holder: function()
						{
							return \'swf_video_upload_button_holder\';
						},
						
						div_holder: function()
						{
							return \'swf_video_upload_button\';
						},
						
						get_settings: function()
						{		
							swfu.setUploadURL("' . $this->url()->makeUrl('video.frame') . '");
							swfu.setFileSizeLimit("'.$iMaxFileSize .' B");
							swfu.setFileUploadLimit(1);
							swfu.setFileQueueLimit(1);
							swfu.customSettings.flash_user_id = '.Phpfox::getUserId() .';
							swfu.customSettings.sHash = "'.Phpfox::getService('core')->getHashForUpload().'";
							swfu.setFileTypes("*.mpg; *.mpeg; *.wmv; *.avi; *.mov; *.flv","Video files (mpg, mpeg, wmv, avi, mov or flv)");
							swfu.atFileQueue = function()
							{
								$(\'#js_upload_actual_inner_form\').slideUp();
							
								$(\'#js_video_form :input\').each(function(iKey, oObject)
								{									
									swfu.addPostParam($(oObject).attr(\'name\'), $(oObject).val());
								});
							}
						}
					}
				</script>',
				)
			);			
		}		
		
		$aMenus = array();		
		if (Phpfox::getParam('video.allow_video_uploading'))
		{
			$aMenus['file'] = 'File Upload';
		}
		$aMenus['url'] = 'Paste URL';
		
		$this->template()->buildPageMenu('js_upload_video', $aMenus);
		
		$this->template()->setTitle('Upload/Share a Video')
			->setBreadcrumb('Video', ($aCallback === false ? $this->url()->makeUrl('video') : $aCallback['url_home_photo']))
			->setBreadcrumb('Upload/Share a Video', ($aCallback === false ? $this->url()->makeUrl('video.add') : $this->url()->makeUrl('video.add', array('module' => $sModule, 'item' => $iItem))), true)
			->setFullSite()			
			->assign(array(
					'sModule' => $sModule,
					'iItem' => $iItem,				
					'sMethod' => $sMethod,
					'sMethodUrl' => $sMethodUrl			
				)
			)
			->setHeader('cache', array(
					'upload.js' => 'module_video',
					'video.js' => 'module_video'
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('video.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}

?>