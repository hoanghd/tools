<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: ajax.class.php 1117 2009-09-30 12:55:29Z Miguel_Espinoza $
 */
class Contact_Component_Ajax_Ajax extends Phpfox_Ajax
{

	public function manageOrdering()
	{
		$aVals = $this->get('val');
		Phpfox::getService('contact.process')->updateOrdering($aVals['ordering']);
	}
}

?>