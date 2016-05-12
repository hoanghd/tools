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
 * @version 		$Id: login.class.php 2625 2011-05-23 19:42:56Z Raymond_Benc $
 */
class User_Component_Controller_Login extends Phpfox_Component 
{	
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		define('PHPFOX_DONT_SAVE_PAGE', true);
		
		if (Phpfox::isUser())
		{
			$this->url()->send('profile');
		}
		
		switch (Phpfox::getParam('user.login_type'))
		{
			case 'user_name':
				$aValidation['login'] = Phpfox::getPhrase('user.provide_your_user_name');
				break;
			case 'email':
				$aValidation['login'] = Phpfox::getPhrase('user.provide_your_email');
				break;				
			default:
				$aValidation['login'] = Phpfox::getPhrase('user.provide_your_user_name_email');
		}
		$aValidation['password'] = Phpfox::getPhrase('user.provide_your_password');		
		
		$oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_login_form', 'aParams' => $aValidation));
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				list($bLogged, $aUser) = (Phpfox::getService('user.auth')->login($aVals['login'], $aVals['password'], (isset($aVals['remember_me']) ? true : false), Phpfox::getParam('user.login_type')));
				if ($bLogged)
				{
					$sReturn = '';
					if (Phpfox::getParam('core.redirect_guest_on_same_page'))
					{
						$sReturn = Phpfox::getLib('session')->get('redirect');
						if (is_bool($sReturn))
						{
							$sReturn = '';
						}		
						
						if ($sReturn)
						{
							$aParts = explode('/', trim($sReturn, '/'));		
							if (isset($aParts[0]))
							{
								$aParts[0] = Phpfox::getLib('url')->reverseRewrite($aParts[0]);
							}
							if (isset($aParts[0]) && !Phpfox::isModule($aParts[0]))
							{
								$aUserCheck = Phpfox::getService('user')->getByUserName($aParts[0]);
								if (isset($aUserCheck['user_id']))
								{
									if (isset($aParts[1]) && !Phpfox::isModule($aParts[1]))
									{
										$sReturn = '';	
									}
								}
								else 
								{
									$sReturn = '';
								}
							}
						}
					}
					
					if (!$sReturn)
					{
						$sReturn = Phpfox::getParam('user.redirect_after_login');
					}
					
					if ($sReturn == 'profile')
					{
						$sReturn = $aUser['user_name'];
					}
					
					$sReturn = trim($sReturn, '/');
					$sReturn = str_replace('/', '.', $sReturn);			
					
					Phpfox::getLib('session')->remove('redirect');
					if (isset($aUser['status_id']) && $aUser['status_id'] == 1)
					{
						$this->url()->send($sReturn, null, Phpfox::getPhrase('user.you_still_need_to_verify_your_email_address'));
					}
					$this->url()->send($sReturn);
				}
				else
				{
					if ($sPlugin = Phpfox_Plugin::get('user.controller_login_login_failed')){eval($sPlugin);}
				}
			}
		}
		
		$sSiteName = Phpfox::getParam('core.site_title');
		$this->template()->setBreadCrumb('Login')
			->setTitle(Phpfox::getPhrase('user.login_title'))
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'sSiteName' => $sSiteName,
				'sSignUpPage' => $this->url()->makeUrl('user.register'),
				'sDefaultEmailInfo' => ($this->request()->get('email') ? trim(base64_decode($this->request()->get('email'))) : '')
			)
		);
	}
}

?>