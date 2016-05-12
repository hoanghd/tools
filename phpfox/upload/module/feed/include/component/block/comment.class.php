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
 * @package  		Module_Feed
 * @version 		$Id: comment.class.php 3243 2011-10-07 11:34:56Z Miguel_Espinoza $
 */
class Feed_Component_Block_Comment extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aFeed = $this->getParam('aFeed');
		$sFeedType = (isset($aFeed['feed_display']) ? $aFeed['feed_display'] : null);
		$aFeed['feed_id'] = $aFeed['item_id'];
		$aFeed['is_view_item'] = true;

		$bCanPostComment = true;
		if (isset($aFeed['comment_privacy']) && $aFeed['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('privacy.can_comment_on_all_items'))
		{
			switch ($aFeed['comment_privacy'])
			{
				case 1:					
					if ((int) $aFeed['feed_is_friend'] <= 0)
					{
						$bCanPostComment = false;						
					}
					break;
				case 2:
					if ((int) $aFeed['feed_is_friend'] > 0)
					{
						$bCanPostComment = true;
					}
					else 
					{
						if (!Phpfox::getService('friend')->isFriendOfFriend($aFeed['user_id']))
						{
							$bCanPostComment = false;	
						}
					}
					break;
				case 3:
					$bCanPostComment = false;
					break;
			}
		}
		$aFeed['can_post_comment'] = $bCanPostComment;
		
		if ((int) $aFeed['total_like'] > 0 && Phpfox::isModule('like'))
		{
			$aFeed['likes'] = Phpfox::getService('like')->getLikesForFeed($aFeed['like_type_id'], $aFeed['item_id'], ((int) $aFeed['feed_is_liked'] > 0 ? true : false), Phpfox::getParam('feed.total_likes_to_display'));
		}		
		
		$iPageLimit = 2;
		$mPager = null;
		if (Phpfox::isModule('comment') && $sFeedType != 'mini')
		{	
			if ((int) $aFeed['total_comment'] > 0)
			{					
				if ($sFeedType == 'view')
				{
					$iPageLimit = Phpfox::getParam('comment.comment_page_limit');
					$mPager = $aFeed['total_comment'];
				}
							
				$aFeed['comments'] = Phpfox::getService('comment')->getCommentsForFeed($aFeed['comment_type_id'], $aFeed['item_id'], $iPageLimit, $mPager);
			}
		}
		
		if ($sFeedType == 'view')
		{
			Phpfox::getLib('pager')->set(array(
					'ajax' => 'comment.viewMoreFeed', 
					'page' => Phpfox::getLib('request')->getInt('page'), 
					'size' => $iPageLimit, 
					'count' => $mPager,
					'phrase' => 'View previous comments',
					'icon' => 'misc/comment.png',
					'aParams' => array(
						'comment_type_id' => $aFeed['comment_type_id'],
						'item_id' => $aFeed['item_id'],
						'append' => true,
						'pagelimit' => $iPageLimit,
						'total' => $mPager
					)
				)
			);
		}
		
		if (empty($sFeedType))
		{
			$sFeedType = 'default';
		}
		$this->template()->assign(array(
				'aFeed' => $aFeed,
				'sFeedType' => $sFeedType				
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('comment.component_block_comment_clean')) ? eval($sPlugin) : false);
	}	
}

?>