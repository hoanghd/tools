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
 * @package  		Module_User
 * @version 		$Id: verify.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class User_Component_Controller_Password_Verify extends Phpfox_Component 
{
	/**
	 * Process the controller
	 *
	 */
	public function process()
	{
		if (Phpfox::isUser())
		{
			$this->url()->send('');
		}		
		
		if ($sRequest = $this->request()->get('id'))
		{
			if (Phpfox::getService('user.password')->verifyRequest($sRequest))
			{
				$this->url()->send('user.password.verify', null, Phpfox::getPhrase('user.new_password_successfully_sent_check_your_email_to_use_your_new_password'));
			}
		}
		
		$this->template()->setTitle(Phpfox::getPhrase('user.password_request_verification'))->setBreadcrumb(Phpfox::getPhrase('user.password_request_verification'));
	}
}

?>