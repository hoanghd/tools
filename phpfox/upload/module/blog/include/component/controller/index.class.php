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
 * @package  		Module_Blog
 * @version 		$Id: index.class.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
class Blog_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{			
			Phpfox::getService('core')->getLegacyItem(array(
					'field' => array('blog_id', 'title'),
					'table' => 'blog',		
					'redirect' => 'blog',
					'title' => $sLegacyTitle
				)
			);
		}		
		
		if ($this->request()->get('req2') == 'main')
		{
			return Phpfox::getLib('module')->setController('error.404');
		}
	
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_start')) ? eval($sPlugin) : false);
		
		if (($iRedirectId = $this->request()->get('redirect')) && ($aRedirectBlog = Phpfox::getService('blog')->getBlogForEdit($iRedirectId)))
		{
			Phpfox::permalink('blog', $aRedirectBlog['blog_id'], $aRedirectBlog['title'], true);
		}
		
		Phpfox::getUserParam('blog.view_blogs', true);	
		
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsProfile = true;
			$aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else 
		{		
			$bIsProfile = $this->getParam('bIsProfile');	
			if ($bIsProfile === true)
			{
				$aUser = $this->getParam('aUser');
			}
		}
	
		/**
		 * Check if we are going to view an actual blog instead of the blog index page.
		 * The 2nd URL param needs to be numeric.
		 */
		if (!Phpfox::isAdminPanel())
		{
			if ($this->request()->getInt('req2') > 0)
			{
				/**
				 * Since we are going to be viewing a blog lets reset the controller and get out of this one.
				 */
				return Phpfox::getLib('module')->setController('blog.view');			
			}		
		}
		
		if ($this->request()->get('req2') == 'category' && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
			$aLegacyItem = Phpfox::getService('core')->getLegacyItem(array(
					'field' => array('category_id', 'name'),
					'table' => 'blog_category',		
					'redirect' => 'blog.category',
					'title' => $sLegacyTitle,
					'search' => 'name_url'
				)
			);		
		}		

		/**
		 * This creates a global variable that can be used in other components. This is a good way to 
		 * pass information to other components.
		 */
		$this->setParam('sTagType', 'blog');
		
		$this->template()->setTitle(($bIsProfile ? Phpfox::getPhrase('blog.full_name_s_blogs', array('full_name' => $aUser['full_name'])) : Phpfox::getPhrase('blog.blog_title')))->setBreadCrumb(($bIsProfile ? 'Blogs' : Phpfox::getPhrase('blog.blog_title')), ($bIsProfile ? $this->url()->makeUrl($aUser['user_name'], 'blog') : $this->url()->makeUrl('blog')));
		
		$sView = $this->request()->get('view');			

		$this->search()->set(array(
				'type' => 'blog',
				'field' => 'blog.blog_id',				
				'search_tool' => array(
					'table_alias' => 'blog',
					'search' => array(
						'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('blog', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('blog', array('view' => $this->request()->get('view')))),
						'default_value' => Phpfox::getPhrase('blog.search_blogs_dot'),
						'name' => 'search',
						'field' => 'blog_text.text'
					),
					'sort' => array(
						'latest' => array('blog.time_stamp', Phpfox::getPhrase('blog.latest')),
						'most-viewed' => array('blog.total_view', Phpfox::getPhrase('blog.most_viewed')),
						'most-liked' => array('blog.total_like', Phpfox::getPhrase('blog.most_liked')),
						'most-talked' => array('blog.total_comment', Phpfox::getPhrase('blog.most_discussed'))
					),
					'show' => array(5, 10, 15)
				)
			)
		);				
		
		$aBrowseParams = array(
			'module_id' => 'blog',
			'alias' => 'blog',
			'field' => 'blog_id',
			'table' => Phpfox::getT('blog'),
			'hide_view' => array('pending', 'my')				
		);		
		
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
				Phpfox::getPhrase('blog.all_blogs') => '',
				Phpfox::getPhrase('blog.my_blogs') => 'my'							
			);
			
			if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend'))
			{
				$aFilterMenu[Phpfox::getPhrase('blog.friends_blogs')] = 'friend';	
			}			
			
			if (Phpfox::getUserParam('blog.can_approve_blogs'))
			{
				$iPendingTotal = Phpfox::getService('blog')->getPendingTotal();
				
				if ($iPendingTotal)
				{
					$aFilterMenu[Phpfox::getPhrase('blog.pending_blogs') . (Phpfox::getUserParam('blog.can_approve_blogs') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
				}
			}			
		}		
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_search')) ? eval($sPlugin) : false);
		
		$this->template()->buildSectionMenu('blog', $aFilterMenu);		
	
		switch ($sView)
		{
			case 'spam':
				Phpfox::isUser(true);
				if (Phpfox::getUserParam('blog.can_approve_blogs'))
				{
					$this->search()->setCondition('AND blog.is_approved = 9');
				}
				break;
			case 'pending':
				Phpfox::isUser(true);
				if (Phpfox::getUserParam('blog.can_approve_blogs'))
				{
					$this->search()->setCondition('AND blog.is_approved = 0');
				}				
				break;
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND blog.user_id = ' . Phpfox::getUserId());				
				break;
			case 'draft':
				Phpfox::isUser(true);
				$this->search()->setCondition("AND blog.user_id = " . $aUser['user_id'] . " AND blog.is_approved IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '1') . ") AND blog.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('privacy')->getForBrowse($aUser)) . ") AND blog.post_status = 2");
				break;
			default:
				if ($bIsProfile === true)
				{
					$this->search()->setCondition("AND blog.user_id = " . $aUser['user_id'] . " AND blog.is_approved IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '1') . ") AND blog.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('privacy')->getForBrowse($aUser)) . ") AND blog.post_status IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1,2' : '1') . ")");	
				}
				else
				{
					$this->search()->setCondition("AND blog.is_approved = 1 AND blog.privacy IN(%PRIVACY%) AND blog.post_status = 1");
				}
				break;
		}	
		
		if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category')
		{			
			if ($aBlogCategory = Phpfox::getService('blog.category')->getCategory($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3'))))
			{
				$this->template()->setBreadCrumb(Phpfox::getPhrase('blog.category'));		
				
				$this->search()->setCondition('AND blog_category.category_id = ' . $this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')) . ' AND blog_category.user_id = ' . ($bIsProfile ? (int) $aUser['user_id'] : 0));
				
				$this->template()->setTitle(Phpfox::getLib('locale')->convert($aBlogCategory['name']));
				$this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aBlogCategory['name']), $this->url()->makeUrl('current'), true);
				
				$this->search()->setFormUrl($this->url()->permalink(array('blog.category', 'view' => $this->request()->get('view')), $aBlogCategory['category_id'], $aBlogCategory['name']));
			}			
		}
		elseif ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'tag')
		{
			if (($aTag = Phpfox::getService('tag')->getTagInfo('blog', $this->request()->get(($bIsProfile === true ? 'req4' : 'req3')))))
			{
				$this->template()->setBreadCrumb('Topic: ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);				
				
				$this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($this->request()->get(($bIsProfile === true ? 'req4' : 'req3'))) . '\' AND tag.added > ' . (PHPFOX_TIME - (86400 * Phpfox::getParam('tag.tag_days_treading'))) . '');	
			}
		}		
		
		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$aItems = $this->search()->browse()->getRows();
		
		Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
		
		Phpfox::getService('blog')->getExtra($aItems, 'user_profile');

		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_middle')) ? eval($sPlugin) : false);
		
		$this->template()->setMeta('keywords', Phpfox::getParam('blog.blog_meta_keywords'));
		$this->template()->setMeta('description', Phpfox::getParam('blog.blog_meta_description'));
		if ($bIsProfile)
		{
			$this->template()->setMeta('description', '' . $aUser['full_name'] . ' has ' . $this->search()->browse()->getCount() . ' blogs.');
		}
		
		foreach ($aItems as $aItem)
		{
			$this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));	
			if (!empty($aItem['tag_list']))
			{
				$this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
			}
		}		
		
		/**
		 * Here we assign the needed variables we plan on using in the template. This is used to pass
		 * on any information that needs to be used with the specific template for this component.
		 */
		$this->template()->assign(array(
					'iCnt' => $this->search()->browse()->getCount(),
					'aItems' => $aItems,
					'sSearchBlock' => Phpfox::getPhrase('blog.search_blogs_'),
					'bIsProfile' => $bIsProfile,
					'sTagType' => ($bIsProfile === true ? 'blog_profile' : 'blog'),
					'sBlogStatus' => $this->request()->get('status'),
					'iShorten' => Phpfox::getParam('blog.length_in_index'),
					'sView' => $sView					
				)
			)
			->setHeader('cache', array(
				'quick_submit.js' => 'module_blog',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',				
				'quick_edit.js' => 'static_script',				
				'comment.css' => 'style_css',
				'pager.css' => 'style_css',
				'feed.js' => 'module_feed'
			)
		);			
		
		$this->setParam('global_moderation', array(
				'name' => 'blog',
				'ajax' => 'blog.moderation',
				'menu' => array(
					array(
						'phrase' => Phpfox::getPhrase('blog.delete'),
						'action' => 'delete'
					),
					array(
						'phrase' => Phpfox::getPhrase('blog.approve'),
						'action' => 'approve'
					)					
				)
			)
		);
				
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'iCnt',
				'aItems',
				'sSearchBlock'
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>