<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Add a new setting from the Admin CP
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: add.class.php 2105 2010-11-09 16:15:38Z Miguel_Espinoza $
 */
class Admincp_Component_Controller_Setting_Add extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 * @todo Complete the update routine...
	 */
	public function process()
	{	
		if (Phpfox::getParam('core.phpfox_is_hosted'))
		{
			$this->url()->send('admincp');
		}			
		
		Phpfox::getUserParam('core.can_add_new_setting', true);
		
		$bEdit = false;
		if (($iId = $this->request()->getInt('id')))
		{
			$aSetting = Phpfox::getService('admincp.setting')->getForEdit($iId);	
			
			if (is_array($aSetting) && isset($aSetting['setting_id']))
			{
				$bEdit = true;
				$this->url()->send('admincp.setting');
				$this->template()->assign(array(
						'aForms' => $aSetting						
					)
				);
			}
		}		
		
		$aValidation = array(
			'var_name' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('admincp.add_variable_name')
			),
			'title' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('admincp.add_title_for_setting')
			),
			'info' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('admincp.add_information_regarding_setting')
			)
		);		
		
		$oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_setting_form', 'aParams' => $aValidation));		
		
		if ($aVals = $this->request()->getArray('val'))
		{			
			if ($oValid->isValid($aVals))
			{
				if ($bEdit)
				{
					exit('Updating...');
				}
				else 
				{
					if (($sSetting = Phpfox::getService('admincp.setting')->isSetting($aVals['var_name'])))
					{
						Phpfox_Error::set(Phpfox::getPhrase('admincp.already_in_use') . ': ' . $sSetting);	
					}
					else 
					{					
						if (($sSetting = Phpfox::getService('admincp.setting.process')->add($aVals)))
						{
							$this->url()->send('admincp', array('setting', 'add'), Phpfox::getPhrase('admincp.added') . ': ' . $sSetting);	
						}					
					}
				}
			}
		}		
		
		$this->template()->assign(array(
					'aProducts' => Phpfox::getService('admincp.product')->get(),
					'aGroups' => Phpfox::getService('admincp.setting.group')->getGroups(),
					'aModules' => Phpfox::getLib('module')->getModules(),
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(),
					'bEdit' => $bEdit			
				)
			)
			->setBreadCrumb(Phpfox::getPhrase('admincp.add_setting'))
			->setTitle(Phpfox::getPhrase('admincp.add_setting'));
			
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_setting_add_process')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_setting_add_clean')) ? eval($sPlugin) : false);
	}
}

?>