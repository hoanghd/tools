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
 * @package  		Module_Log
 * @version 		$Id: phpfox.class.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
class Module_Log 
{
	public static $aDevelopers = array(
		array(
			'name' => 'Raymond Benc',
			'website' => ''
		)
	);
	
	public static $aTables = array(
		'log_session',
		'log_staff',
		'log_view'
	);
}

?>