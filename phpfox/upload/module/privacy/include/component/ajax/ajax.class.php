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
 * @package  		Module_Privacy
 * @version 		$Id: ajax.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Privacy_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function getFriends()
	{
		Phpfox::getBlock('privacy.friend');
		
		$this->setTitle(Phpfox::getPhrase('privacy.custom_privacy'));
	}
}

?>