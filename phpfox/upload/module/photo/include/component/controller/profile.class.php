<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Displays a users photo and album gallery on their profile.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: profile.class.php 2633 2011-05-30 13:57:44Z Raymond_Benc $
 */
class Photo_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->setParam('bIsProfile', true);
		
		if ($this->request()->get('req3') == 'albums')
		{
			$this->template()->assign('sReq3', 'albums');
			Phpfox::getComponent('photo.albums', array('bNoTemplate' => true), 'controller');			
		}
		else 
		{
			$this->template()->assign('sReq3', 'photo');
			Phpfox::getComponent('photo.index', array('bNoTemplate' => true), 'controller');
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}

?>