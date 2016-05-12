<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Friend
 * @version 		$Id: detail.class.php 254 2009-02-23 12:36:20Z Miguel_Espinoza $
 */
class Friend_Component_Block_Birthday extends Phpfox_Component
{
	public function process()
	{
		if (!Phpfox::getParam('friend.enable_birthday_notices'))
		{
			return false;
		}
		
		if (!Phpfox::isUser())
		{
			return false;
		}

		$aBirthdays = Phpfox::getService('friend')->getBirthdays(Phpfox::getuserId());

		$bIsEventSection = (Phpfox::getLib('module')->getFullControllerName() == 'event.index' ? true : false);
		if (!Phpfox::isModule('event'))
		{
			$bIsEventSection = true;
		}
		
		if ($bIsEventSection && empty($aBirthdays) && (Phpfox::getParam('friend.show_empty_birthdays') == false))
		{
			return false;
		}
		
		$aUpcomingEvents = array();
		if (!$bIsEventSection)
		{
			$this->search()->set(array(				
					'type' => 'event',
					'field' => 'm.event_id',				
					'search_tool' => array(
						'default_when' => 'upcoming',
						'when_field' => 'start_time',
						'when_upcoming' => true,
						'table_alias' => 'm',	
						'sort' => array(
							'latest' => array('m.start_time', 'Latest', 'ASC')						
						),
						'show' => array(5)
					)
				)
			);			
			
			$aBrowseParams = array(
				'module_id' => 'event',
				'alias' => 'm',
				'field' => 'event_id',
				'table' => Phpfox::getT('event'),
				'hide_view' => array('pending', 'my')
			);			
			
			$this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = 0');
			$this->search()->browse()->params($aBrowseParams)->execute();
			
			$aUpcomingEvents = $this->search()->browse()->getRows();
		}
		
		$this->template()->assign(array(
				'aUpcomingEvents' => $aUpcomingEvents,
				'aBirthdays' => $aBirthdays,
				'bIsEventSection' => $bIsEventSection,
				'sHeader' => ($bIsEventSection ? Phpfox::getPhrase('friend.birthdays') : Phpfox::getPhrase('event.upcoming_events')),
				//'sDeleteBlock' => 'dashboard'
			)
		);

		return 'block';
	}
}
?>
