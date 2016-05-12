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
 * @package  		Module_User
 * @version 		$Id: phpfox.class.php 2603 2011-05-16 07:41:27Z Miguel_Espinoza $
 */
class Module_User 
{	
	public static $aTables = array(
		'user',
		'user_activity',
		'user_blocked',
		'user_count',
		'user_css',
		'user_css_code',
		'user_custom',
		'user_custom_value',
		'user_dashboard',
		'user_delete',
		'user_delete_feedback',
		'user_design_order',
		'user_featured',
		'user_field',
		'user_gateway',
		'user_group',
		'user_group_custom',
		'user_group_setting',
		'user_inactive',
		'user_ip',
		'user_notification',
		'user_privacy',
		'user_promotion',
		'user_rating',
		'user_setting',
		'user_snoop',
		'user_space',
		'user_track',
		'user_verify',
		'user_verify_error',
		'user_status',
		'upload_track',
		'user_custom_multiple_value',
		'user_custom_data'		
	);
	
	public static $aInstallWritable = array(
		'file/pic/user/'
	);		
}

?>