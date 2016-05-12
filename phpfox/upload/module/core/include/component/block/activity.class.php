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
 * @version 		$Id: activity.class.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
class Core_Component_Block_Activity extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		$aModules = Phpfox::massCallback('getDashboardActivity');
		
		$aActivites = array(
			Phpfox::getPhrase('core.total_items') => $aUser['activity_total'],
			Phpfox::getPhrase('core.activity_points') => $aUser['activity_points'],
		);
		foreach ($aModules as $aModule)
		{
			foreach ($aModule as $sPhrase => $sLink)
			{
				$aActivites[$sPhrase] = $sLink;				
			}			
		}
		
		$this->template()->assign(array(
				'aActivites' => $aActivites
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_block_activity_clean')) ? eval($sPlugin) : false);
	}
}

?>