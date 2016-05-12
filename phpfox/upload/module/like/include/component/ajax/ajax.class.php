<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 3335 2011-10-20 17:26:57Z Raymond_Benc $
 */
class Like_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function add()
	{
		Phpfox::isUser(true);
		
		if (Phpfox::getService('like.process')->add($this->get('type_id'), $this->get('item_id')))
		{
			if ($this->get('type_id') == 'feed_mini')
			{
				$this->_loadCommentLikes();
			}
			else
			{
				$this->_loadLikes(true);
			}
		}
	}
	
	public function delete()
	{
		Phpfox::isUser(true);
		
		if (Phpfox::getService('like.process')->delete($this->get('type_id'), $this->get('item_id')))
		{
			if ($this->get('type_id') == 'feed_mini')
			{
				$this->_loadCommentLikes();	
			}
			else
			{
				$this->_loadLikes(false);
			}
		}
	}

	public function browse()
	{				
		$this->error(false);
		Phpfox::getBlock('like.browse');	
		$this->setTitle((($this->get('type_id') == 'pages' && $this->get('force_like') == '') ? Phpfox::getPhrase('like.members') : Phpfox::getPhrase('like.people_who_like_this')));
	}
	
	private function _loadCommentLikes()
	{
		$aComment = Phpfox::getService('comment')->getComment($this->get('item_id'));
		if ($aComment['total_like'] > 0)
		{
			$sPhrase = Phpfox::getPhrase('like.1_person');
			if ($aComment['total_like'] > 1)
			{
				$sPhrase = Phpfox::getPhrase('like.total_people', array('total' => $aComment['total_like']));
			}
			$this->call('$(\'#js_comment_' . $this->get('item_id') . '\').find(\'.js_like_link_holder\').show();');
			$this->call('$(\'#js_comment_' . $this->get('item_id') . '\').find(\'.js_like_link_holder_info\').html(\'' . $sPhrase . '\');');
		}
		else 
		{
			$this->call('$(\'#js_comment_' . $this->get('item_id') . '\').find(\'.js_like_link_holder\').hide();');
		}	
	}
	
	private function _loadLikes($bIsLiked)
	{
		$aLikes = Phpfox::getService('like')->getLikesForFeed($this->get('type_id'), $this->get('item_id'), $bIsLiked, Phpfox::getParam('feed.total_likes_to_display'), true);
		
		if (!Phpfox::getService('like')->getTotalLikes())
		{
			$this->html('#js_like_body_' . str_replace('js_feed_like_holder_', '', $this->get('parent_id')), '');
			
			return;
		}
		
		$this->template()->assign(array(
				'aFeed' => array(
					'feed_is_liked' => $bIsLiked,
					'feed_total_like' => Phpfox::getService('like')->getTotalLikes(),
					'like_type_id' => $this->get('type_id'),
					'item_id' => $this->get('item_id'),
					'likes' => $aLikes
				)
			)			
		);
			
		$this->template()->getTemplate('like.block.display');				
		
		$this->html('#js_like_body_' . str_replace('js_feed_like_holder_', '', $this->get('parent_id')), $this->getContent(false));
		$this->call('$(\'#js_like_body_' . str_replace('js_feed_like_holder_', '', $this->get('parent_id')) . '\').parents(\'.comment_mini_content_holder:first\').show();');		
	}
}

?>