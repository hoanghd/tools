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
 * @version 		$Id: form.class.php 1305 2009-12-08 02:51:17Z Raymond_Benc $
 */
class Admincp_Component_Block_Module_Form extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->template()->assign(array(
				'aModules' => Phpfox::getService('admincp.module')->getModules(),
				'bUseClass' => $this->getParam('class'),
				'sModuleFormTitle' => $this->getParam('module_form_title', Phpfox::getPhrase('admincp.module')),
				'bModuleFormRequired' => $this->getParam('module_form_required', true),
				'sModuleFormValue' => $this->getParam('module_form_value', Phpfox::getPhrase('admincp.select') . ':'),
				'sModuleFormId' => $this->getParam('module_form_id', 'module_id')
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_block_module_form_clean')) ? eval($sPlugin) : false);
	}
}

?>