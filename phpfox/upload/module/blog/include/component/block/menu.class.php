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
 * @package  		Module_Blog
 * @version 		$Id: menu.class.php 434 2009-04-21 08:33:22Z Raymond_Benc $
 */
class Blog_Component_Block_Menu extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			return false;
		}
		
		$aUser = $this->getParam('aUser');

		if ($aUser['user_id'] != Phpfox::getUserId())
		{
			return false;
		}
		
		// show the count for the drafts
		if (Phpfox::getParam('blog.show_drafts_count') && Phpfox::getUserParam('blog.show_drafts_count')) // check for the setting to be enabled here
		{
			$this->template()->assign(array(
				'iDraftsCount' => Phpfox::getService('blog')->getDraftsCount($aUser['user_id'])
				)
			);
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_block_menu_clean')) ? eval($sPlugin) : false);
	}
}

?>