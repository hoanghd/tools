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
 * @version 		$Id: add.class.php 3250 2011-10-07 12:45:48Z Raymond_Benc $
 */
class Pages_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('pages.can_add_new_pages', true);
		
		Phpfox::getService('pages')->setIsInPage();
		
		$bIsEdit = false;
		if (($iEditId = $this->request()->getInt('id')) && ($aPage = Phpfox::getService('pages')->getForEdit($iEditId)))			
		{
			$bIsEdit = true;
			$this->template()->assign('aForms', $aPage);
			
			$aMenus = array(
				'detail' => Phpfox::getPhrase('pages.details'),
				'info' => Phpfox::getPhrase('pages.information')
			);
			
			if (!$aPage['is_app'])
			{
				$aMenus['photo'] = Phpfox::getPhrase('pages.photo');
			}
			$aMenus['permissions'] = Phpfox::getPhrase('pages.permissions');
			if (Phpfox::isModule('friend'))
			{
				$aMenus['invite'] = Phpfox::getPhrase('pages.invite');
			}
			$aMenus['url'] = Phpfox::getPhrase('pages.url');
			$aMenus['admins'] = Phpfox::getPhrase('pages.admins');
			$aMenus['widget'] = Phpfox::getPhrase('pages.widgets');			
			
			$this->template()->buildPageMenu('js_pages_block', 
				$aMenus,
				array(
					'link' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']),
					'phrase' => Phpfox::getPhrase('pages.view_this_page')
				)				
			);					
			
			if (($aVals = $this->request()->getArray('val')))
			{
				if (Phpfox::getService('pages.process')->update($aPage['page_id'], $aVals, $aPage))
				{
					$aNewPage = Phpfox::getService('pages')->getForEdit($aPage['page_id']);
					
					$this->url()->forward(Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']));
				}
			}
		}		
		
		$this->template()->setTitle(($bIsEdit ? '' . Phpfox::getPhrase('pages.editing_page') . ': ' . $aPage['title']: Phpfox::getPhrase('pages.creating_a_page')))
			->setBreadcrumb(Phpfox::getPhrase('pages.pages'), $this->url()->makeUrl('pages'))
			->setBreadcrumb(($bIsEdit ? '' . Phpfox::getPhrase('pages.editing_page') . ': ' . $aPage['title']: Phpfox::getPhrase('pages.creating_a_page')), $this->url()->makeUrl('pages.add'), true)
			->setEditor()
			->setFullSite()
			->setPhrase(array(
					'core.select_a_file_to_upload'
				)
			)			
			->setHeader(array(
					'pages.css' => 'style_css',
					'privacy.css' => 'module_user',
					'progress.js' => 'static_script',
					'pages.js' => 'module_pages'
				)
			)
			->setHeader(array('<script type="text/javascript">$Behavior.pagesProgressBarSettings = function(){ if ($Core.exists(\'#js_pages_block_customize_holder\')) { oProgressBar = {holder: \'#js_pages_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 1, total: 1, frame_id: \'js_upload_frame\', file_id: \'image\'}; $Core.progressBarInit(); } }</script>'))			
			->assign(array(
					'aPermissions' => (isset($aPage) ? Phpfox::getService('pages')->getPerms($aPage['page_id']) : array()),
					'aTypes' => Phpfox::getService('pages.type')->get(),
					'bIsEdit' => $bIsEdit,
					'iMaxFileSize' => Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('pages.max_upload_size_pages') / 1024) * 1048576),
					'aWidgetEdits' => Phpfox::getService('pages')->getWidgetsForEdit()
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pages.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}

?>