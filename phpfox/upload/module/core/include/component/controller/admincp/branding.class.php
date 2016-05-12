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
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Branding extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		//if ($aVals = $this->request()->getArray('val'))
		//{
			//if (Phpfox::getLib('phpfox.api')->send('clientVerification', $aVals))
			//{
				Phpfox::getLib('database')->update(Phpfox::getT('setting'), array('value_actual' => '1'), "var_name = 'branding'");
				Phpfox::getLib('cache')->remove();
				
				$this->url()->send('admincp', null, Phpfox::getPhrase('admincp.phpfox_branding_removal_successfully_installed'));
			//}
		//}
		
		$this->template()->setTitle(Phpfox::getPhrase('admincp.phpfox_branding_removal'))
			->setBreadcrumb(Phpfox::getPhrase('admincp.phpfox_branding_removal'))
			->assign(array(
					
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_branding_clean')) ? eval($sPlugin) : false);
	}
}

?>