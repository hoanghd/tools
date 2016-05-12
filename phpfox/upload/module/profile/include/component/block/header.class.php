<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Profile Block Header
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Profile
 * @version 		$Id: header.class.php 3143 2011-09-20 14:08:51Z Miguel_Espinoza $
 */
class Profile_Component_Block_Header extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		(($sPlugin = Phpfox_Plugin::get('profile.component_block_header_process')) ? eval($sPlugin) : false);
		
		$aUser = $this->getParam('aUser');
		$aUser['is_header'] = true;
		$sRelationship = Phpfox::getService('custom')->getRelationshipPhrase($aUser);
		//d($sRelationship, true);
		$bCanSendPoke = Phpfox::isModule('poke') && Phpfox::getService('poke')->canSendPoke($aUser['user_id']);
		
		$this->template()->assign(array(
				'bCanPoke' => $bCanSendPoke,
				'sRelationship' => $sRelationship
			)
		);
		
		if (isset($bHideProfileBlockHeader))
		{
			return false;
		}		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('profile.component_block_header_clean')) ? eval($sPlugin) : false);
	}
}

?>