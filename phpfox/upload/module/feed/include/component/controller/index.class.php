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
 * @version 		$Id: index.class.php 2768 2011-07-29 18:59:34Z Raymond_Benc $
 */
class Feed_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->template()->setEditor()->setHeader('cache', array(
				'feed.js' => 'module_feed',					
				'comment.css' => 'style_css',					
				'quick_edit.js' => 'static_script',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'jquery/plugin/jquery.scrollTo.js' => 'static_script'								
			)					
		);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('feed.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>