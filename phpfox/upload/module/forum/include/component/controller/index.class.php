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
 * @package  		Module_Forum
 * @version 		$Id: index.class.php 3310 2011-10-18 09:13:16Z Raymond_Benc $
 */
class Forum_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle))
		{
			if (($sLegacyThread = $this->request()->get('req3')) && !empty($sLegacyThread))
			{
				$aLegacyItem = Phpfox::getService('core')->getLegacyItem(array(
						'field' => array('thread_id', 'title'),
						'table' => 'forum_thread',		
						'redirect' => 'forum.thread',
						'title' => $sLegacyThread
					)
				);				
			}
			else
			{
				$aForumParts = explode('-', $sLegacyTitle);
				if (isset($aForumParts[1]))
				{
					$aLegacyItem = Phpfox::getService('core')->getLegacyItem(array(
							'field' => array('forum_id', 'name'),
							'table' => 'forum',		
							'redirect' => 'forum',
							'search' => 'forum_id',
							'title' => $aForumParts[1]
						)
					);
				}
			}
		}			
		
		Phpfox::getUserParam('forum.can_view_forum', true);
		
		$aParentModule = $this->getParam('aParentModule');	
		
		if ($this->request()->get('req2') == 'topics' || $this->request()->get('req2') == 'posts')
		{
			return Phpfox::getLib('module')->setController('error.404');
		}
				
		$this->template()->setBreadcrumb(Phpfox::getPhrase('forum.forum'), $this->url()->makeUrl('forum'))
			->setPhrase(array(
					'forum.provide_a_reply',
					'forum.adding_your_reply',
					'forum.are_you_sure',
					'forum.post_successfully_deleted',
					'forum.reply_multi_quoting'
				)
			)			
			->setHeader('cache', array(					
					'forum.css' => 'style_css',
					'forum.js' => 'module_forum'
				)
			);
		
		if ($aParentModule !== null)
		{
			Phpfox::getLib('module')->getComponent('forum.forum', array('bNoTemplate' => true), 'controller');

			return;
		}
		
		if ($this->request()->getInt('req2') > 0)
		{
			return Phpfox::getLib('module')->setController('forum.forum');
		}		
		
		$this->setParam('bIsForum', true);

		Phpfox::getService('forum')->buildMenu();
		
		$this->template()->setTitle(Phpfox::getPhrase('forum.forum'))
			->assign(array(
				'aForums' => Phpfox::getService('forum')->live()->getForums(),
				'bHasCategory' => Phpfox::getService('forum')->hasCategory(),				
				'aCallback' => null
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>