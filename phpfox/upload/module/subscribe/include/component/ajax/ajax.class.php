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
 * @package  		Module_Subscribe
 * @version 		$Id: ajax.class.php 1245 2009-11-02 16:10:29Z Raymond_Benc $
 */
class Subscribe_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function message()
	{
		Phpfox::getBlock('subscribe.message');
	}
	
	public function upgrade()
	{
		Phpfox::getBlock('subscribe.upgrade');
	}
	
	public function listUpgrades()
	{
		Phpfox::getBlock('subscribe.list');
		
		$this->html('#js_core_dashboard', $this->getContent(false));
	}
	
	public function listUpgradesOnSignup()
	{
		Phpfox::getBlock('subscribe.list', array('on_signup' => true));
	}
	
	public function ordering()
	{		
		if (Phpfox::getService('subscribe.process')->updateOrder($this->get('val')))
		{
			
		}
	}
	
	public function updateActivity()
	{		
		if (Phpfox::getService('subscribe.process')->updateActivity($this->get('package_id'), $this->get('active')))
		{
			
		}
	}	
	
	public function deleteImage()
	{
		Phpfox::getService('subscribe.process')->deleteImage($this->get('package_id'));
	}
	
	public function updatePurchase()
	{
		if (Phpfox::getService('subscribe.purchase.process')->updatePurchase($this->get('purchase_id'), $this->get('status')))
		{
			
		}	
	}
}

?>