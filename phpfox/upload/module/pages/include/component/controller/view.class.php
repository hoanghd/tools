<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_IS_PAGES_VIEW', true);

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Pages_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$mId = $this->request()->getInt('req2');
		
		if (!($aPage = Phpfox::getService('pages')->getForView($mId)))
		{
			return Phpfox_Error::display(Phpfox::getPhrase('pages.the_page_you_are_looking_for_cannot_be_found'));
		}
		
		if (Phpfox::getUserParam('pages.can_moderate_pages') || $aPage['is_admin'])
		{
			
		}
		else
		{
			if ($aPage['view_id'] != '0')
			{
				return Phpfox_Error::display(Phpfox::getPhrase('pages.the_page_you_are_looking_for_cannot_be_found'));
			}
		}
		
		if ($aPage['view_id'] == '2')
		{
			return Phpfox_Error::display(Phpfox::getPhrase('pages.the_page_you_are_looking_for_cannot_be_found'));
		}		
		
		if (Phpfox::isMobile())
		{
			$aPageMenus = Phpfox::getService('pages')->getMenu($aPage);
			
			$aFilterMenu = array();
			foreach ($aPageMenus as $aPageMenu)
			{
				$aFilterMenu[$aPageMenu['phrase']] = $aPageMenu['url'];
			}
			
			$this->template()->buildSectionMenu('pages', $aFilterMenu);
		}
		
		if (Phpfox::isModule('privacy'))
		{
			Phpfox::getService('privacy')->check('pages', $aPage['page_id'], $aPage['user_id'], $aPage['privacy'], (isset($aPage['is_friend']) ? $aPage['is_friend'] : 0));		
		}
		
		
		$bCanViewPage = true;
		$sCurrentModule = $this->request()->get(($this->request()->get('req1') == 'pages' ? 'req3' : 'req2'));
		
		Phpfox::getService('pages')->buildWidgets($aPage['page_id']);
		
		$this->setParam('aPage', $aPage);
		$this->setParam('aParentModule', array(			
				'module_id' => 'pages',
				'item_id' => $aPage['page_id'],
				'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url'])
			)
		);
		
		$sModule = $this->request()->get(($this->request()->get('req1') == 'pages' ? 'req3' : 'req2'));
		if (empty($sModule) && !empty($aPage['landing_page']))
		{
			$sModule = $aPage['landing_page'];
			$sCurrentModule = $aPage['landing_page'];
		}		
		
		$this->template()->assign(array(
					'aPage' => $aPage,
					'sCurrentModule' => $sCurrentModule,
					'bCanViewPage' => $bCanViewPage,
					'iViewCommentId' => $this->request()->getInt('comment-id')
				)
			)
			->setHeader('cache', array(				
				'profile.css' => 'style_css',
				'pages.css' => 'style_css'
			)
		);
		
		if (Phpfox::isMobile())
		{
			$this->template()->setBreadcrumb($aPage['title'], Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']), true);
		}
		
		if ($bCanViewPage && $sModule && Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule, 'getPageSubMenu') && !$this->request()->getInt('comment-id'))
		{
			if (Phpfox::hasCallback($sModule, 'canViewPageSection') && !Phpfox::callback($sModule . '.canViewPageSection', $aPage['page_id']))
			{
				return Phpfox_Error::display(Phpfox::getPhrase('pages.unable_to_view_this_section_due_to_privacy_settings'));
			}
			
			$this->template()->assign('bIsPagesViewSection', true);
			$this->setParam('bIsPagesViewSection', true);
			$this->setParam('sCurrentPageModule', $sModule);
			
			Phpfox::getComponent($sModule . '.index', array('bNoTemplate' => true), 'controller');
		}
		elseif ($bCanViewPage && $sModule && Phpfox::getService('pages')->isWidget($sModule) && !$this->request()->getInt('comment-id'))
		{
			$this->template()->assign(array(
					'aWidget' => Phpfox::getService('pages')->getWidget($sModule)
				)
			);
		}
		else
		{
			$bCanPostComment = true;
			if ($sCurrentModule == 'pending')
			{
				$this->template()->assign('aPendingUsers', Phpfox::getService('pages')->getPendingUsers($aPage['page_id']));
				$this->setParam('global_moderation', array(
						'name' => 'pages',
						'ajax' => 'pages.moderation',
						'menu' => array(
							array(
								'phrase' => Phpfox::getPhrase('pages.delete'),
								'action' => 'delete'
							),
							array(
								'phrase' => Phpfox::getPhrase('pages.approve'),
								'action' => 'approve'
							)					
						)
					)
				);				
			}
			
			if (Phpfox::getService('pages')->isAdmin($aPage))
			{
				define('PHPFOX_FEED_CAN_DELETE', true);
			}

			$this->setParam('aFeedCallback', array(
					'module' => 'pages',
					'table_prefix' => 'pages_',
					'ajax_request' => 'pages.addFeedComment',
					'item_id' => $aPage['page_id'],
					'disable_share' => ($bCanPostComment ? false : true),
					'feed_comment' => 'pages_comment'
				)
			);		

			$this->template()->setTitle($aPage['title'])
				->setEditor()
				->setHeader('cache', array(
						'jquery/plugin/jquery.highlightFade.js' => 'static_script',	
						'jquery/plugin/jquery.scrollTo.js' => 'static_script',
						'comment.css' => 'style_css',
						'pager.css' => 'style_css',
						'feed.js' => 'module_feed'						
					)
				);			
		}	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}

?>