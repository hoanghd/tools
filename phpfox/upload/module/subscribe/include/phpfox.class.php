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
 * @package  		Module_Subscribe
 * @version 		$Id: phpfox.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class Module_Subscribe 
{	
	public static $aTables = array(
		'subscribe_package',
		'subscribe_purchase'
	);
	
	public static $aInstallWritable = array(
		'file/pic/subscribe/'
	);		
}

?>