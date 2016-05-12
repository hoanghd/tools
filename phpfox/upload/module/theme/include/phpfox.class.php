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
 * @package  		Module_Theme
 * @version 		$Id: phpfox.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class Module_Theme 
{	
	public static $aTables = array(
		'theme',		
		'theme_css',
		'theme_style',
		'theme_style_logo',
		'theme_template'
	);
	
	public static $aInstallWritable = array(
		'file/css/'
	);		
}

?>