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
 * @version 		$Id: invite.class.php 2546 2011-04-18 19:51:26Z Raymond_Benc $
 */
class Event_Component_Block_Invite extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!Phpfox::isUser())
		{
			return false;
		}
		
		$aEventInvites = Phpfox::getService('event')->getInviteForUser();
		
		if (!count($aEventInvites))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => 'Invites',
				'aEventInvites' => $aEventInvites
			)
		);
		
		return 'block';	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('event.component_block_invite_clean')) ? eval($sPlugin) : false);
	}
}

?>