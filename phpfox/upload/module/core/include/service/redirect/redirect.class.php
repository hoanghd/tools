<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Service
 * @version 		$Id: core.class.php 2209 2010-11-26 12:24:28Z Miguel_Espinoza $
 */
class Core_Service_Redirect_Redirect extends Phpfox_Service
{
	/**
	 * If a url is requested and it does not exist this function checks if it ever existed
	 * if so then it provides the old title for the item
	 *
	 * @example http://site.com/index.php?do=/user1/blog/blog-1fds/ vs http://site.com/index.php?do=/user1/blog/blog-1/
	 * @param string $sModule
	 * @param string $sOldTitle
	 * @return string|false
	 */
	public function getRedirection($sModule, $sOldTitle)
	{
		
		if (Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule, 'getRedirectionTable'))
		{
			$sNewTitle = $this->database()->select('new_title')
					->from(Phpfox::callback($sModule.'.getRedirectionTable'))
					->where('old_title = "' . $sOldTitle . '"')
					->execute('getSlaveField');

			if (!empty($sNewTitle))
			{
				return $sNewTitle;
			}
		}
		return false;
	}

	/**
	 * This function checks if a user is allowed to update the URL of a specific blog
	 */
	public function canUpdateURL($sModule, $iUser, $iItemId)
	{
		// first the general permission
		if (!Phpfox::isModule($sModule) 
				|| (Phpfox::getUserParam($sModule.'.can_update_url') == false)
				|| !Phpfox::hasCallback($sModule, 'getRedirectionTable'))
		{
			return false;
		}
		$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::callback($sModule. '.getRedirectionTable'))
				->where('item_id = ' . (int)$iItemId)
				->execute('getSlaveField');
		if ($iCnt >= Phpfox::getUserParam($sModule.'.how_many_url_updates') && $iCnt > 0 &&
				Phpfox::getUserParam($sModule.'.how_many_url_updates') > 0)
		{
			return false;
		}

		return true;
	}

	public function check($sActualTitle, $sReq = 'req3')
	{
		$sTitle = urldecode(Phpfox::getLib('request')->get($sReq));
		$sActualTitle = Phpfox::getLib('url')->cleanTitle($sActualTitle);		

		if (empty($sTitle) || empty($sActualTitle))
		{
			return;
		}
		
		if ($sTitle != $sActualTitle)
		{
			$sPath = '';
			
			$aRequests = (array) Phpfox::getLib('request')->getRequests();
			if (defined('PHPFOX_IS_AJAX_PAGE') && PHPFOX_IS_AJAX_PAGE)
			{
				$aSubRequests = explode('/', trim(Phpfox::getLib('request')->get(PHPFOX_GET_METHOD), '/'));
				$aRequests = array();
				foreach ($aSubRequests as $iKey => $sSubRequest)
				{
					$aRequests['req' . ($iKey + 1)]	= $sSubRequest;
				}
			}
			
			foreach ($aRequests as $sKey => $sValue)
			{
				if ($sKey == PHPFOX_GET_METHOD)
				{
					continue;
				}				
				
				if ($sKey == $sReq)
				{
					$sValue = $sActualTitle;
				}
				
				$sPath .= $sValue . '.';
			}
			$sPath = rtrim($sPath, '.');
			
			if (!empty($sActualTitle))
			{
				Phpfox::getLib('url')->send($sPath, array(), null, 301);
			}
		}
	}
}
?>
