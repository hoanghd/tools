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
 * @package  		Module_Invite
 * @version 		$Id: index.class.php 2633 2011-05-30 13:57:44Z Raymond_Benc $
 */
class Invite_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!$this->request()->get('user') && !$this->request()->get('id'))
		{
			Phpfox::isUser(true);
		}
		
		list($bIsRegistration, $sNextUrl) = $this->url()->isRegistration(2);	
		
		(($sPlugin = Phpfox_Plugin::get('invite.component_controller_index_process_start')) ? eval($sPlugin) : false);

		// is a user sending an invite
		if ($aVals = $this->request()->getArray('val'))
		{
			// we may have a bunch of emails separated by commas, lets array them
			$aMails = explode(',', $aVals['emails']);
			
			list($aMails, $aInvalid, $aCacheUsers) = Phpfox::getService('invite')->getValid($aMails, Phpfox::getUserId());		

			// failed emails
			$sFailed = '';
			$bSent = true;
			if (!empty($aMails))
			{
				foreach ($aMails as $sMail)
				{
					$sMail = trim($sMail);
					// we insert the invite id and send the reference, so we can track which users
					// have signed up

					$iInvite = Phpfox::getService('invite.process')->addInvite($sMail, Phpfox::getUserId());	
					
					(($sPlugin = Phpfox_Plugin::get('invite.component_controller_index_process_send')) ? eval($sPlugin) : false);		

					// check if we could send the mail
					$sLink = Phpfox::getLib('url')->makeUrl('invite', array('id' => $iInvite));
					$bSent = Phpfox::getLib('mail')->to($sMail)		
						->fromEmail(Phpfox::getUserBy('email'))				
						->fromName(Phpfox::getUserBy('full_name'))				
						->subject(array('invite.full_name_invites_you_to_site_title', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title'))))
						->message(array('invite.full_name_invites_you_to_site_title_link', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
						->send();
				}
			}		

			if ($bIsRegistration === true)
			{
				$this->url()->send($sNextUrl, null, Phpfox::getPhrase('invite.your_friends_have_successfully_been_invited'));
			}								
				
			$this->template()->assign(array(
					'aValid' => $aMails,
					'aInValid' => $aInvalid,
					'aUsers' => $aCacheUsers
				)
			);
		}

		// check if someone is visiting a link sent by email
		if (($iId = $this->request()->getInt('id')))
		{
			// we update the entry to be seen:			
			if (Phpfox::getService('invite.process')->updateInvite($iId, true))
			{
				$this->url()->send('user.register');
			}
		}
		// check if someone is visiting from a link pasted in a site or other places
		elseif ($iId = $this->request()->getInt('user'))
		{
			if (Phpfox::getService('invite.process')->updateInvite($iId, false))
			{
				$this->url()->send('user.register');
			}
		}		
		
		$this->template()->setTitle(Phpfox::getPhrase('invite.invite_your_friends'))
			->setBreadcrumb(Phpfox::getPhrase('invite.invite_your_friends'))
			->assign(array(
					'sFullName' => Phpfox::getUserBy('full_name'),
					'sSiteEmail' => Phpfox::getUserBy('email'),
					'sSiteTitle' => Phpfox::getParam('core.site_title'),
					'sIniviteLink' => Phpfox::getLib('url')->makeUrl('invite', array('user' => Phpfox::getUserId())),
					'bIsRegistration' => $bIsRegistration,
					'sNextUrl' => $this->url()->makeUrl($sNextUrl)
				)
			);
		
		(($sPlugin = Phpfox_Plugin::get('invite.component_controller_index_process_end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('invite.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>