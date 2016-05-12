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
 * @package 		Phpfox_Module
 * @version 		$Id: phpfox.class.php 1561 2010-05-04 13:16:02Z Miguel_Espinoza $
 */
class Module_Ad
{	
	public static $aTables = array(
		'ad',
		'ad_invoice',
		'ad_plan',
		'ad_track',
		'ad_sponsor'
	);
	
	public static $aInstallWritable = array(
		'file/pic/ad/'
	);		
}

?>