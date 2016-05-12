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
 * @package  		Module_Friend
 * @version 		$Id: ajax.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Friend_Component_Ajax_Ajax extends Phpfox_Ajax
{
	/**
	 * Shows the congratulate box for a birthday congratulation
	 */
	public function congratulate()
	{
		Phpfox::isUser(true);
		$iUser = (int)$this->get('iUser');
		Phpfox::getBlock('friend.congratulate', array('iUser' => $iUser));
	}

	/**
	 * Actually manages the sending of the message
	 */
	public function sendCongrats()
	{
		$aVal = $this->getAll('val');

		$iUser = (int) $aVal['val']['iUser'];
		$sMessage = $aVal['val']['message'];
		/* id for the egift*/
		$iEgift = $fCost = 0;
		if (isset($aVal['val']['egift_id']))
		{
			$iEgift = ((int) $aVal['val']['egift_id']);
			$fCost = Phpfox::getService('egift')->getCost($iEgift);
		}
		
		
		/* Add it to the database, but if its not free then hide it and dont send the notification */
		$mSent = Phpfox::getService('friend.process')->sendCongrats($iUser, $sMessage, $iEgift, $fCost);	
		/* $mSent is the invoice_id from egift_invoice*/
		if ($mSent > 0 && !is_bool($mSent))
		{
			Phpfox::getBlock('api.gateway.form',
							array('gateway_data' => array(
									'item_number' => 'egift|' . $mSent, 
									'currency_code' => Phpfox::getService('core.currency')->getDefault(),
									'amount' => $fCost,
									'item_name' => 'egift card with message: ' . $sMessage . '',
									'return' => Phpfox::getLib('url')->makeUrl('friend.invoice'),
									'recurring' => 0,
									'recurring_cost' => '',
									'alternative_cost' => 0,
									'alternative_recurring_cost' => 0
							)));
			$this->html('#TB_ajaxContent', $this->getContent(false));
		}
		else
		{
			$this->error(false);
			if ($mSent)
			{
				$this->height('#TB_ajaxContent', '30px')->html('#TB_ajaxContent', '<div class="valid_message">' . Phpfox::getPhrase('friend.your_message_has_been_sent') . '</div>')->call('setTimeout("tb_remove();", 1000);');
				$this->call('$("#js_congratulate_' . $iUser . '").hide("slow");');
				$this->hide('#js_form_congratulate');
				$this->alert(Phpfox::getPhrase('friend.your_message_has_been_sent'));
			}
			else
			{
				$sErrors = implode(',', Phpfox_Error::get());
				$this->call('$("#TB_ajaxContent").find(".error_message").remove();');
				$this->append('#TB_ajaxContent', '<div class="error_message">' . $sErrors . '</div>');
			}
		}
	}
	
	public function request()
	{
		Phpfox::isUser(true);	
		Phpfox::getUserParam('friend.can_add_friends', true);		
		
		$this->setTitle(Phpfox::getPhrase('friend.add_to_friends'));
		
		Phpfox::getBlock('friend.request', array('user_id' => $this->get('user_id')));			
		
		echo $this->template()->getHeader();
	}
	
	public function processRequest()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('friend.can_add_friends', true);
		
		if (Phpfox::getService('friend')->isFriend($this->get('user_id'), Phpfox::getUserId()))
		{
			return false;
		}
		
		$aVal = $this->get('val');
		if ($this->get('type') == 'yes')
		{
			if (Phpfox::getService('friend.process')->add(Phpfox::getUserId(), $this->get('user_id'), (isset($aVal['list_id']) ? (int) $aVal['list_id'] : 0)))
			{
				
			}
		}
		else 
		{
			if (Phpfox::getService('friend.process')->deny(Phpfox::getUserId(), $this->get('user_id')))
			{
				
			}			
		}
		
		if ($this->get('inline'))
		{
			$aUser = Phpfox::getService('user')->getUser($this->get('user_id'));
			
			if ($this->get('type') == 'yes')
			{	
				$this->addClass('.js_friend_request_' . $this->get('request_id'), 'row_moderate');
				$this->call('$(\'.js_friend_request_' . $this->get('request_id') . '\').find(\'.js_drop_data_add\').hide();');
				$this->call('$(\'.js_friend_request_' . $this->get('request_id') . '\').find(\'.extra_info_middot\').show();');				
			}
			else 
			{
				$this->remove('.js_friend_request_' . $this->get('request_id'));
			}		
		}
		else 
		{
			$this->call("tb_remove();");
		}
		
		if (isset($aVal['suggestion']))
		{
			// $this->loadSuggestion();
		}		
		
		$this->call('$Core.loadInit();');
		$this->remove('.js_profile_online_friend_request');
	}
	
	public function addRequest()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('friend.can_add_friends', true);
		
		$aVals = $this->get('val');		
		$aUser = Phpfox::getService('user')->getUser($aVals['user_id'], 'u.user_id, u.user_name, u.user_image, u.server_id');
		
		if (Phpfox::getUserId() === $aUser['user_id'])
		{
			return false;
		}
		elseif (Phpfox::getService('friend.request')->isRequested(Phpfox::getUserId(), $aUser['user_id']))
		{	
			return false;
		}		
		elseif (Phpfox::getService('friend.request')->isRequested($aUser['user_id'], Phpfox::getUserId()))
		{
			return false;
		}
		elseif (Phpfox::getService('friend')->isFriend($aUser['user_id'], Phpfox::getUserId()))
		{	
			return false;
		}
		
		if (Phpfox::getService('friend.request.process')->add(Phpfox::getUserId(), $aVals['user_id'], (isset($aVals['list_id']) ? $aVals['list_id'] : 0), $aVals['text']))
		{	
			if (isset($aVals['invite']))
			{
				$this->call('tb_remove();')->html('#js_invite_user_' . $aVals['user_id'], '' . Phpfox::getPhrase('friend.friend_request_successfully_sent') . '');	
			}			
			else 
			{
				$this->call('tb_remove(); $("#core_js_messages").message("' . Phpfox::getPhrase('friend.friend_request_successfully_sent') . '", "valid").slideDown("slow").fadeOut(5000);');
				$this->remove('#js_add_friend_on_profile');
			}			
			
			if (isset($aVals['suggestion']))
			{				
				$this->loadSuggestion(true);
			}
			
			if (isset($aVals['page_suggestion']))
			{
				$this->hide('#js_suggestion_parent_' . $aVals['user_id']);
			}
		}			
	}
	
	public function addList()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('friend.can_add_folders', true);
		
		$sName = $this->get('name');

		if (Phpfox::getLib('parse.format')->isEmpty($sName))
		{
			$this->html('#js_friend_list_add_error', Phpfox::getPhrase('friend.provide_a_name_for_your_list'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
		}
		elseif (Phpfox::getService('friend.list')->reachedLimit()) // Did they reach their limit?
		{
			$this->html('#js_friend_list_add_error', Phpfox::getPhrase('friend.you_have_reached_your_limit'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
		}			
		elseif (Phpfox::getService('friend.list')->isFolder($sName))
		{
			$this->html('#js_friend_list_add_error', Phpfox::getPhrase('mail.folder_already_use'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
		}
		else 
		{
			if ($iId = Phpfox::getService('friend.list.process')->add($sName))
			{
				if ($this->get('custom'))
				{
					$this->hide('#js_create_custom_friend_list')->show('#js_add_friends_to_list')->val('#js_custom_friend_list_id', $iId);
				}
				else 
				{
					$this->call('js_box_remove($(\'#js_friend_list_add_error\'));');
					$this->alert(Phpfox::getPhrase('friend.list_successfully_created'), Phpfox::getPhrase('friend.create_new_list'), 400, 150, true);
					$this->call('$Core.friend.addNewList(' . (int) $iId . ', \'' . str_replace("'", "\\'", Phpfox::getLib('parse.input')->clean($sName)) . '\');');
					$this->append('.sub_section_menu ul', '<li><a href="' . Phpfox::getLib('url')->makeUrl('friend', array('view' => 'list', 'id' => $iId)) . '">' . str_replace("'", "\\'", Phpfox::getLib('parse.input')->clean($sName)) . '</a></li>');
				}
			}
		}
	}

	public function addNewList()
	{
		$this->setTitle(Phpfox::getPhrase('friend.create_new_list'));
		
		Phpfox::getBlock('friend.list.add');
	}
	
	public function buildCache()
	{
		$this->call('$Cache.friends = ' . json_encode(Phpfox::getService('friend')->getFromCache($this->get('allow_custom'))) . ';');
	}
	
	public function updateOrder()
	{
		Phpfox::isUser(true);
		
		Phpfox::getService('friend.process')->updateOrder($this->get('user'));
		
		Phpfox::getBlock('friend.top', array(
				'bIsAjax' => true
			)
		);
				
		$this->call("$('#sJsBlockBorder_TopFriends').find('.block_content').html('" . $this->getContent() . "');");
		$this->call("$('#sJsBlockBorder_TopFriends').find('h3').html('" . Phpfox::getPhrase('friend.top_friends', array('phpfox_squote' => true)) . "');");
	}
	
	public function addTop()
	{
		Phpfox::isUser(true);
		
		Phpfox::getService('friend.process')->toggleTop($this->get('id'));		
		Phpfox::getBlock('friend.top', array(
				'bIsAjax' => true
			)
		);
		
		$sPath = Phpfox::getLib('template')->getStyle('image', 'misc/favorite.png');
		$sLink = '<a href="#" onclick="$.ajaxCall(\'friend.deleteTop\', \'id=' . $this->get('id') . '\'); return false;" title="' . Phpfox::getPhrase('friend.remove_from_your_top_friends_list') . '"><img src="' . $sPath . '" style="vertical-align:middle;" /></a>';
				
		$this->call("$('#js_block_border_friend_top').find('.content').html('" . $this->getContent() . "'); $('#js_add_top_fav_link_" . $this->get('id') . "').html('" . str_replace("'", "\'", $sLink) . "'); $Behavior.orderTopFriends();");		
	}
	
	public function deleteTop()
	{
		Phpfox::isUser(true);
		
		Phpfox::getService('friend.process')->toggleTop($this->get('id'), true);
		Phpfox::getBlock('friend.top', array(
			'bIsAjax' => true
		));
		
		$sPath = Phpfox::getLib('template')->getStyle('image', 'misc/favorite_add.png');
		$sLink = '<a href="#"" onclick="$.ajaxCall(\'friend.addTop\', \'id=' . $this->get('id') . '\'); return false;" title="' . Phpfox::getPhrase('friend.add_to_your_top_friends_list') . '"><img src="' . $sPath . '" style="vertical-align:middle;" /></a>';		
				
		$this->call("$('#js_block_border_friend_top').find('.content').html('" . $this->getContent() . "'); $('#js_add_top_fav_link_" . $this->get('id') . "').html('" . str_replace("'", "\'", $sLink) . "'); $Behavior.orderTopFriends();");			
	}
	
	public function editLists()
	{
		Phpfox::getBlock('friend.list.edit');
		$this->call("$('#js_friend_box_lists').hide();");			
		$this->html('#js_edit_lists', $this->getContent(false));	
		$this->call('$Core.loadInit();');		
	}
	
	public function updateList()
	{
		if (Phpfox::getService('friend.list.process')->update($this->get('list_id'), $this->get('name')))
		{
			$this->call('$Core.friend.updateListTitle(\'' . str_replace("'", "\\'", Phpfox::getLib('parse.input')->clean($this->get('name'), 255)) . '\');');	
		}		
	}
	
	public function deleteList()
	{
		if (Phpfox::getService('friend.list.process')->delete($this->get('id')))
		{
			$this->call("$('#js_edit_input_list_" . $this->get('id') . "').hide();");
			$this->call("$('.sJsList_" . $this->get('id') . "').hide(); $Core.loadInit();");
		}
	}

	public function move()
	{
		Phpfox::isUser(true);
		
		$aVals = $this->get('val');
		if (Phpfox::getService('friend.list.process')->move($this->get('list'), $aVals['id']))
		{
			Phpfox::addMessage(Phpfox::getPhrase('friend.friends_successfully_moved'));
			
			$this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('friend', array('view' => 'list', 'id' => $this->get('list'))) . '\';');	
		}		
	}
	
	public function delete()
	{		
		if (Phpfox::getService('friend.process')->delete($this->get('id')))
		{			
			$this->call('$("#js_friend_' . $this->get('id') . '").remove();');
			$this->alert(Phpfox::getPhrase('friend.friend_successfully_removed'), Phpfox::getPhrase('friend.remove_friend'), 300, 150, true);
		}	
	}
	
	public function search()
	{
		Phpfox::getBlock('friend.search', array('input' => $this->get('input'), 'friend_module_id' => $this->get('friend_module_id'), 'friend_item_id' => $this->get('friend_item_id'), 'type' => $this->get('type')));
		if ($this->get('type') == 'mail')
		{
			$this->call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . Phpfox::getPhrase('friend.search_for_members', array('phpfox_squote' => true)) . '\');</script>');
		}
		else 
		{			
			$this->call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . Phpfox::getPhrase('friend.search_for_your_friends', array('phpfox_squote' => true)) . '\');</script>');
		}
	}
	
	public function searchAjax()
	{		
		Phpfox::getBlock('friend.search', array('search' => true, 'friend_module_id' => $this->get('friend_module_id'), 'friend_item_id' => $this->get('friend_item_id'), 'page' => $this->get('page'), 'find' => $this->get('find'), 'letter' => $this->get('letter'), 'input' => $this->get('input'), 'view' => $this->get('view'), 'type' => $this->get('type')));
		
		$this->call('$(\'#js_friend_search_content\').html(\'' . $this->getContent() . '\'); updateFriendsList();');		
	}
	
	public function searchDropDown()
	{
		Phpfox::isUser(true);
		$oDb = Phpfox::getLib('database');
		$sFind = $this->get('search');
		if (empty($sFind))
		{
			$iCnt = 0;
		}
		else 
		{
			list($iCnt, $aFriends) = Phpfox::getService('friend')->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId() . ' AND (u.full_name LIKE \'%' . $oDb->escape($sFind) . '%\' OR (u.email LIKE \'%' . $oDb->escape($sFind) . '@%\' OR u.email = \'' . $oDb->escape($sFind) . '\'))', 'friend.time_stamp DESC', 0, 10, true, true);
		}
		
		if ($iCnt)
		{
			$sHtml = '';
			foreach ($aFriends as $aFriend)
			{
				$sHtml .= '<li><a href="#" onclick="$(\'#' . $this->get('div_id') . '\').parent().hide(); $(\'#' . $this->get('input_id') . '\').val(\'' . $aFriend['user_id'] . '\'); $(\'#' . $this->get('text_id') . '\').val(\'' . $aFriend['full_name'] . '\'); return false;">' . Phpfox::getLib('parse.output')->shorten(Phpfox::getLib('parse.output')->clean($aFriend['full_name']), 40, '...') . '</a></li>';
			}
			$this->html('#' . $this->get('div_id'), '<ul>' . $sHtml . '</ul>');
			$this->call('$(\'#' . $this->get('div_id') . '\').parent().show();');
		}
		else 
		{
			$this->html('#' . $this->get('div_id'), '');
			$this->call('$(\'#' . $this->get('div_id') . '\').parent().hide();');
		}
	}
	
	public function getEditBar()
	{
		Phpfox::getBlock('friend.setting');
		$this->html('#js_edit_block_' . $this->get('block_id'), $this->getContent(false))->slideDown('#js_edit_block_' . $this->get('block_id'));
	}	
	
	public function loadSuggestion($bLoadTemplate = true)
	{		
		Phpfox::getBlock('friend.suggestion', 'reload=true');
		
		if ($bLoadTemplate === true)
		{
			Phpfox::getLib('template')->getTemplate('friend.block.suggestion');
		}
		
		$this->slideUp('#js_friend_suggestion_loader')->html('#js_friend_suggestion', $this->getContent(false))->slideDown('#js_friend_suggestion');	
		$this->call('$Core.loadInit();');	
	}
	
	public function removeSuggestion()
	{		
		if (Phpfox::getService('friend.suggestion')->remove($this->get('user_id')))
		{
			if ($this->get('load'))
			{
				$this->loadSuggestion();	
			}			
		}
	}
	
	public function addFriendsToList()
	{
		if (Phpfox::getService('friend.list.process')->addFriendsToList((int) $this->get('list_id'), (array) $this->get('friends')))
		{
			Phpfox::getBlock('privacy.friend', array('bNoCustomDiv' => true, 'list_id' => (int) $this->get('list_id')));					
			
			$this->html('#js_custom_friend_list', $this->getContent(false));				
		}
	}
	
	public function manageList()
	{
		Phpfox::isUser(true);
		
		if ($this->get('type') == 'add')
		{
			Phpfox::getService('friend.list.process')->addFriendsTolist($this->get('list_id'), $this->get('friend_id'));			
		}
		else
		{
			Phpfox::getService('friend.list.process')->removeFriendsFromlist($this->get('list_id'), $this->get('friend_id'));
		}
	}
	
	public function setProfileList()
	{
		Phpfox::isUser(true);
		
		if ($this->get('type') == 'add')
		{
			if (Phpfox::getService('friend.list.process')->addListToProfile($this->get('list_id')))
			{
				$this->call('$(\'.friend_list_display_profile\').parent().hide();');
				$this->call('$(\'.friend_list_remove_profile\').parent().show();');
				$this->alert(Phpfox::getPhrase('friend.successfully_added_this_list_to_your_profile'), Phpfox::getPhrase('friend.profile_friend_lists'), 300, 150, true);								
			}
		}
		else
		{
			if (Phpfox::getService('friend.list.process')->removeListFromProfile($this->get('list_id')))
			{
				$this->call('$(\'.friend_list_display_profile\').parent().show();');
				$this->call('$(\'.friend_list_remove_profile\').parent().hide();');
			}
		}
	}
	
	public function updateListOrder()
	{
		Phpfox::isUser(true);

		if (Phpfox::getService('friend.list.process')->updateListOrder($this->get('list_id'), $this->get('friend_id')))
		{
			$this->alert(Phpfox::getPhrase('friend.order_successfully_saved'), Phpfox::getPhrase('friend.list_order'), 400, 150, true);
			$this->call('$Core.processForm(\'#js_friend_list_order_form\', true);');
		}
	}
	
	public function viewMoreFriends()
	{
		Phpfox::getComponent('friend.index', array(), 'controller');
		
		// $this->remove('.js_friend_edit_order_submit');
		$this->remove('.js_pager_view_more_link');
		$this->append('#js_view_more_friends', $this->getContent(false));
		$this->call('$Core.loadInit();');		
	}
	
	public function getRequests()
	{
		if (!Phpfox::isUser())
		{
			$this->call('<script type="text/javascript">window.location.href = \'' . Phpfox::getLib('url')->makeUrl('user.login') . '\';</script>');
		}
		else
		{
			Phpfox::getBlock('friend.accept');		
		}
	}
	
	public function getMutualFriends()
	{
		Phpfox::isUser(true);
		if ((int) $this->get('page') == 0)
		{
			$this->setTitle(Phpfox::getPhrase('friend.mutual_friends'));
		}
		Phpfox::getBlock('friend.mutual-browse');	
		
		if ((int) $this->get('page') > 0)
		{
			$this->remove('#js_friend_mutual_browse_append_pager');
			$this->append('#js_friend_mutual_browse_append', $this->getContent(false));
		}
	}
	
	public function moderation()
	{
		Phpfox::isUser(true);
				
		switch ($this->get('action'))
		{
			case 'accept':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					if (($aRequest = Phpfox::getService('friend.request')->getRequest($iId)) === false)
					{
						continue;
					}
					
					Phpfox::getService('friend.process')->add(Phpfox::getUserId(), $aRequest['friend_user_id']);
						
					$this->slideUp('.js_friend_request_' . $iId);					
				}				
				$this->updateCount();
				$sMessage = 'Friend Request(s) successfully confirmed.';
				break;			
			case 'deny':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					if (($aRequest = Phpfox::getService('friend.request')->getRequest($iId)) === false)
					{
						continue;
					}				
					
					Phpfox::getService('friend.process')->deny(Phpfox::getUserId(), $aRequest['friend_user_id']);
					
					$this->slideUp('.js_friend_request_' . $iId);
				}				
				$sMessage = 'Friend Request(s) successfully denied.';
				break;
		}
		
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');			
	}	
}

?>