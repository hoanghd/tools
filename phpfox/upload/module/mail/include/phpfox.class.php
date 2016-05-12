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
 * @package  		Module_Mail
 * @version 		$Id: phpfox.class.php 1161 2009-10-09 07:42:41Z Raymond_Benc $
 */
class Module_Mail 
{
	public static $aTables = array(
		'mail',
		'mail_folder',
		'mail_hash',
		'mail_text'
	);
}

?>