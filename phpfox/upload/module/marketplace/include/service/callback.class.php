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
 * @package 		Phpfox_Service
 * @version 		$Id: callback.class.php 3358 2011-10-25 15:00:49Z Raymond_Benc $
 */
class Marketplace_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('marketplace');
	}
	
	public function mobileMenu()
	{
		return array(
			'phrase' => 'Marketplace',
			'link' => Phpfox::getLib('url')->makeUrl('marketplace'),
			'icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'mobile/small_marketplace.png'))
		);
	}	

	public function enableSponsor($aParams)
	{
	    return Phpfox::getService('marketplace.process')->sponsor($aParams['item_id'], 1);
	    $this->database()->update(Phpfox::getT('marketplace'),
		array('is_sponsor' => 1, 'is_featured' => 0), 'group_id  = ' . (int)$iId);
	}

	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			Phpfox::getPhrase('marketplace.marketplace_listings') => $aUser['activity_marketplace']
		);
	}
	
	public function getAjaxCommentVar()
	{
		return 'marketplace.can_post_comment_on_listing';
	}	
	
	public function getCommentItem($iId)
	{
		$aListing = $this->database()->select('listing_id AS comment_item_id, user_id AS comment_user_id')
			->from($this->_sTable)
			->where('listing_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		$aListing['comment_view_id'] = 1;
			
		return $aListing;
	}	
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{
		$aRow = $this->database()->select('m.listing_id, m.title, u.full_name, u.user_id, u.gender, u.user_name')
			->from($this->_sTable, 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
			->where('m.listing_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['listing_id']))
		{
			return Phpfox_Error::trigger(Phpfox::getPhrase('marketplace.invalid_callback_on_marketplace_listing'));
		}
		
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		$this->database()->updateCounter('marketplace', 'total_comment', 'listing_id', $aVals['item_id']);		
		
		// Send the user an email
		$sLink = Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']);
		
		Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['listing_id'],
				'owner_subject' => Phpfox::getUserBy('full_name') . ' commented on your listing "' . $aRow['title'] . '".',
				'owner_message' => Phpfox::getUserBy('full_name') . " commented on your listing \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>",
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_marketplace',
				'mass_id' => 'marketplace',
				'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' listing.' : Phpfox::getUserBy('full_name') . ' commented on ' . $aRow['full_name'] . '\'s listing.'),
				'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserBy('full_name') . " commented on " . Phpfox::getService('user')->gender($aRow['gender'], 1) . " listing \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>" : Phpfox::getUserBy('full_name') . " commented on " . $aRow['full_name'] . "'s listing \"<a href=\"" . $sLink . "\">" . $aRow['title'] . "</a>\".\nTo see the comment thread, follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
			)
		);
	}
	
	public function updateCommentText($aVals, $sText)
	{
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_marketplace', $aVals['item_id'], $sText, $aVals['comment_id']) : null);
	}		
	
	public function getItemName($iId, $sName)
	{
		return '<a href="' . Phpfox::getLib('url')->makeUrl('comment.view', array('id' => $iId)) . '">On ' . $sName . '\'s listing.</a>';
	}	

	public function getLink($aParams)
	{
	    $aListing = $this->database()->select('m.title_url')
		    ->from(Phpfox::getT('marketplace'),'m')
		    ->where('m.listing_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
	    if (empty($aListing))
	    {
			return false;
	    }
	    
	    return Phpfox::getLib('url')->makeUrl('marketplace.view.' . $aListing['title_url'] );
	}
	
	public function getCommentNewsFeed($aRow)
	{		
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		

		if ($aRow['owner_user_id'] == $aRow['item_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_listin', array(
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
					'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
					'title_link' => $aRow['link']
				)
			);
		}
		else 
		{
			if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id'))
			{
				$aRow['text'] = Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_listing_a', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link']
					)
				);
			}
			else 
			{
				$aRow['text'] = Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_n', array(
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
						'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
						'title_link' => $aRow['link'],
						'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name']),
						'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
					)
				);
			}
		}	
			
		$aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
		
		return $aRow;
	}		
	
	public function getFeedRedirect($iId, $iChild = null)
	{
		$aListing = $this->database()->select('m.listing_id, m.title')
			->from($this->_sTable, 'm')
			->where('m.listing_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aListing['listing_id']))
		{
			return false;
		}
			
		(($sPlugin = Phpfox_Plugin::get('marketplace.service_callback_getfeedredirect')) ? eval($sPlugin) : false);
		
		return Phpfox::permalink('marketplace', $aListing['listing_id'], $aListing['title']);
	}
	
	public function getReportRedirect($iId)
	{
		return $this->getFeedRedirect($iId);
	}
	
	public function deleteComment($iId)
	{
		$this->database()->updateCounter('marketplace', 'total_comment', 'listing_id', $iId, true);
	}	
	
	public function getProfileLink()
	{
		return 'profile.marketplace';
	}

	public function getNewsFeed($aRow)
	{
		if ($sPlugin = Phpfox_Plugin::get('marketplace.service_callback_getnewsfeed_start')){eval($sPlugin);}
		$oUrl = Phpfox::getLib('url');
		$oParseOutput = Phpfox::getLib('parse.output');		
		
		$aRow['text'] = Phpfox::getPhrase('marketplace.a_href_user_link_owner_full_name_a_added_a_new_listing_a_href_title_link_title_a', array(
				'owner_full_name' => $this->preParse()->clean($aRow['owner_full_name']),
				'title' => $oParseOutput->shorten($oParseOutput->clean($aRow['content']), 30, '...'),
				'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
				'title_link' => $aRow['link']
			)
		);
		
		$aRow['icon'] = 'module/marketplace.png';
		$aRow['enable_like'] = true;
		
		return $aRow;
	}	
	
	public function getBlockDetailsProfile()
	{
		return array(
			'title' => Phpfox::getPhrase('marketplace.marketplace')
		);
	}

	public function hideBlockProfile($sType)
	{
		return array(
			'table' => 'user_design_order'
		);		
	}		

	/**
	 * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aListings = $this->database()
			->select('listing_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aListings as $aListing)
		{
			Phpfox::getService('marketplace.process')->delete($aListing['listing_id']);
		}
		// delete invites
		$this->database()->delete(Phpfox::getT('marketplace_invite'), 'user_id = ' . (int)$iUser);

	}
	
	public function getNotificationFeedApproved($aRow)
	{
		return array(
			'message' => Phpfox::getPhrase('marketplace.your_listing_title_has_been_approved', array('title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
			'link' => Phpfox::getLib('url')->makeUrl('marketplace', array('redirect' => $aRow['item_id'])),
			'path' => 'marketplace.url_image',
			'suffix' => '_120'
		);		
	}	
	
	public function getGlobalPrivacySettings()
	{
		return array(
			'marketplace.display_on_profile' => array(
				'phrase' => Phpfox::getPhrase('marketplace.listings')				
			)
		);
	}		
	
	public function pendingApproval()
	{
		return array(
			'phrase' => Phpfox::getPhrase('marketplace.listings'),
			'value' => Phpfox::getService('marketplace')->getPendingTotal(),
			'link' => Phpfox::getLib('url')->makeUrl('marketplace', array('view' => 'pending'))
		);
	}

	public function legacyRedirect($aRequest)
	{
		if (isset($aRequest['req2']))
		{
			switch ($aRequest['req2'])
			{
				case 'viewall':
					if (isset($aRequest['cat']))
					{				
						$aItem = Phpfox::getService('core')->getLegacyUrl(array(
							'url_field' => 'name_url',
								'table' => 'marketplace_category',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['cat'],
								'user_id' => false
							)
						);
						
						if ($aItem !== false)
						{
							return array('marketplace', $aItem['name_url']);
						}											
					}					
					break;
				case 'view':
					if (isset($aRequest['id']))
					{				
						$aItem = Phpfox::getService('core')->getLegacyUrl(array(
							'url_field' => 'title_url',
								'table' => 'marketplace',
								'field' => 'upgrade_item_id',
								'id' => $aRequest['id'],
								'user_id' => false
							)
						);
						
						if ($aItem !== false)
						{
							return array('marketplace', array('view', $aItem['title_url']));
						}											
					}						
					break;
			}
		}
		
		return 'marketplace';
	}

	public function getUserCountFieldInvite()
	{
		return 'marketplace_invite';
	}	
	
	public function getNotificationFeedInvite($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('marketplace.user_link_invited_you_to_a_marketplace_listing', array('user' => $aRow)),
			'link' => Phpfox::getLib('url')->makeUrl('marketplace', array('redirect' => $aRow['item_id']))
		);
	}		
	
	public function getRequestLink()
	{
		$iTotal = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('marketplace_invite'), 'mi')
			->join(Phpfox::getT('marketplace'), 'm', 'm.listing_id = mi.listing_id')
			->where('mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId())
			->execute('getSlaveField');
		
		if (!Phpfox::getParam('request.display_request_box_on_empty') && !$iTotal)
		{
			return null;
		}

		return '<li><a href="' . Phpfox::getLib('url')->makeUrl('marketplace', array('view' => 'invitation')) . '"' . (!$iTotal ? ' onclick="alert(\'' . Phpfox::getPhrase('marketplace.no_listing_invites') . '\'); return false;"' : '') . '><img src="' . Phpfox::getLib('template')->getStyle('image', 'module/marketplace.png') . '" class="v_middle" /> ' . Phpfox::getPhrase('marketplace.marketplace_invites') . ' (<span id="js_request_marketplace_count_total">' . $iTotal . '</span>)</a></li>';
	}

	public function reparserList()
	{
		return array(
			'name' => Phpfox::getPhrase('marketplace.marketplace_text'),
			'table' => 'marketplace_text',
			'original' => 'description',
			'parsed' => 'description_parsed',
			'item_field' => 'listing_id'
		);
	}

	public function getSiteStatsForAdmins()
	{
		$iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return array(
			'phrase' => Phpfox::getPhrase('marketplace.listings'),
			'value' => $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('marketplace'))
				->where('view_id = 0 AND time_stamp >= ' . $iToday)
				->execute('getSlaveField')
		);
	}

	/**
	 * @param int $iId video_id
	 * @return array in the format:
	 * array(
	 *	'title' => 'item title',		    <-- required
	 *	'link'  => 'makeUrl()'ed link',		    <-- required
	 *	'paypal_msg' => 'message for paypal'	    <-- required
	 *	'item_id' => int			    <-- required
	 *	'user_id;   => owner's user id		    <-- required
	 *	'error' => 'phrase if item doesnt exit'	    <-- optional
	 *	'extra' => 'description'		    <-- optional
	 *	'image' => 'path to an image',		    <-- optional
	 *	'image_dir' => 'photo.url_photo|...	    <-- optional (required if image)
	 *	'server_id' => db value			    <-- optional (required if image)
	 * )
	 */
	public function getToSponsorInfo($iId)
	{
	    $aListing = $this->database()->select('ml.user_id, ml.listing_id as item_id, ml.title, ml.image_path as image, ml.server_id')
		    ->from($this->_sTable, 'ml')
		    ->where('ml.listing_id = ' . (int)$iId)
		    ->execute('getSlaveRow');
		    
	    if (empty($aListing))
	    {
			return array('error' => Phpfox::getPhrase('marketplace.sponsor_error_not_found'));
	    }
	    
	    $aListing['title'] = Phpfox::getPhrase('marketplace.sponsor_title', array('sListingTitle' => $aListing['title']));
	    $aListing['paypal_msg'] = Phpfox::getPhrase('marketplace.sponsor_paypal_message', array('sListingTitle' => $aListing['title']));
	    //$aListing['link'] = Phpfox::getLib('url')->makeUrl('marketplace.view.'.$aListing['title_url']);
	    $aListing['link'] = Phpfox::permalink('ad.sponsor', $aListing['item_id'], $aListing['title']);
	    if (isset($aListing['image']) && $aListing['image'] != '')
	    {
			$aListing['image_dir'] = 'marketplace.url_image';
			$aListing['image'] = sprintf($aListing['image'],'_200');
	    }
	    
	    return $aListing;
	}

	public function getFeedRedirectFeedLike($iId, $iChildId = 0)
	{
		return $this->getFeedRedirect($iChildId);
	}
	
	public function getNewsFeedFeedLike($aRow)
	{
		if ($aRow['owner_user_id'] == $aRow['viewer_user_id'])
		{
			$aRow['text'] = Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_likes_their_own_a_href_link_listing_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'gender' => Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
					'link' => $aRow['link']
				)
			);
		}
		else 
		{
			$aRow['text'] = Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_listing_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['owner_user_name']),
					'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
					'view_user_link' => Phpfox::getLib('url')->makeUrl($aRow['viewer_user_name']),
					'link' => $aRow['link']			
				)
			);
		}
		
		$aRow['icon'] = 'misc/thumb_up.png';

		return $aRow;				
	}		

	public function getNotificationFeedNotifyLike($aRow)
	{		
		return array(
			'message' => Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_likes_your_a_href_link_listing_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
					'user_link' => Phpfox::getLib('url')->makeUrl($aRow['user_name']),
					'link' => Phpfox::getLib('url')->makeUrl('marketplace', array('redirect' => $aRow['item_id']))
				)
			),
			'link' => Phpfox::getLib('url')->makeUrl('marketplace', array('redirect' => $aRow['item_id']))			
		);				
	}	
	
	public function sendLikeEmail($iItemId)
	{
		return Phpfox::getPhrase('marketplace.a_href_user_link_full_name_a_likes_your_a_href_link_listing_a', array(
					'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
					'user_link' => Phpfox::getLib('url')->makeUrl(Phpfox::getUserBy('user_name')),
					'link' => Phpfox::getLib('url')->makeUrl('marketplace', array('redirect' => $iItemId))
				)
			);
	}			
	
	public function paymentApiCallback($aParams)
	{
		Phpfox::log('Module callback recieved: ' . var_export($aParams, true));	
		Phpfox::log('Attempting to retrieve purchase from the database');		
		
		$aInvoice = Phpfox::getService('marketplace')->getInvoice($aParams['item_number']);
		
		if ($aInvoice === false)
		{
			Phpfox::log('Not a valid invoice');
			
			return false;
		}
		
		$aListing = Phpfox::getService('marketplace')->getForEdit($aInvoice['listing_id'], true);
		
		if ($aListing === false)
		{
			Phpfox::log('Not a valid listing.');
			
			return false;
		}
		
		Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));
		
		if ($aParams['status'] == 'completed')
		{
			if ($aParams['total_paid'] == $aInvoice['price'])
			{
				Phpfox::log('Paid correct price');
			}
			else 
			{
				Phpfox::log('Paid incorrect price');
				
				return false;
			}
		}
		else 
		{
			Phpfox::log('Payment is not marked as "completed".');
			
			return false;
		}
		
		Phpfox::log('Handling purchase');
		
		$this->database()->update(Phpfox::getT('marketplace_invoice'), array(
				'status' => $aParams['status'],
				'time_stamp_paid' => PHPFOX_TIME
			), 'invoice_id = ' . $aInvoice['invoice_id']
		);		
		
		if ($aListing['auto_sell'])
		{
			$this->database()->update(Phpfox::getT('marketplace'), array(
					'view_id' => '2'
				), 'listing_id = ' . $aListing['listing_id']
			);					
		}		
		
		Phpfox::getLib('mail')->to($aListing['user_id'])						
			->subject(array('marketplace.item_sold_title', array('title' => Phpfox::getLib('parse.input')->clean($aListing['title'], 255))))
			->fromName($aInvoice['full_name'])
			->message(array('marketplace.full_name_has_purchased_an_item_of_yours_on_site_name', array(
						'full_name' => $aInvoice['full_name'],
						'site_name' => Phpfox::getParam('core.site_title'),
						'title' => $aListing['title'],
						'link' => Phpfox::getLib('url')->makeUrl('marketplace.view', $aListing['title_url']),
						'user_link' => Phpfox::getLib('url')->makeUrl($aInvoice['user_name']),
						'price' => Phpfox::getService('core.currency')->getCurrency($aInvoice['price'], $aInvoice['currency_id'])
					)
				)
			)
			->send();		
		
		Phpfox::log('Handling complete');		
	}
	
	public function getRedirectComment($iId)
	{
		return $this->getFeedRedirect($iId);
	}

	public function getSqlTitleField()
	{
		return array(
			'table' => 'marketplace',
			'field' => 'title'
		);
	}		
	
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('listing_id, title, user_id')
			->from(Phpfox::getT('marketplace'))
			->where('listing_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
			
		if (!isset($aRow['listing_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'marketplace\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'marketplace', 'listing_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(Phpfox::getPhrase('marketplace.full_name_liked_your_listing_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(Phpfox::getPhrase('marketplace.full_name_liked_your_listing_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->send();
					
			Phpfox::getService('notification.process')->add('marketplace_like', $aRow['listing_id'], $aRow['user_id']);				
		}		
	}
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'marketplace\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'marketplace', 'listing_id = ' . (int) $iItemId);
	}	
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('e.listing_id, e.title, e.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('marketplace'), 'e')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('e.listing_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['listing_id']))
		{
			return false;
		}			
			
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' own listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked your listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' liked <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('marketplace', $aRow['listing_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('b.listing_id, b.title, b.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('marketplace'), 'b')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
			->where('b.listing_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on ' . Phpfox::getService('user')->gender($aRow['gender'], 1) . ' listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';	
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on your listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
		else 
		{
			$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' commented on <span class="drop_data_user">' . $aRow['full_name'] . '\'s</span> listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('marketplace', $aRow['listing_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);
	}	
	
	public function getNotificationInvite($aNotification)
	{
		$aRow = $this->database()->select('e.listing_id, e.title, e.user_id, u.full_name')	
			->from(Phpfox::getT('marketplace'), 'e')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
			->where('e.listing_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		if (!isset($aRow['listing_id']))
		{
			return false;
		}			
			
		$sPhrase = Phpfox::getService('notification')->getUsers($aNotification) . ' wants you to check out the listing "' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...') . '"';
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('marketplace', $aRow['listing_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	public function getActivityFeed($aItem)
	{				
		$aRow = $this->database()->select('e.listing_id, e.title, e.time_stamp, e.image_path, e.server_id, e.total_like, e.total_comment, et.description_parsed, l.like_id AS is_liked')
			->from(Phpfox::getT('marketplace'), 'e')
			->leftJoin(Phpfox::getT('marketplace_text'), 'et', 'et.listing_id = e.listing_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'marketplace\' AND l.item_id = e.listing_id AND l.user_id = ' . Phpfox::getUserId())
			->where('e.listing_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');		
		
		$aReturn = array(
			'feed_title' => $aRow['title'],
			'feed_info' => Phpfox::getPhrase('feed.created_a_listing'),
			'feed_link' => Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']),
			'feed_content' => $aRow['description_parsed'],
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/marketplace.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],	
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],
			'enable_like' => true,			
			'like_type_id' => 'marketplace',
			'total_comment' => $aRow['total_comment'],
			'comment_type_id' => 'marketplace'
		);
		
		if (!empty($aRow['image_path']))
		{
			$sImage = Phpfox::getLib('image.helper')->display(array(
					'server_id' => $aRow['server_id'],
					'path' => 'marketplace.url_image',
					'file' => $aRow['image_path'],
					'suffix' => '_120',
					'max_width' => 120,
					'max_height' => 120					
				)
			);
			
			$aReturn['feed_image'] = $sImage;
		}		
		
		return $aReturn;
	}	
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_listing']))
			{
				return false;
			}

			if (isset($aUser['total_listing']) && (int) $aUser['total_listing'] === 0)
			{
				return false;
			}
		}
		
		$aMenus[] = array(
			'phrase' => Phpfox::getPhrase('profile.listings'),
			'url' => 'profile.marketplace',
			'total' => (int) (isset($aUser['total_listing']) ? $aUser['total_listing'] : 0),
			'icon' => 'module/marketplace.png'	
		);	
		
		return $aMenus;
	}	
	
	public function getTotalItemCount($iUserId)
	{
		return array(
			'field' => 'total_listing',
			'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('marketplace'))->where('view_id = 0 AND user_id = ' . (int) $iUserId)->execute('getSlaveField')
		);	
	}	

	public function globalUnionSearch($sSearch)
	{
		$this->database()->select('item.listing_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'marketplace\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('marketplace'), 'item')
			->where('item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title', $sSearch))
			->union();
	}
	
	public function getSearchInfo($aRow)
	{
		$aInfo = array();
		$aInfo['item_link'] = Phpfox::getLib('url')->permalink('marketplace', $aRow['item_id'], $aRow['item_title']);
		$aInfo['item_name'] = 'Marketplace Listing';
		
		$aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
				'server_id' => $aRow['item_photo_server'],
				'file' => $aRow['item_photo'],
				'path' => 'marketplace.url_image',
				'suffix' => '_120',
				'max_width' => '120',
				'max_height' => '120'				
			)
		);		
		
		return $aInfo;
	}
	
	public function getSearchTitleInfo()
	{
		return array(
			'name' => 'Listings'
		);
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
		if ($sPlugin = Phpfox_Plugin::get('marketplace.service_callback__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>