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
 * @package 		Phpfox_Component
 * @version 		$Id: related.class.php 3131 2011-09-19 14:25:23Z Raymond_Benc $
 */
class Video_Component_Block_Related extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if ($iVideoId = $this->request()->getInt('video_id'))
		{
			list($iCnt, $aVideos) = Phpfox::getService('video')->getRelatedVideos($iVideoId, $this->request()->get('video_title'), ($this->request()->getInt('page_number') + 1));
			
			if (!count($aVideos))
			{
				return false;
			}			

			Phpfox::getLib('pager')->set(array('page' => $this->request()->getInt('page_number'), 'size' => Phpfox::getParam('video.total_related_videos'), 'count' => $iCnt));			
			if (Phpfox::getLib('pager')->getLastPage() <= $this->request()->getInt('page_number'))
			{
				return false;
			}			
			
			$this->template()->assign(array(
					'aRelatedVideos' => $aVideos,
					'bIsLoadingMore' => true					
				)
			);
		}
		else 
		{
			$aVideo = $this->getParam('aVideo');
			
			list($iCnt, $aVideos) = Phpfox::getService('video')->getRelatedVideos($aVideo['video_id'], $aVideo['title'], 0, true);			
					
			if (!count($aVideos))
			{
				return false;
			}
			
			$this->template()->assign(array(
					'sHeader' => 'Suggestions',
					'aRelatedVideos' => $aVideos
				)
			);	

			Phpfox::getLib('pager')->set(array('page' => $this->request()->getInt('page_number'), 'size' => Phpfox::getParam('video.total_related_videos'), 'count' => $iCnt));
			if (Phpfox::getLib('pager')->getTotalPages() > 1)
			{
				$this->template()->assign(array(
						'aFooter' => array(
							'Load More Suggestions' => '#'
						)				
					)
				);	
			}
			
			return 'block';		
		}		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('video.component_block_related_clean')) ? eval($sPlugin) : false);
	}
}

?>