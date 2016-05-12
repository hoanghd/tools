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
 * @package  		Module_Admincp
 * @version 		$Id: index.class.php 1284 2009-11-27 23:44:31Z Raymond_Benc $
 */
class Admincp_Component_Controller_Product_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Phpfox::getService('admincp.product.process')->updateActive($aVals))
			{
				$this->url()->send('admincp.product', null, Phpfox::getPhrase('admincp.product_s_updated'));
			}			
		}				
		
		if ($sDeleteProduct = $this->request()->get('delete'))
		{
			if (Phpfox::getService('admincp.product.process')->delete($sDeleteProduct))
			{
				$this->url()->send('admincp.product', null, Phpfox::getPhrase('admincp.product_successfully_deleted'));
			}
		}
		
		if (($sUpgrade = $this->request()->get('upgrade')))
		{
			if (Phpfox::getService('admincp.product.process')->upgrade($sUpgrade))
			{
				$this->url()->send('admincp.product', null, Phpfox::getPhrase('admincp.product_successfully_upgraded'));				
			}
		}

		$aProducts = Phpfox::getService('admincp.product')->get(false);
		foreach ($aProducts as $iKey => $aProduct)
		{
			if ($aProduct['product_id'] == 'phpfox' || $aProduct['product_id'] == 'phpfox_installer')
			{
				unset($aProducts[$iKey]);
			}
		}

		$this->template()->setTitle(Phpfox::getPhrase('admincp.manage_products'))
			->setBreadcrumb(Phpfox::getPhrase('admincp.manage_products'))
			->assign(array(
					'aProducts' => $aProducts
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_product_index_clean')) ? eval($sPlugin) : false);
	}
}

?>