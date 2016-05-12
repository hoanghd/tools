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
 * @version 		$Id: activity.class.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
class Egift_Component_Block_Display extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aUser = $this->getParam('aUser');
                // if the user has not set its birthday we just dont show it
                if (empty($aUser['birthday']))
                {
                    return false;
                }
		$iDays = Phpfox::getLib('date')->daysToDate($aUser['birthday'], null, false);
		
		
		if (!defined('PHPFOX_IS_USER_PROFILE') || PHPFOX_IS_USER_PROFILE == false 
				|| (!isset($aUser['is_user_birthday']) || $aUser['is_user_birthday'] != true)
				|| ($aUser['user_id'] == Phpfox::getUserId())
				)
		{
			// we should also check if its this user's birthday 
			return false;
		}
		$aCategories = Phpfox::getService('egift')->getCategories(true);
		$aEgifts = Phpfox::getService('egift')->getEgifts();

		foreach ($aEgifts as $sCat => $aCat)
		{
			foreach ($aCat as $iKey => $aGift)
			{
				$bEmpty = true;
				if (is_array($aGift['price']) && !empty($aGift['price']))
				{
					/*get the currency for this user*/
					$aGift['price'] = $aEgifts[$sCat][$iKey]['price'][Phpfox::getService('user')->getCurrency()];
					$aGift['currency_id'] = Phpfox::getService('user')->getCurrency();
				}
				else
				{
					$aGift['price'] = '0.00';
				}
				$aEgifts[$aGift['category_id']][] = $aGift;
			}
			unset($aEgifts[$sCat]);
		}

		$this->template()->assign(array(
				'aCategories' => $aCategories,
				'aEgifts' => $aEgifts
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_block_activity_clean')) ? eval($sPlugin) : false);
	}
}

?>