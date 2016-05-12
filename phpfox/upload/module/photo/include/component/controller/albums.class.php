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
 * @version 		$Id: albums.class.php 3177 2011-09-26 10:10:14Z Raymond_Benc $
 */
class Photo_Component_Controller_Albums extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::getUserParam('photo.can_view_photos', true);
		
		$aParentModule = $this->getParam('aParentModule');	
		
		if ($iDeleteId = $this->request()->getInt('delete'))
		{
			if (Phpfox::getService('photo.album.process')->delete($iDeleteId))
			{
				$this->url()->send('photo.albums', null, Phpfox::getPhrase('photo.photo_album_successfully_deleted'));
			}
		}		
		
		$bIsUserProfile = false;
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsUserProfile = true;
			$aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}		
		
		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			$bIsUserProfile = true;
			$aUser = $this->getParam('aUser');
		}
						
		$sPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'], 'photo') : ($aParentModule === null ? $this->url()->makeUrl('photo.albums') : $aParentModule['url'] . 'albums/photo/'));
		
		$aBrowseParams = array(
			'module_id' => 'photo.album',
			'alias' => 'pa',
			'field' => 'album_id',
			'table' => Phpfox::getT('photo_album'),
			'hide_view' => array('pending', 'myalbums')
		);		
		
		$this->search()->set(array(
				'type' => 'photo.album',
				'field' => 'pa.album_id',				
				'search_tool' => array(
					'table_alias' => 'pa',
					'search' => array(
						'action' => $sPhotoUrl,
						'default_value' => 'Search Photo Albums...',
						'name' => 'search',
						'field' => 'pa.name'
					),
					'sort' => array(
						'latest' => array('pa.time_stamp', 'Latest'),
						'most-talked' => array('pa.total_comment', 'Most Discussed')
					),
					'show' => array(9, 12, 15)
				)
			)
		);			
		
		if ($bIsUserProfile)
		{
			$this->search()->setCondition('AND pa.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND pa.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('privacy')->getForBrowse($aUser)) . ') AND pa.user_id = ' . (int) $aUser['user_id']);
		}
		else
		{	
			if ($this->request()->get('view') == 'myalbums')
			{
				Phpfox::isUser(true);
				
				$this->search()->setCondition('AND pa.user_id = ' . Phpfox::getUserId());
			}
			else
			{
				$this->search()->setCondition('AND pa.view_id = 0 AND pa.privacy IN(%PRIVACY%) AND pa.total_photo > 0');
			}
		}	
		
		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$aAlbums = $this->search()->browse()->getRows();
		$iCnt = $this->search()->browse()->getCount();		
		
		Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
		
		$this->template()->setTitle(Phpfox::getPhrase('photo.photo_albums'))
			->setHeader(array(
					'pager.css' => 'style_css'
				)
			)
			->setBreadcrumb(Phpfox::getPhrase('photo.photos'), $this->url()->makeUrl('photo'))
			->assign(array(
				'aAlbums' => $aAlbums
			)
		);	
		
		if ($aParentModule === null)
		{
			Phpfox::getService('photo')->buildMenu();
		}			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_albums_clean')) ? eval($sPlugin) : false);
	}
}

?>