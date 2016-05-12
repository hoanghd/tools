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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 2846 2011-08-18 09:30:49Z Raymond_Benc $
 */
class Admincp_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function buildSearchValues()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
		
		$this->call('aAdminCPSearchValues = ' . json_encode(Phpfox::getService('admincp.setting')->getForSearch()) . ';');
	}
	
	public function updateBlockActivity()
	{		
		if (Phpfox::getService('admincp.block.process')->updateActivity($this->get('id'), $this->get('active')))
		{
			
		}
	}	
	
	public function blockOrdering()
	{
		if ($aVals = $this->get('val'))
		{
			if (Phpfox::getService('admincp.block.process')->updateOrder($aVals['ordering'], (isset($aVals['style_id']) ? (int) $aVals['style_id'] : null)))
			{

			}			
		}		
	}
	
	public function getBlocks()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getBlock('admincp.block.setting');		

		$this->html('#js_setting_block', $this->getContent(false));
		$this->show('#content_editor_text');
		$this->show('#js_editing_block');
		$this->html('#js_editing_block_text', ($this->get('m_connection') == '' ? Phpfox::getPhrase('admincp.site_wide') : $this->get('m_connection')));		
		$this->call('$.scrollTo(0);');		
		$this->call('$Core.loadInit();');
		$this->call('Core_drag.init({table: \'.js_drag_drop\', ajax: \'admincp.blockOrdering\'});');
	}
	
	public function removeSettingFromArray()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
		Phpfox::getService('admincp.setting.process')->removeSettingFromArray($this->get('setting'), $this->get('value'));
	}
	
	public function checkProductVersions()
	{
		Phpfox::getService('admincp.product.process')->checkProductVersions();
	}
	
	public function updateModuleActivity()
	{
		if (Phpfox::getService('admincp.module.process')->updateActivity($this->get('id'), $this->get('active')))
		{
			
		}		
	}
	
	public function componentFeedActivity()
	{
		if (Phpfox::getService('admincp.component.process')->updateActivity($this->get('id'), $this->get('active')))
		{
			
		}		
	}
}

?>