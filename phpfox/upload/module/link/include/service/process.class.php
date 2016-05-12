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
 * @version 		$Id: process.class.php 3120 2011-09-19 09:25:17Z Raymond_Benc $
 */
class Link_Service_Process extends Phpfox_Service 
{
	private $_iLinkId = 0;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('link');	
	}
	
	public function add($aVals, $bIsCustom = false, $aCallback = null)
	{
		if (!defined('PHPFOX_FORCE_IFRAME'))
		{
			define('PHPFOX_FORCE_IFRAME', true);
		}
		
		if (empty($aVals['privacy_comment']))
		{
			$aVals['privacy_comment'] = 0;
		}	

		if (empty($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}			
		
		$iId = $this->database()->insert($this->_sTable, array(
				'user_id' => Phpfox::getUserId(),
				'is_custom' => ($bIsCustom ? '1' : '0'),
				'module_id' => ($aCallback === null ? null : $aCallback['module']),
				'item_id' => ($aCallback === null ? 0 : $aCallback['item_id']),
				'parent_user_id' => (isset($aVals['parent_user_id']) ? (int) $aVals['parent_user_id'] : 0),
				'link' => $this->preParse()->clean($aVals['link']['url'], 255),
				'image' => (isset($aVals['link']['image_hide']) && $aVals['link']['image_hide'] == '1' ? null : $this->preParse()->clean($aVals['link']['image'], 255)),
				'title' => $this->preParse()->clean($aVals['link']['title'], 255),
				'description' => $this->preParse()->clean($aVals['link']['description'], 200),
				'status_info' => (empty($aVals['status_info']) ? null : $this->preParse()->prepare($aVals['status_info'])),
				'privacy' => (int) $aVals['privacy'],
				'privacy_comment' => (int) $aVals['privacy_comment'],
				'time_stamp' => PHPFOX_TIME,
				'has_embed' => (empty($aVals['link']['embed_code']) ? '0' : '1')
			)
		);

		if (!empty($aVals['link']['embed_code']))
		{
			$this->database()->insert(Phpfox::getT('link_embed'), array(
					'link_id' => $iId,
					'embed_code' => $this->preParse()->prepare($aVals['link']['embed_code'])
				)
			);
		}
		
		$this->_iLinkId = $iId;
		
		return ($bIsCustom ? $iId : Phpfox::getService('feed.process')->callback($aCallback)->add('link', $iId, $aVals['privacy'], $aVals['privacy_comment'], (isset($aVals['parent_user_id']) ? (int) $aVals['parent_user_id'] : 0)));
	}
	
	public function getInsertId()
	{
		return (int) $this->_iLinkId;
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
		if ($sPlugin = Phpfox_Plugin::get('link.service_process__call'))
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