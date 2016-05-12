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
 * @package  		Module_Tag
 * @version 		$Id: cloud.class.php 3214 2011-09-30 12:05:14Z Raymond_Benc $
 */
class Tag_Component_Block_Cloud extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		if ((defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW')) && (!defined('PHPFOX_SHOW_TAGS')))
		{
			return false;
		}
		
		$sType = $this->getParam('sTagType', null);
		$bNoTagBlock = $this->getParam('bNoTagBlock', false);
		
		$aRows = Phpfox::getService('tag')->getTagCloud($sType, (($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser'))) ? $aUser['user_id'] : null), $this->getParam('iTagDisplayLimit', null));		
		
		if (!count($aRows))
		{
			return false;
		}

		if ($this->getParam('bIsProfile') === true && !defined('TAG_ITEM_ID'))
		{	
			foreach ($aRows as $iKey => $aRow)
			{
				$aRows[$iKey]['link'] = Phpfox::getService('user')->getLink($aUser['user_id'], $aUser['user_name'], array($sType, 'tag', $aRow['url']));
			}
		}
		
		if ($bNoTagBlock === false)
		{
			$this->template()->assign(array(
					'sHeader' => 'Trending Topics'
				)
			);
		}
		
		$this->template()->assign(array(
				'aRows' => $aRows,
				'sTagGlobalType' => $sType
			)
		);
		
		if ($sType === null)
		{
			$this->template()->assign(array(
					'sDeleteBlock' => 'dashboard'
				)
			);
		}				
		
		return 'block';	
	}
}

?>