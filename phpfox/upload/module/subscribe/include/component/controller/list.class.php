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
 * @package 		Phpfox_Component
 * @version 		$Id: list.class.php 1604 2010-05-31 06:42:26Z Raymond_Benc $
 */
class Subscribe_Component_Controller_List extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$this->template()->setTitle(Phpfox::getPhrase('subscribe.membership_packages'))
			->setBreadcrumb(Phpfox::getPhrase('subscribe.membership_packages'), $this->url()->makeUrl('subscribe'))
			->setBreadcrumb(Phpfox::getPhrase('subscribe.subscriptions'), $this->url()->makeUrl('subscribe.list'), true)
			->assign(array(
					'aPurchases' => Phpfox::getService('subscribe.purchase')->get(Phpfox::getUserId())
				)
			);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_list_clean')) ? eval($sPlugin) : false);
	}
}

?>