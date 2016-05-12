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
 * @version 		$Id: browse.class.php 3311 2011-10-18 09:42:51Z Miguel_Espinoza $
 */
class Photo_Service_Browse extends Phpfox_Service 
{
	private $_sCategory = null;	
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		
	}
	
	public function category($sCategory)
	{
		$this->_sCategory = $sCategory;
		
		return $this;
	}
	
	public function query()
	{
		$this->database()->select('pa.name AS album_name, ')->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = photo.album_id');
		
		if (Phpfox::getLib('request')->get('mode') == 'edit')
		{
			$this->database()->select('pi.description, ')->leftJoin(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = photo.photo_id');
		}
	}	
	
	public function processRows(&$aRows)
	{
		foreach ($aRows as $iKey => $aRow)
		{				
			$aRows[$iKey]['link'] = Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']);
			$aRows[$iKey]['destination'] = Phpfox::getService('photo')->getPhotoUrl($aRow);
			
			if (Phpfox::getLib('request')->get('mode') == 'edit')
			{
				$sCategoryList = '';
				$aCategories = (array) $this->database()->select('category_id')
					->from(Phpfox::getT('photo_category_data'))
					->where('photo_id = ' . (int) $aRow['photo_id'])
					->execute('getSlaveRows');
					
				foreach ($aCategories as $aCategory)
				{
					$sCategoryList .= $aCategory['category_id'] . ',';
				}
				
				$aRows[$iKey]['category_list'] = rtrim($sCategoryList, ',');			
			}
		}
	}	
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
		if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
		{
			$this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = photo.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());	
		}
		
		if ($this->request()->get('req2') == 'tag')
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = photo.photo_id AND tag.category_id = \'photo\'');
		}		

		if ($this->_sCategory !== null || (isset($_SESSION['photo_category']) && $_SESSION['photo_category'] != ''))
		{		
			$this->database()->innerJoin(Phpfox::getT('photo_category_data'), 'pcd', 'pcd.photo_id = photo.photo_id');
			if (!$bIsCount)
			{
				$this->database()->group('photo.photo_id');
			}
		}	
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
		if ($sPlugin = Phpfox_Plugin::get('photo.service_browse__call'))
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