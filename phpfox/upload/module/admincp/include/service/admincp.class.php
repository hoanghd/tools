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
 * @package  		Module_Admincp
 * @version 		$Id: admincp.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Admincp_Service_Admincp extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
	}
	
	public function check()
	{
		$iCnt = 0;
		
		// Is the install dir. in place?
		if (file_exists(PHPFOX_DIR . 'install' . PHPFOX_DS . 'index.php'))
		{
			Phpfox_Error::set(Phpfox::getPhrase('admincp.install_dir_exists'));
			$iCnt++;
		}
		
		// Is main config writable?
		if (Phpfox::getLib('file')->isWritable(PHPFOX_DIR_SETTING . 'server.sett.php', true))
		{
			$sFilePath = 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'server.sett.php';
			
			Phpfox_Error::set(Phpfox::getPhrase('admincp.main_configuration_file_file_path_is_writable', array('file_path' => $sFilePath)));
			
			$iCnt++;
		}
		
		// Is the main file folder writable?
		if (Phpfox::getLib('file')->isWritable(PHPFOX_DIR_FILE, true))
		{
			$sFilePath = str_replace(PHPFOX_DIR, '', PHPFOX_DIR_FILE);
			
			Phpfox_Error::set(Phpfox::getPhrase('admincp.main_file_folder_is_writable_file_path', array('file_path' => $sFilePath)));
			
			$iCnt++;
		}		
		
		return ($iCnt ? false : true);
	}

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('admincp.service_admincp__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}

?>