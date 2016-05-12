<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: minimize.class.php 2246 2010-12-15 20:19:58Z Raymond_Benc $
 */
class Phpfox_File_Minimize
{
	public function __construct()
	{		
	}
	
	public function js($sContent)
	{		
		if (file_exists(PHPFOX_DIR_LIB . 'jsmin/jsmin.class.php'))
		{
			require_once(PHPFOX_DIR_LIB . 'jsmin/jsmin.class.php');		
			
			return JSMin::minify($sContent);
		}
		
		return $sContent;
	}
	
	public function css($sContent)
	{
		$sPath = Phpfox::getLib('template')->getStyle('image');
		$sContent = str_replace('url(../image/', 'url(' . $sPath, $sContent);
		$sContent = str_replace('url("../image/', 'url("' . $sPath, $sContent);
		$sContent = str_replace('url(\'../image/', 'url(\'' . $sPath, $sContent);
		$sContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sContent);
		$sContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $sContent);	
							    
		return $sContent;
	}
}