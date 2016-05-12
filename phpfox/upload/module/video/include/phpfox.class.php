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
 * @package  		Module_Video
 * @version 		$Id: phpfox.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class Module_Video
{	
	public static $aTables = array(
		'video',
		'video_category',
		'video_category_data',
		'video_embed',
		'video_rating',
		'video_text',
		'video_track'
	);
	
	public static $aInstallWritable = array(
		'file/video/',
		'file/pic/video/'
	);
}

?>