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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 3349 2011-10-25 09:10:31Z Miguel_Espinoza $
 */
class Im_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function load()
	{	
		
		if ( (Phpfox::getUserBy('im_hide') == 1 && $this->get('doEnable') != 1) )
		{
			return;
		}
		$this->hide('#js_im_ajax_button');
		Phpfox::getBlock('im.messenger');		
		
		$this->html('#main_messenger_holder', $this->getContent(false));
		$this->call('setCookie(\'im_last_open_window\', \'messenger\');');	
		
		/* Hide the offlines and show the onlines, not sure this should be here...*/
		$this->call('$("#js_im_status_online, #js_im_display_online").show();$("#js_im_display_offline, #js_im_status_offline").hide();');
		
		/* Allow future updates */
		$this->call('bIsOnline = true;bIsWaiting = false;');
		
		//$this->call('$(".js_cache_im_room").each(function(){$(this).remove();});');
		$this->html('#im_chats_lists','');
		
		/* We need to get all the online conversations we have open */
		$oBlockImChat = Phpfox::getBlock('im.user');
		
		/* that block should have everything so lets repopulate the footer bar */
		$this->after('#im_chats_lists', $this->getContent(false));
		
		/* Check if we have enough room */
		$this->call('goFullHeight();');
		// Display the messenger only if the user has not minimized it 		
		$sShow = 'if (getCookie("js_im_open") == 1){';
		$sShow .= '$("#main_messenger_holder").show();';
		/* add the seen class */
		$sShow .= '$("#main_messenger_link").find(\'#js_instant_messenger_link\').addClass("seen");';
		$sShow .= '}else{';
		$sShow .= '$("#main_messenger_holder").hide();';
		$sShow .= '}';
		$this->call($sShow);
		
		/* Hide all the conversations open */
		//$this->call('$(".js_messages").hide();');
		//Phpfox::getService('im.process')->hideRoom('all');
		
	}
	
	public function hideRoom()
	{		
		PHpfox::getService('im.process')->hideRoom($this->get('id'));
		$this->call('deleteCookie(\'im_last_open_window\');');
	}
	
	public function block()
	{
		Phpfox::getService('im.process')->block($this->get('id'));		
		
		$this->call('deleteCookie(\'im_last_open_window\');');
	}

	public function close()
	{
		Phpfox::getService('im.process')->close($this->get('id'));				
		$this->call('deleteCookie(\'im_last_open_window\');');
	}
	
	/**
	 * This function is called when starting a conversation when someone
	 */
	public function chat()
	{
		list($iRoomId, $sName, $bIsAlreadyOpen) = Phpfox::getService('im.process')->add($this->get('user_id'));
		/*when opening a conversation this is the function being called and bIsAlreadyOpen is true when 
			going offline + going online + starting a new conversation. (dont know why but shouldnt be)
		*/
		$aOrder = $this->get('order');
		if (!is_array($aOrder))
		{
			$aOrder = array();
		}
		$aOrder[$iRoomId] = '';
		Phpfox::getService('im.process')->updateOrder($aOrder);				
				
		/* we remove the room every time to create it anew */
		$this->call('$("#js_cache_im_room_'.$iRoomId.'").remove();');
		
		$this->template()->assign(array(
					'aRoom' => Phpfox::getService('im')->getChat($iRoomId),
					'bIsNewRoom' => true
						)
				)
				->getTemplate('im.block.link');

		$sContent = $this->getContent(true);
		/* Logic to show the new chat always to the left */
		$sJs = 'if($(".js_cache_im_room").length > 0){';
		$sJs .= '$(".js_cache_im_room:first").before("'.$sContent .'");}';
		$sJs .= 'else{';
		$sJs .= '$("#js_im_holder").before("'.$sContent .'");}';
		$this->call($sJs)//->before('#js_im_holder', $this->getContent(false))
				/* Hide the main IM only if not in fullHeight*/
				->call('if ($(\'body\').width() < iMinWidth){ $("#js_footer_im_holder").hide(); }');
			
		/* open the messages part of the chat right away */
		$this->set(array('id' => $iRoomId));
		$this->open();
		
		$this->call('doScroll('.$iRoomId.', true);');
		$this->call('bIsOnline = true;');
		$this->call('setCookie(\'im_last_open_window\', \'chat_' . $iRoomId . '\');');
		/* fix highlighting, not fond of doing it an ajax response */
		$this->call('$(".seen").removeClass("seen");');
		$this->call('$("#js_cache_im_room_'.$iRoomId.'").addClass("seen");');
	}	
	
	/* Called from im.link when clicking on it via clickOnLink()*/
	public function open()
	{			
		$oBlockImChat = Phpfox::getBlock('im.chat', array(
				'im_id' => $this->get('id')
			)
		);
		
		/* The following updates `phpfox_im`.`is_active` to show the last opened IM messages */
		Phpfox::getService('im.process')->openChat($this->get('id'));
		
		if ($oBlockImChat->isLive() === true)
		{			
			Phpfox::getService('im.process')->isSeen($this->get('id'));
			
			$this->html('#js_messages_' . $this->get('id'), $this->getContent(false));
			/* Minimize other chat windows -where the messages- */
			$this->call('$(".js_messages").hide();');
			// Show the messages for this conversation 
			$this->call('$("#js_messages_'.$this->get('id').'").show();');
			$this->call('$(\'#js_cache_im_room_' . $this->get('id') . '\').find(\'.im_ajax_button:first\').hide();')
				->call('$(\'#js_cache_im_room_' . $this->get('id') . '\').find(\'.im_delete_button:first\').show();');
			
			/* Lets make sure that the newly open conversation scrolls to the bottom */
			$this->call('doScroll('. $this->get('id') .',true);');
			$this->call('setCookie(\'im_last_open_window\', \'chat_' . $this->get('id') . '\');');
		}
		$this->call('deleteCookie(\'im_last_open_window\');');		
	}
	
	
	
	public function add()
	{
		$aVals = $this->get('val');
		
		if (Phpfox::getService('im.process')->addText($aVals))
		{							
			$this->template()->assign(array(
						'aMessage' => array(
							'time_stamp' => PHPFOX_TIME,
							'full_name' => Phpfox::getUserBy('full_name'),
							'user_name' => Phpfox::getUserBy('user_name'),
							'server_id' => Phpfox::getUserBy('server_id'),
							'user_image' => Phpfox::getUserBy('user_image'),
							'user_id' => Phpfox::getUserId(),
							'text' => $aVals['text'],
							'last_owner' => 0
						),
						'sClass' => 'im_sending_message'
					)
				)
				->getTemplate('im.block.text');
			
			/* We need to check if the input is different in case the user is entering text 
			 * faster than the response arrives
			 */
			$this->call('if ($("#js_im_text").val().replace(/(\r\n|\n|\r)/gm,"") == "'. trim($aVals['text']) .'") {$("#js_im_text").val("");}else{}');
			$this//->val('#js_im_text', '')
				->append('#js_im_messages_' . $aVals['parent_id'], $this->getContent(false))
				->call('doScroll('.$aVals['parent_id'] .', true);');
		}
	}
	
	
	
	/* This function is to replace the ajax call to im.getMessages and im.getRooms
	 * Every call to im.getMessages or im.getRooms should be changed to getUpdate
	 */
	/* 
	 * @TODO
	 * Bugs:
	 * 		[Fixed] If a user goes offline and then back online (in the IM)
	 * 			+ the list of messages for the previous user goes to the 
	 * 				far left
	 * 			+ And it is duplicated on top of the chat menu
	 * 		[Fixed] Sending a message
	 * 			+ Duplicates it in the output text area (when sending 
	 * 				and when reading it)
	 * 		[Fixed] When a new friend comes online and types, it does not show
	 * 			a visual notification
	 *		[Fixed] Sometimes when going offline and back online it changes to display that
	 *			we are offline but we are actually online
	 *		[Fixed] Sometimes getUpdate stops executing, probably the variable fails.
	 *		[Fixed] The class_text_first overwrites the im_text  
	 *		[Fixed] When starting a conversation if the chat is already open it does not 
	 *			gives focus, adds the class or shows the conversation
	 * 		[Fixed] Doesnt realize when someone comes online
	 * 		[Fixed] Issues two calls in the same ajax response when updating the 
	 * 			online users
	 *		[Fixed] When loading the page and being offline from IM it still calls 
	 *			getUpdate in a loop.
	 * 		[Fixed] When user is offline and comes back online it does not update 
	 * 				the logo or the text inside the IM menu ("Offline")
	 *		[Fixed] When loading for the first time it shows the im.link even if there
	 *				are no new messages or users online
	 *		[Fixed] Setting users online is happening in 2 places, need to centralize, it happens
	 *			via direct ajax call $(#js_... and by calling setOnlineFriends([...
	 *		[Fixed] When closing a conversation it opens a previously closed one
	 *		[Fixed] We are getting the yellow highlight way too often, like when getting a 
	 *			conversation with someone else
	 *		[Fixed] Since the im.link is being destroyed and re-created the chat messages sometimes
	 *			aligns to another conversation
	 *		[Done] Close the im.link more often
	 *		[Fixed] When starting a new conversation the messages must show right away
	 *		[Fixed] Change width of messages and im content to 200px
	 *		[Yeap] Add visual feedback that the message is being sent
	 *		[Fixed] Append new message (not with weird feedback "sending")
	 *			= The ajax response adds it and the get update removes the last message	 * 
	 *		[Fixed] Yellow highlight is for all the div, nod just the 'a' when getting new message
	 *		[Fixed] The message box does not need to be all the way down, undo that
	 *		[Fixed] new conversations should go to the left, not to the right
	 *		[Fixed]	When a conversation is open, a new one arrives, and we click the new one
	 *				the messages are shown on top of the previous conversation
	 *		[Done] The Chat menu link in footer should have a width of 200px like the menu itself
	 *		[Fixed] When a chat or menu has focus the link in the bottom should change background
	 *			to white
	 *		[Fixed] Add smart auto scrolling, if the user is reading an old message do not scroll, if the
	 *			user is reading the last message received and a new one arrives then do scroll
	 *		[Fixed] classes on im.link are a little off
	 *			The last active chat should have the white background on Link, including the 
	 *			im.menu
	 *		[Fixed] Refresing the main chat block keeps the user from searching friends, move the friends search
	 *			down and onKeyUp when searching friends abort the ajax connection
	 *			When user starts a new chat or erases the friend being searched then we start 
	 *			getUpdate again.
	 *		[Done] Ditch the Hide 
	 *		[Fixed] Clicking on any part of the im.link should trigger the main action (not just on the link)
	 *			Done for the js_im_holder
	 *		[Fixed] Move js_im_friend_list to the bottom, like on FB
	 *		[Fixed] Reloading the page should keep conversations open
	 *		[Fixed] Cursor is wrong when hovering over messages
	 *		[Fixed] Doesnt remember that we closed a chat
	 *		[Fixed] Remove class seen from main messenger link when closing or minimizing
	 *		[Fixed] IM is showing when not logged in
	 *		[Fixed] main im block should always show when in full height unless they sign off.
	 *		[Fixed] Fix the minimum width for the full Height
	 *		[Fixed] Not showing new messages if the im.link is closed
	 *		[Fixed] Not `remembering` the full height mode
	 *		[Fixed] Sender sees double their post
	 *		[Fixed] When in fullHeight minimizing means going offline and it should not happen when
	 *			opening a new conversation
	 *		Green availability icon on im.links is not updating properly when there are 
	 *			more than one user online
	 *		When there's more than one user online it forgets which chat I had open
	 */

	public function getUpdate()
	{
		/* Get update must do both, get the rooms and get the messages for each room
		 * to save on resources this function is reimplementing these tasks instead 
		 * of plainly calling them (the params may interfere, for example getMessages
		 * needs a room id by default)
		 */

		/* Check if we are online */
		if (Phpfox::getService('im')->isUserOnline(Phpfox::getUserId()) == false)
		{
			$this->call('bIsOnline = false; bIsWaiting = false;');
			return false;
		}
		
		/* This array has the last message received for each room*/
		$aLastMessage = $this->get('aRoom');
		/* Open chat rooms */
		$aOpenChatRooms = explode(',',$this->get('openRooms'));	
		
		/* Refresh the list of online users */
		$oImList = Phpfox::getBlock('im.list');
		$this->call('$("#js_im_friend_list").html("' . $this->getContent() . '");');
		/* We use this variable to update the users online in the IM and
		 * internally to tell when to send the update
		 */
		$aUpdateOnline = array();
			
		/* Remove sending messages */
		$this->call('$(".im_sending_message").remove();');
		$iUpdateOnlineCount = null;
		/* this is how we do the long polling */
		for ($i = 0; $i < Phpfox::getParam('im.im_php_loops'); $i++)
		{
			/* Used when an update is found and we want to break from the for */
			$bBreak = false;
			/* Gets the chat conversations active for the current user, even the
			 * ones this user has not seen (user just came online) */
			$aRooms = Phpfox::getService('im')->getRooms();
			/* get the newest online friends */			
			$aCond = array('AND f.user_id = ' . Phpfox::getUserId() . ' AND u.im_hide != 1');			
			list($iCnt, $aFriends) = Phpfox::getService('im')->getOnlineFriends(Phpfox::getUserId(), $aCond);
			$iUpdateOnlineCount = $iCnt;
			
			/* Quick check if the number of online friends from before is different than 
			 * the actual we call im.load right away */
			if (count($aFriends) != count($oImList->_aFriends))
			{
				$this->load();
				$bBreak = true;
				$oImList->_aFriends = $aFriends;
			}
			else
			{
				/* but if the count is the same we need to make sure that the friends
				 * that were online are the same that are now online 
				 */
				foreach ($aFriends as $aFriend)
				{
					$bMatched = false;
					foreach ($oImList->_aFriends as $aCacheFriend)
					{			
						if ($aFriend['user_id'] == $aCacheFriend['user_id'])
						{
							$bMatched = true;
						}
					}
					/* If this list is different then we call im.load */
					if ($bMatched == false)
					{
						$this->load();
						$bBreak = true;
						$oImList->_aFriends = $aFriends;
						break;
					}
				}				
			}

			foreach ($aRooms as $aRoom)
			{
								
				/* Figure out if a user has gone online or offline */
				/* We have the list of friends in $oImList->_aFriends and we 
				 * have the is_logged_in in $aRoom which tells us if this user is logged
				 * in, so we match both and if $oImList tells is online we update it
				 */
				foreach ($oImList->_aFriends as $aFriend)
				{
					if ($aFriend['user_id'] == $aRoom['is_logged_in'])
					{						
						/* Make sure this room is set as online */						
						$aUpdateOnline[$aRoom['parent_id']] = $aRoom['parent_id'];
						/* sUpdateOnline has changed*/
					}
				}			
				/* Lets get the messages for this chat room */
				$iLastMessage = Phpfox::getService('im')->getLastMessageForRoom($aRoom['parent_id']);
				
				$aMessages = Phpfox::getService('im')->getMessages($aRoom['parent_id'], $iLastMessage);
								
				foreach ($aMessages as $iKey => $aMessage)
				{	
					$aMessages[$iKey]['room_is_active'] = $aRoom['is_active'];					
				}
				
				/* if this room is active and is a page refresh we need to add its im.link*/
				if ($this->get('bIsNew') == 1)
				{
					if ($aRoom['is_active'] > 0)
					{
						$this->template()->assign(array(
									'aRoom' => $aRoom,
									'bIsNewRoom' => true
										)
								)
								->getTemplate('im.block.link');					
						$this->call('addImLink('.$aRoom['parent_id'].',\'' . $this->getContent(true) . '\');');
						$bBreak = true;
						
						/* show the messages div */
						if ($aRoom['is_active'] == 2)
						{
							$this->set(array('id' => $aRoom['parent_id']));
							$this->open();
							/* add the css classes */
							$this->call('$(".seen").removeClass("seen");$("#js_cache_im_room_'.$aRoom['parent_id'] .'").addClass("seen");');
						}
					}					
				}
				if (!empty($aMessages))
				{											
					/* if bIsNew == 1 we do not show the im.link because its only loading */
					if ($this->get('bIsNew') != 1)
					{						
						/* Lets try creating the im.link every time */
						$this->template()->assign(array(
									'aRoom' => $aRoom,
									'bIsNewRoom' => true
										)
								)
								->getTemplate('im.block.link');
						
						$this->call('addImLink('.$aRoom['parent_id'].',\'' . $this->getContent(true) . '\');')
								->call('$("#js_chat_room_' . $aRoom['parent_id'] . '").show();');						
						/* Crash control (opening the same im.link) is handled by im.js */
					}

					/* the bIsFirst is used to control the im_text_first class on the 
					 * message */
					/* im.message is only the messages for this room, not the link in the bottom */
					Phpfox::getBlock('im.message', array(
						'aRoom' => $aRoom,
						'aMessages' => $aMessages,
						'aChat' => $aRoom,
						'bIsFirst' => false
					));

					$bForce = $this->get('bIsNew') == 1;
					/* If we sent one of the messages then we scroll to the bottom */
					foreach ($aMessages as $aMessage)
					{
						if ($aMessage['user_id'] == Phpfox::getUserId())
						{
							$bForce = true;
						}
					}
					$sRoomContent = $this->getContent();
					
					$sCall = 'showMessage(' . $aRoom['parent_id'] . ',"' . $sRoomContent . '", ' . ($bForce ? 'true' : 'false').');';
					$sCall .= 'doScroll(' . $aRoom['parent_id'] .');';
					$this->call($sCall);
					
					$bBreak = true;
				}
			}
			
			if ($bBreak == true)
			{
				break;
			}
			else
			{
				/* The long polling effect: */
				sleep(Phpfox::getParam('im.im_php_sleep'));
			}
			
		}					
			
		/* Update online rooms */
		$sRooms = '[';
		/* Go through the list of friends that are online*/
		foreach ($aRooms as $aRoom)
		{
			foreach ($oImList->_aFriends as $aFriend)
			{
				if ($aRoom['im_hide2'] != 1 && $aFriend['user_id'] == $aRoom['owner_user_id'])
				{
					$sRooms .= $aRoom['parent_id'] . ',';
				}
			}
		}
		$sRooms = rtrim($sRooms, ',');
		
		/* setOnlineFriends no longer updates #js_im_total_friend_count */
		$this->call('$Core.im.setOnlineFriends('. $sRooms .']);');			
		$this->html('#js_im_total_friend_count', count($aFriends));
		/* Release the lock for next iteration */
		$this->call('bIsWaiting = false;getUpdate();');
	}
	public function goOffline()
	{
		Phpfox::getService('im.process')->goOffline();
		$this->call('bIsOnline = false;bIsWaiting=true;');
	}
	
	public function changeStatus()
	{
		Phpfox::getService('im.process')->changeStatus($this->get('status'));
	}
	
	public function playSound()
	{
		Phpfox::getService('im.process')->playSound($this->get('im_beep'));	
	}
	
	public function searchFriends()
	{
		Phpfox::getBlock('im.list', array(
				'im_display_ul' => false
			)
		);
		
		$this->html('#js_im_user_list', $this->getContent(false));
	}
}

?>