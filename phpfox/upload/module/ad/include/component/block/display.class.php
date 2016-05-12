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
 * @version 		$Id: display.class.php 3009 2011-09-05 19:03:23Z Raymond_Benc $
 */
class Ad_Component_Block_Display extends Phpfox_Component
{	
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (defined('PHPFOX_IS_AJAX_PAGE') && PHPFOX_IS_AJAX_PAGE)
		{
		
		}
		
		(($sPlugin = Phpfox_Plugin::get('ad.component_block_display_process__start')) ? eval($sPlugin) : false);
		if (!Phpfox::getParam('ad.enable_ads'))
		{
			return false;
		}	
		
		if ($this->getParam('block_id') == '1' || $this->getParam('block_id') == '3')
		{
			$aDeny = array(
				'forum'				
			);			
			
			if (in_array(Phpfox::getLib('module')->getModuleName(), $aDeny))
			{
				return false;
			}		
		}		
		
		$aAd = Phpfox::getService('ad')->getForBlock($this->getParam('block_id'));				
		
		if (!count($aAd))
		{			
			return false;
		}
		
		if (!empty($aAd['disallow_controller']))
		{
			$sControllerName = Phpfox::getLib('module')->getFullControllerName();
			$aParts = explode(',', $aAd['disallow_controller']);
			foreach ($aParts as $sPart)
			{
				$sPart = trim($sPart);
				if ($sControllerName == $sPart)
				{
					return false;
				}
			}
		}
		
		$this->template()->assign(array(
				'aAd' => $aAd
			)
		);	
		
		(($sPlugin = Phpfox_Plugin::get('ad.component_block_display_process__end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ad.component_block_display_clean')) ? eval($sPlugin) : false);
	}
}

?>