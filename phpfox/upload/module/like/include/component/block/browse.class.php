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
 * @package 		Phpfox_Component
 * @version 		$Id: browse.class.php 3048 2011-09-08 18:33:51Z Raymond_Benc $
 */
class Like_Component_Block_Browse extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aLikes = Phpfox::getService('like')->getLikes($this->request()->get('type_id'), $this->request()->getInt('item_id'));
	
		$sErrorMessage = '';
		if ($this->request()->getInt('type_id') == 'pages')
		{
			$aPage = Phpfox::getService('pages')->getPage($this->request()->getInt('item_id'));
			if (!count($aLikes))
			{
				if ($aPage['type_id'] == 3)
				{
					$sErrorMessage = 'This group has no members.';				
				}
				else
				{
					$sErrorMessage = 'Nobody likes this.';
				}
			}
		}
		
		$this->template()->assign(array(
				'aLikes' => $aLikes,
				'sErrorMessage' => $sErrorMessage
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('like.component_block_browse_clean')) ? eval($sPlugin) : false);
	}
}

?>