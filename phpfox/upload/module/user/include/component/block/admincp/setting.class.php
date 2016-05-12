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
 * @version 		$Id: setting.class.php 1676 2010-07-14 13:49:41Z Raymond_Benc $
 */
class User_Component_Block_Admincp_Setting extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aGroup = Phpfox::getService('user.group')->getGroup($this->request()->get('group_id'));

		$aSettings = Phpfox::getService('user.group.setting')->get($this->request()->get('group_id'), $this->request()->get('module_id'));
		
		$aCurr = array();
		if (isset($aSettings['phpfox']))
		{
			$aModuleSettings = $aSettings['phpfox'][$this->request()->get('module_id')];			
			foreach ($aModuleSettings as $aKey => $aVal)
			{
			    if (preg_match('/_sponsor_price/i',$aVal['name']))
			    {
					$aVals = Phpfox::getLib('parse.format')->isSerialized($aVal['value_actual']) ? unserialize($aVal['value_actual']) : 'No price set';
					if (is_array($aVals) && is_numeric(reset($aVals))) // so a module can have 2 settings with currencies (music.song, music.album)
					{
					    $this->setParam('currency_value_val[value_actual]['.$aVal['setting_id'].']', $aVals);			    
					}
					$aSettings['phpfox'][$this->request()->get('module_id')][$aKey]['isCurrency'] = 'Y';
			    }
			}
		}
		
		$this->template()->assign(array(
				'aSettings' => $aSettings,
				'aForms' => $aGroup,
				'aCurrency' => $aCurr
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_admincp_setting_clean')) ? eval($sPlugin) : false);
	}
}

?>