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
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Friend_Component_Controller_Accept extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$iPage = $this->request()->getInt('page');
		$iLimit = Phpfox::getParam('friend.total_requests_display');
		$iRequestId = $this->request()->getInt('id');	
		
		list($iCnt, $aFriends) = Phpfox::getService('friend.request')->get($iPage, $iLimit, $iRequestId);
		
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
		
		Phpfox::getService('friend')->buildMenu();
		
		$this->setParam('global_moderation', array(
				'name' => 'friend',
				'ajax' => 'friend.moderation',
				'menu' => array(
					array(
						'phrase' => 'Accept',
						'action' => 'accept'
					),
					array(
						'phrase' => 'Deny',
						'action' => 'deny'
					)					
				)
			)
		);		
		
		$this->template()->setTitle(Phpfox::getPhrase('friend.friend_requests'))
			->setBreadcrumb('Friends', $this->url()->makeUrl('friend'))
			->assign(array(
				'aFriends' => $aFriends,
				'iRequestId' => $iRequestId,
				'bIsFriendController' => true
			)
		);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_controller_accept_clean')) ? eval($sPlugin) : false);
	}
}

?>