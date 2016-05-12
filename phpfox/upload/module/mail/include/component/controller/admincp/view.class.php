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
 * @version 		$Id: view.class.php 1337 2009-12-17 18:58:27Z Raymond_Benc $
 */
class Mail_Component_Controller_Admincp_View extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aMessage = Phpfox::getService('mail')->getMail($this->request()->getInt('id'));
		
		if (!isset($aMessage['mail_id']))
		{
			return Phpfox_Error::display(Phpfox::getPhrase('mail.message_not_found'));
		}
		
		$this->template()->setTitle(Phpfox::getPhrase('mail.viewing_private_message'))
			->setBreadCrumb(Phpfox::getPhrase('mail.private_messages'), $this->url()->makeUrl('admincp.mail.private'))
			->setBreadcrumb(Phpfox::getPhrase('mail.viewing_private_message'), null, true)
			->assign(array(
					'aMessage' => $aMessage
			)
		);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_admincp_view_clean')) ? eval($sPlugin) : false);
	}
}

?>