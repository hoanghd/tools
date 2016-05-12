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
 * @version 		$Id: my.class.php 2592 2011-05-05 18:51:50Z Raymond_Benc $
 */
class Marketplace_Component_Block_My extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aListing = $this->getParam('aListing');
		
		list($iCnt, $aListings) = Phpfox::getService('marketplace')->getUserListings($aListing['listing_id'], $aListing['user_id']);
		
		if (!$iCnt)
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => 'More From Seller',
				'aMyListings' => $aListings
			)
		);
		
		if ($iCnt > Phpfox::getParam('marketplace.total_listing_more_from'))
		{
			$this->template()->assign(array(
					'aFooter' => array(
						Phpfox::getPhrase('marketplace.view_more') => $this->url()->makeUrl($aListing['user_name'], array('marketplace'))
					)
				)
			);
		}
		
		return 'block';				
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_block_my_clean')) ? eval($sPlugin) : false);
	}
}

?>