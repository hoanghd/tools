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
 * @version 		$Id: view.class.php 2693 2011-06-28 11:24:05Z Raymond_Benc $
 */
class Comment_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (($iCommentId = $this->request()->getInt('req3')))
		{
			$aComment = Phpfox::getService('comment')->getComment($iCommentId);
			if (Phpfox::hasCallback($aComment['type_id'], 'getParentItemCommentUrl'))
			{
				$sNewUrl = Phpfox::callback($aComment['type_id'] . '.getParentItemCommentUrl', $aComment);
				if ($sNewUrl !== false)
				{
					$aComment['callback_url'] = $sNewUrl;
				}
			}
			
			$this->template()->setTitle('Viewing Comment')
				->setHeader(array(
						'view.css' => 'module_comment'
					)
				)
				->setBreadcrumb('Viewing Comment')
				->assign(array(
					'aComment' => $aComment
				)
			);
		}
		else
		{
			$aComment = Phpfox::getService('comment')->getComment($this->request()->getInt('id'));
			
			if (!isset($aComment['comment_id']))
			{
				return Phpfox_Error::display(Phpfox::getPhrase('comment.comment_does_not_exist'));
			}
			
			$this->url()->forward(Phpfox::callback('comment.getRedirectRequest', $aComment['comment_id']));
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('comment.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}

?>