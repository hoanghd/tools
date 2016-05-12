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
 * @package  		Module_Track
 * @version 		$Id: process.class.php 2103 2010-11-09 16:02:21Z Raymond_Benc $
 */
class Track_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor 2
	 */	
	public function __construct()
	{	

	}
	
	public function add($sType, $iId, $iUserId = null)
	{		
		if (Phpfox::getUserBy('is_invisible'))
		{
			return false;
		}
		
		return Phpfox::callback($sType . '.addTrack', $iId, $iUserId);
	}
	
	public function update($sTable, $iId, $iUserId = null)
	{
		$this->database()->update(Phpfox::getT($sTable), array(
				'time_stamp' => PHPFOX_TIME
			), 'item_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId()
		);
	}
	
	public function remove($sType, $iId, $iUserId = null)
	{		
		return Phpfox::callback($sType . '.removeTrack', $iId, $iUserId);
	}	
	
	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('track.service_process__call'))
		{
			return eval($sPlugin);
		}
			
		Phpfox_Error::throwException('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}

?>