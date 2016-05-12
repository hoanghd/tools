<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Apps_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		Phpfox::getUserParam('apps.can_view_app', true);
		
		if ($this->request()->get('req2') == 'view')
		{
			$this->url()->forward($this->url()->makeUrl('apps', array($this->request()->get('req3'), $this->request()->get('req4'))));
		}
		
		if ($this->request()->get('req2') == 'category')
		{
			$aApp = Phpfox::getService('apps')->getAppById($this->request()->get('req3'));
		}
		else
		{
			$aApp = Phpfox::getService('apps')->getAppById($this->request()->get('req2'));
		}
		
		
		/* if there is no app */
		if (empty($aApp))
		{
			return Phpfox_Error::display(Phpfox::getPhrase('apps.that_app_was_not_found_check'));
		}
		
		if (empty($aApp['is_installed']))
		{
			if ($this->request()->get('force') && Phpfox::getUserParam('apps.can_moderate_apps'))
			{
				
			}
			else
			{
				$this->setParam('aApp', $aApp);
				/* loading another controller as a safety measure vs loading the app and 
				 * a hovering block*/
				return Phpfox::getLib('module')->setController('apps.install');
			}
		}
		
		$sKey = Phpfox::getService('apps')->getKey($aApp['app_id']);
		
		$sFrameUrl = '' . $aApp['app_url'] . '?key=' . $sKey;
		
		$this->template()
			->setEditor()
			->assign(array(
					'aApp' => $aApp,
					'iUserId' => Phpfox::getUserId(),
					// 'sToken' => $sToken,
					'sFrameUrl' => $sFrameUrl					
				)
			)
			->setHeader(array(
				// '<script type="text/javascript">oParams["apps.keep_alive"] = ' . Phpfox::getParam('apps.token_keep_alive') . ';</script>',
				'view.js' => 'module_apps'				
			)
		);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('apps.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>