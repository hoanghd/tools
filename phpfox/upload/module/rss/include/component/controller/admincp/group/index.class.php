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
 * @version 		$Id: index.class.php 2846 2011-08-18 09:30:49Z Raymond_Benc $
 */
class Rss_Component_Controller_Admincp_Group_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (($iDeleteId = $this->request()->get('delete')))
		{
			if (Phpfox::getService('rss.group.process')->delete($iDeleteId))
			{
				$this->url()->send('admincp.rss.group', null, Phpfox::getPhrase('rss.group_successfully_deleted'));
			}
		}
		
		$this->template()->setTitle(Phpfox::getPhrase('rss.manage_groups'))
			->setBreadcrumb(Phpfox::getPhrase('rss.manage_groups'), $this->url()->makeUrl('admincp.rss.group'))	
			->setHeader(array(
					'drag.js' => 'static_script',
					'<script type="text/javascript">Core_drag.init({table: \'#js_drag_drop\', ajax: \'rss.groupOrdering\'});</script>'		
				)
			)
			->assign(array(
					'aGroups' => Phpfox::getService('rss.group')->get()
				)
			);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_group_index_clean')) ? eval($sPlugin) : false);
	}
}

?>