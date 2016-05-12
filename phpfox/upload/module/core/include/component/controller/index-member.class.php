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
 * @package  		Module_Core
 * @version 		$Id: index-member.class.php 3320 2011-10-19 12:39:18Z Miguel_Espinoza $
 */
class Core_Component_Controller_Index_Member extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		
		if ($sPlugin = Phpfox_Plugin::get('core.component_controller_index_member_start'))
		{
		    eval($sPlugin);
		}
		
		if ($this->request()->get('req3') == 'customize')
		{
				define('PHPFOX_IN_DESIGN_MODE', true);
				define('PHPFOX_CAN_MOVE_BLOCKS', true);
				if (($iTestStyle = $this->request()->get('test_style_id')))
				{
					if (Phpfox::getLib('template')->testStyle($iTestStyle))
					{
						
					}
				}
				
				$aDesigner = array(
					'current_style_id' => Phpfox::getUserBy('style_id'),
					'design_header' => Phpfox::getPhrase('core.customize_dashboard'),
					'current_page' => $this->url()->makeUrl(''),
					'design_page' => $this->url()->makeUrl('core.index-member', 'customize'),
					'block' => 'core.index-member',				
					'item_id' => Phpfox::getUserId(),
					'type_id' => 'user'
				);
				
				$this->setParam('aDesigner', $aDesigner);	
				
				$this->template()
						->setPhrase(array(
							'theme.are_you_sure'
						))
						->setHeader('cache', array(
								'jquery/ui.js' => 'static_script',
								'sort.js' => 'module_theme',
								'style.css' => 'style_css',
								'select.js' => 'module_theme',
								'design.js' => 'module_theme',
								'video.css' => 'module_video'
							)
						)
						->setHeader(array(												
							'<script type="text/javascript">function designOnUpdate() { $Core.design.updateSorting(); }</script>',		
							'<script type="text/javascript">$Core.design.init({type_id: \'user\'});</script>'
						)					
				);			
		}
		else 
		{
			$this->template()->setHeader('jquery/ui.js', 'static_script');
			$this->template()->setHeader('cache', array(						
						'sort.js' => 'module_theme',
						'design.js' => 'module_theme',
						'video.css' => 'module_video'
					)
				)
				->setHeader(array(	
					'<script type="text/javascript">function designOnUpdate() { $Core.design.updateSorting(); }</script>',
					'<script type="text/javascript">$Core.design.init({type_id: \'user\'});</script>'
				)
			);
		}		
		
		Phpfox::getLib('module')->setCacheBlockData(array(
				'table' => 'user_dashboard',
				'field' => 'user_id',
				'item_id' => Phpfox::getUserId(),
				'controller' => 'core.index-member'
			)
		);
		
		$this->template()->setHeader('cache', array(
					'feed.js' => 'module_feed',
					'welcome.css' => 'style_css',
					'announcement.css' => 'style_css',
					'comment.css' => 'style_css',
					'quick_edit.js' => 'static_script',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script'					
				)
			)
			->setEditor(array(
					'load' => 'simple'					
			)
		);	
	}
}

?>