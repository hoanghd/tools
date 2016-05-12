<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Egift_Component_Controller_Admincp_Categories extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCategories = Phpfox::getService('egift')->getCategories();
		$aLanguages = Phpfox::getService('language')->getAll();

		
		/* Adding a category*/
		if ($this->request()->getArray('cat_name'))
		{
			if (Phpfox::getService('egift.process')->addCategory($this->request()->getArray('cat_name')))
			{
				$this->url()->send('admincp.egift.categories',array(), 'Category added successfully');
			}
		}
		/* Editing categories */
		else if($aVal = $this->request()->getArray('val'))
		{
			if (Phpfox::getService('egift.process')->editCategory($this->request()->getArray('val')))
			{
				$this->url()->send('admincp.egift.categories',array(),'Update successfully');
			}
		}
		/* Deleting a category */
		else if ($iId = $this->request()->getInt('delete'))
		{
			if (Phpfox::getService('egift.process')->deleteCategory($iId))
			{
				$this->url()->send('admincp.egift.categories',array(),'Delete successfully');
			}
		}

		$this->template()->assign(array(
			'aCategories' => $aCategories,
			'aLanguages' => $aLanguages,
			'iTotalColumns' => count($aLanguages) + 3
			))
			->setHeader(array(
				'categories.js' => 'module_egift',
				'drag.js' => 'static_script',
				'<script type="text/javascript">Core_drag.init({table: \'#js_drag_drop\', ajax: \'egift.setOrder\'});</script>'
			))
			->setBreadcrumb(Phpfox::getPhrase('egift.module_egift'), $this->url()->makeUrl('admincp.egift'))
			->setBreadcrumb(Phpfox::getPhrase('egift.maange_categories'), null, true);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('egift.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>