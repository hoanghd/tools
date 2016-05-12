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
 * @package  		Module_Friend
 * @version 		$Id: mini.class.php 3248 2011-10-07 12:29:57Z Miguel_Espinoza $
 */
class Friend_Component_Block_Mini extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (Phpfox::getUserBy('profile_page_id'))
		{
			return false;
		}
		
		if (!Phpfox::isUser())
		{
			return false;
		}
		
		$iTotal = 20;		
		list($iCnt, $aRows) = Phpfox::getService('friend')->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId(), 'ls.last_activity DESC', 0, $iTotal, true, false, true);

		$this->template()->assign(array(
				'sHeader' => '' . Phpfox::getPhrase('friend.friends_online') . ' (' . $iCnt . ')',
				'aFriends' => $aRows			
			)
		);
		
		if (Phpfox::getUserParam('friend.can_remove_friends_from_dashboard'))
		{
			//$this->template()->assign('sDeleteBlock', 'dashboard');
		}

		return 'block';	
	}	
}

?>