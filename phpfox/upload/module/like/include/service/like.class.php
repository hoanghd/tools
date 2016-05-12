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
 * @package 		Phpfox_Service
 * @version 		$Id: like.class.php 3018 2011-09-06 19:07:03Z Raymond_Benc $
 */
class Like_Service_Like extends Phpfox_Service 
{
	private $_iTotalLikeCount = 0;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('like');	
	}
	
	public function getTotalLikes()
	{
		return $this->_iTotalLikeCount;
	}
	
	public function getLikesForFeed($sType, $iItemId, $bIsLiked = false, $iLimit = 4, $bLoadCount = false)
	{
		if ($bIsLiked)
		{
			$iLimit--;
		}
		
		$aLikes = $this->database()->select('l.*, ' . Phpfox::getUserField())
			->from($this->_sTable, 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId . ' AND l.user_id != ' . Phpfox::getUserId())
			->order('l.time_stamp DESC')
			->group('u.user_id')
			->limit($iLimit)
			->execute('getSlaveRows');
			
		$this->_iTotalLikeCount = ($bLoadCount == true ? $this->database()->select('COUNT(*)')->from(Phpfox::getT('like'), 'l')->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId)->execute('getSlaveField') : count($aLikes));

		return $aLikes;
	}
	
	public function getLikes($sType, $iItemId)
	{
		$aLikes = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('like'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId)
			->order('u.full_name ASC')
			->group('u.user_id')
			->execute('getRows');			
					
		return $aLikes;
	}	
	
	public function getForMembers($sType, $iItemId)
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('like'), 'l')
			->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId)
			->execute('getSlaveField');
		
		$aLikes = $this->database()->select(Phpfox::getUserField())
			->from(Phpfox::getT('like'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int) $iItemId)
			->order('u.full_name ASC')
			->group('u.user_id')
			->limit(5)
			->execute('getRows');			
					
		return array($iCnt, $aLikes);		
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
		if ($sPlugin = Phpfox_Plugin::get('like.service_like__call'))
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