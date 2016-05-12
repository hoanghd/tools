<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Photo controller where we upload new images and includes
 * a photo progress bar.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: upload.class.php 2696 2011-06-30 19:30:33Z Raymond_Benc $
 */
class Photo_Component_Controller_Upload extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->url()->send('photo.add', array(), null, 301);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_upload_clean')) ? eval($sPlugin) : false);
	}
}

?>