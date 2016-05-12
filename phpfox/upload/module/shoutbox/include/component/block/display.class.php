<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Block that displays a shoutbox anywhere on the site depending on 
 * where it is placed.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Shoutbox
 * @version 		$Id: display.class.php 1388 2010-01-11 20:17:18Z Raymond_Benc $
 */
class Shoutbox_Component_Block_Display extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// Make sure the user group viewing this block is actually allowed to view it		
		if (!Phpfox::getUserParam('shoutbox.can_view_shoutbox'))
		{
			return false;
		}		
	
		$aCallback = $this->getParam('aCallbackShoutbox', array());
		
		if (isset($aCallback['module']) && $aCallback['module'] == 'group' && !Phpfox::getService('group')->hasAccess($aCallback['item'], 'can_use_shoutbox'))
		{
			return false;
		}
		
		// Assign the vars to the template
		$this->template()->assign(array(
				'sHeader' => Phpfox::getPhrase('shoutbox.shoutbox'),
				'aShoutouts' => Phpfox::getService('shoutbox')->callback($aCallback)->getMessages(Phpfox::getParam('shoutbox.shoutbox_display_limit')),
				'iShoutboxRefresh' => (Phpfox::getParam('shoutbox.shoutbox_refresh') * 1000),
				'iShoutoutWordWrap' => Phpfox::getParam('shoutbox.shoutbox_wordwrap'),				
				'aCallbackShoutbox' => $aCallback
			)
		);
		
		// $this->template()->assign('sDeleteBlock', 'dashboard');

		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('shoutbox.component_controller_index_clean')) ? eval($sPlugin) : false);
		
		// Remove template vars from memory
		$this->template()->clean(array(
				'aShoutouts',
				'iShoutboxRefresh',
				'iShoutoutWordWrap'
			)
		);
	}	
}

?>