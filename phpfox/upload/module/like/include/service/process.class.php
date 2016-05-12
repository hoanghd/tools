<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Module_Like
 * @version 		$Id: process.class.php 2744 2011-07-20 13:42:24Z Raymond_Benc $
 */
class Like_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('like');
	}
	
	public function add($sType, $iItemId, $iUserId = null)
	{
		$bIsNotNull = false;
		if ($iUserId === null)
		{
			$iUserId = Phpfox::getUserId();
			$bIsNotNull = true;
		}
		
		$iCheck = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('like'))
			->where('type_id = \'' . $this->database()->escape($sType) . '\' AND item_id = ' . (int) $iItemId . ' AND user_id = ' . $iUserId)
			->execute('getField');
		
		if ($iCheck)
		{
			return Phpfox_Error::set(Phpfox::getPhrase('feed.you_have_already_liked_this_feed'));
		}		
		
		$iCnt = (int) $this->database()->select('COUNT(*)')	
			->from(Phpfox::getT('like_cache'))
			->where('type_id = \'' . $this->database()->escape($sType) . '\' AND item_id = ' . (int) $iItemId . ' AND user_id = ' . (int) $iUserId)
			->execute('getSlaveField');
		
		$this->database()->insert($this->_sTable, array(
				'type_id' => $sType,
				'item_id' => (int) $iItemId,
				'user_id' => $iUserId,
				'time_stamp' => PHPFOX_TIME
			)
		);
		$iCnt = 0;
		if (!$iCnt)
		{
			$this->database()->insert(Phpfox::getT('like_cache'), array(
					'type_id' => $sType,
					'item_id' => (int) $iItemId,
					'user_id' => $iUserId
				)
			);				
		}
		
		Phpfox::callback($sType . '.addLike', $iItemId, ($iCnt ? true : false), ($bIsNotNull ? null : $iUserId));
		
		return true;
	}
	
	public function delete($sType, $iItemId)
	{
		$this->database()->delete(Phpfox::getT('like'), 'type_id = \'' . $this->database()->escape($sType) . '\' AND item_id = ' . (int) $iItemId . ' AND user_id = ' . Phpfox::getUserId());
		
		Phpfox::callback($sType . '.deleteLike', $iItemId);
		
		return true;	
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
		if ($sPlugin = Phpfox_Plugin::get('like.service_process__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>