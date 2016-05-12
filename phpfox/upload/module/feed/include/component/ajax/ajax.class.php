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
 * @version 		$Id: ajax.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Feed_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function getEditBar()
	{
		Phpfox::getBlock('feed.setting');
		
		$this->html('#js_edit_block_' . $this->get('block_id'), $this->getContent(false))->slideDown('#js_edit_block_' . $this->get('block_id'));
	}
	
	public function addComment()
	{
		Phpfox::isUser(true);
		
		$aVals = (array) $this->get('val');		
		
		/* Check if user chose an egift */
		if (isset($aVals['egift_id']) && !empty($aVals['egift_id']))
		{
			/* is this gift a free one? */
			$aGift = Phpfox::getService('egift')->getEgift($aVals['egift_id']);
			if (!empty($aGift))
			{
				$bIsFree = true;
				foreach ($aGift['price'] as $sCurrency => $fVal)
				{
					if ($fVal > 0)
					{
						$bIsFree = false;
					}
				}	
				/* This is an important change, in v2 birthday_id was the mail_id, in v3
				 * birthday_id is the feed_id
				*/
				$aVals['feed_type'] = 'feed_egift';
				$iId = Phpfox::getService('feed.process')->addComment($aVals);
				// Always make an invoice, so the feed can check on the state
				$iInvoice = Phpfox::getService('egift.process')->addInvoice($iId, $aVals['parent_user_id'], $aGift);
				
				if (!$bIsFree)
				{
					Phpfox::getBlock('api.gateway.form',
							array('gateway_data' => array(
									'item_number' => 'egift|' . $iInvoice, 
									'currency_code' => Phpfox::getService('user')->getCurrency(),//Phpfox::getService('core.currency')->getDefault(),
									'amount' => $aGift['price'][Phpfox::getService('user')->getCurrency()],
									'item_name' => 'egift card with message: ' . $aVals['user_status'] . '',
									'return' => Phpfox::getLib('url')->makeUrl('friend.invoice'),
									'recurring' => 0,
									'recurring_cost' => '',
									'alternative_cost' => 0,
									'alternative_recurring_cost' => 0
							)));
					$this->call('$("#js_activity_feed_form").hide().after("' . $this->getContent(true) . '");');
				}
				else
				{
					// egift is free
					Phpfox::getService('feed')->processAjax($iId);
				}
			}
			
		}
		else
		{			
			if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->addComment($aVals)))
			{
				Phpfox::getService('feed')->processAjax($iId);		
			}
			else 
			{
				$this->call('$Core.activityFeedProcess(false);');
			}	
		}
		
	}
	
	public function viewMore()
	{
		Phpfox::getBlock('feed.display');		

		$this->remove('#feed_view_more');
		$this->append('#js_feed_content', $this->getContent(false));
		$this->call('$Core.loadInit();');
	}
	
	public function rate()
	{		
		Phpfox::isUser(true);
		
		list($sRating, $iLastVote) = Phpfox::getService('feed.process')->rate($this->get('id'), $this->get('type'));
		Phpfox::getBlock('feed.rating', array(
				'sRating' => (int) $sRating,
				'iFeedId' => $this->get('id'),
				'bHasRating' => true,
				'iLastVote' => $iLastVote
			)
		);
		$this->html('#js_feed_rating' . $this->get('id'), $this->getContent(false));		
	}

	public function delete()
	{
		if (Phpfox::getService('feed.process')->deleteFeed($this->get('id'), $this->get('module'), $this->get('item')))
		{
			$this->slideUp('#js_item_feed_' . $this->get('id'));
			$this->alert(Phpfox::getPhrase('feed.feed_successfully_deleted'), Phpfox::getPhrase('feed.feed_deletion'), 300, 150, true);
		}
		else
		{
			$this->alert(Phpfox::getPhrase('feed.unable_to_delete_this_entry'));
		}
	}
	
	public function getCommentText()
	{
		$aRow = Phpfox::getService('feed')->getFeed($this->get('feed_id'));	

		(($sPlugin = Phpfox_Plugin::get('feed.component_ajax_getcommenttext')) ? eval($sPlugin) : false);
		
		if (!isset($bHasPluginCall))
		{			
			$this->call("$('#js_quick_edit_id" . $this->get('id') . "').html('<textarea style=\"width:95%; height:80px;\" name=\"quick_edit_input\" cols=\"90\" rows=\"10\" id=\"js_quick_edit" . $this->get('id') . "\">" . str_replace("'", "\'", Phpfox::getLib('parse.output')->ajax($aRow['content'])) . "</textarea>');");		
		}
	}	
	
	public function updateFeedText()
	{
		$sTxt = $this->get('quick_edit_input');
		
		if (Phpfox::getLib('parse.format')->isEmpty($sTxt))
		{
			$this->alert(Phpfox::getPhrase('comment.add_some_text_to_your_comment'));
			
			return false;	
		}

		if (Phpfox::getService('feed.process')->updateCommentText($this->get('feed_id'), $sTxt))
		{
			Phpfox::getLib('parse.output')->setImageParser(array('width' => 200, 'height' => 200));
			if (Phpfox::getParam('core.allow_html'))
			{
				$sTxt = Phpfox::getLib('parse.output')->parse(Phpfox::getLib('parse.input')->prepare($sTxt));
			}
			else 
			{
				$sTxt = Phpfox::getLib('parse.output')->parse($sTxt);
			}
			Phpfox::getLib('parse.output')->setImageParser(array('clear' => true));

			$this->html('#' . $this->get('id'), $sTxt, '.highlightFade()');			
		}
	}
	
	public function like()
	{
		if (Phpfox::getService('feed.process')->like($this->get('feed_id'), $this->get('type_id')))
		{
			list($aLikesCount, $aLikes) = Phpfox::getService('feed')->getLikeForFeed($this->get('feed_id'));
			
			if (count($aLikes))
			{
				$this->template()->assign(array(
						'aFeed' => array(
							'feed_id' => $this->get('feed_id'),
							'like_rows' => $aLikes[$this->get('feed_id')],
							'like_count' => ($aLikesCount[$this->get('feed_id')] - count($aLikes[$this->get('feed_id')]))
						)
					)
				);
				
				$this->template()->getTemplate('feed.block.like');
				
				$this->html('#js_feed_like_holder_' . $this->get('feed_id'), $this->getContent(false));
				$this->call('$(\'#js_feed_like_holder_' . $this->get('feed_id') . '\').parents(\'.comment_mini_content_holder:first\').show();');
			}
			else 
			{
				$this->html('#js_feed_like_holder_' . $this->get('feed_id'), '');	
				$this->call('$(\'#js_feed_like_holder_' . $this->get('feed_id') . '\').parents(\'.comment_mini_content_holder:first\').hide();');
			}
		}
	}
	
	public function likeList()
	{
		Phpfox::getBlock('feed.like-list');
	}
	
	public function reloadActivityFeed()
	{
        $aParts = explode(',', $this->get('reload-ids'));
		$aRows = Phpfox::getService('feed')->get(null, null, 0);
		
		$iNewCnt = 0;
		$sLoadIds = '';
		$aIds = array();
		foreach ($aParts as $sPart)
		{
			$iPart = (int) trim($sPart);
			
			$aIds[$iPart] = $iPart;
		}

		
		foreach ($aRows as $aRow)
		{
			if (!in_array($aRow['feed_id'], $aIds))
			{
				$iNewCnt++;
	
				$sLoadIds .= $aRow['feed_id'] . ',';								
			}
		}
	
		$this->call('$Core.rebuildActivityFeedCount(' . (int) $iNewCnt . ', \'' . $sLoadIds . '\');');
		$this->call('setTimeout("$.ajaxCall(\'feed.reloadActivityFeed\', \'reload-ids=\' + $Core.getCurrentFeedIds(), \'GET\');", ' . (Phpfox::getParam('feed.refresh_activity_feed') * 1000) . ');');
	}
	
	public function approveComment()
	{
        Phpfox::isUser(true);
		Phpfox::getUserParam('comment.can_moderate_comments', true);
		Phpfox::getService('feed.process')->approve($this->get('feed_id'));		
	}
    
    public function appendMore()
    {	
		$aRows = Phpfox::getService('feed')->get();
		
		$sCustomIds = '';
		foreach ($aRows as $aRow)
		{
			$sCustomIds .= $aRow['feed_id'];
			$this->template()->assign(array(				
					'aFeed' => $aRow					
				)
			);
			$this->template()->getTemplate('feed.block.entry');
		}
		
		$sIds = 'js_feed_' . md5($sCustomIds);
		
		$this->call('$(\'.js_parent_feed_entry\').each(function(){$(this).removeClass(\'row_first\');});');
		$this->prepend('#js_new_feed_update', '<div id="' . $sIds . '" style="display:none;">' . $this->getContent(false) . '</div>');
		$this->hide('#activity_feed_updates_link_holder');
		$this->slideDown('#' . $sIds);		
		$this->call('$Core.loadInit();');        
    }
}

?>