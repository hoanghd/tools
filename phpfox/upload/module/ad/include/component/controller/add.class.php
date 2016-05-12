<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 3318 2011-10-19 12:36:19Z Miguel_Espinoza $
 */
class Ad_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('ad.can_create_ad_campaigns', true);
		
		$bIsEdit = false;
		$bCompleted = ($this->request()->get('req3') == 'completed' ? true : false);		
		if (($iId = $this->request()->getInt('id')) && ($aAd = Phpfox::getService('ad')->getForEdit($iId)))
		{
			if ($aAd['user_id'] != Phpfox::getUserId())
			{
				return Phpfox_Error::display(Phpfox::getPhrase('ad.unable_to_edit_purchase_this_ad'));
			}
			
			if (!$bCompleted)
			{
				$bIsEdit = true;
			}
			
			$this->template()->assign(array(
					'aForms' => $aAd
				)
			);
		}

		if ($bIsEdit)
		{
			$aValidation = array();
		}
		else 
		{
			$aValidation = array(
				'url_link' => array(
					'def' => 'url'
				)
			);				
		}
		
		$aValidation['name'] = Phpfox::getPhrase('ad.provide_a_campaign_name');
		$aValidation['total_view'] = Phpfox::getPhrase('ad.define_how_many_impressions_for_this_ad');
		
		$oValidator = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));			
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($oValidator->isValid($aVals))
			{
				$aPlan = Phpfox::getService('ad')->getPlan($aVals['location'], true);
				$aVals = array_merge($aPlan, $aVals);
				if ($bIsEdit)
				{
					if (($iId = Phpfox::getService('ad.process')->updateCustom($aAd['ad_id'], $aVals)))
					{
						$this->url()->send('ad.manage', null, Phpfox::getPhrase('ad.ad_successfully_updated'));
					}					
				}
				else 
				{
					if (($iId = Phpfox::getService('ad.process')->addCustom($aVals)))
					{
						$this->url()->send('ad.add.completed', array('id' => $iId));
					}
				}
			}
		}
		
		$aAge = array();
		$iAgeEnd = date('Y')-Phpfox::getParam('user.date_of_birth_start');
		$iAgeStart = date('Y')-Phpfox::getParam('user.date_of_birth_end');
		for ($iAgeStart; $iAgeStart <= $iAgeEnd; $iAgeStart++)
		{
			$aAge[$iAgeStart] = $iAgeStart;
		}	
		
		$iPlacementCount = count((array) Phpfox::getService('ad')->getPlacements());
		
		if (!$bCompleted && !$bIsEdit)
		{
			if ($iPlacementCount)
			{
				$this->template()->setHeader(array(
						'add.js' => 'module_ad'
					)
				);
			}		
		}
		else 
		{			
			$aPlan = Phpfox::getService('ad')->getPlan($aAd['location'], true);

			if (!isset($aPlan['plan_id']))
			{
				return Phpfox_Error::display(Phpfox::getPhrase('ad.not_a_valid_ad_plan'));	
			}
			// is it free?
			$aCosts = unserialize($aPlan['cost']);
			$bIsFree = true;
			foreach ($aCosts as $sCurrency => $fCost)
			{
				if ($fCost > 0)
				{
					$bIsFree = false;
					break;
				}
			}
			$this->template()->assign(array('bIsFree' => $bIsFree));
			$this->setParam('gateway_data', array(
					'item_number' => 'ad|' . $aAd['ad_id'],
					'currency_code' => $aPlan['default_currency_id'],
					'amount' => (($aPlan['default_cost'] * $aAd['total_view']) / 1000),
					'item_name' => $aPlan['title'],
					'return' => $this->url()->makeUrl('ad.manage', array('view' => 'pending', 'payment' => 'done')),
					'recurring' => '',
					'recurring_cost' => '',
					'alternative_cost' => '',
					'alternative_recurring_cost' => ''
				)
			);			
		}
		
		$this->template()->setTitle(($bIsEdit ? Phpfox::getPhrase('ad.updating_an_ad') : Phpfox::getPhrase('ad.creating_an_ad')))	
			->setBreadcrumb(Phpfox::getPhrase('ad.advertise'), $this->url()->makeUrl('ad'))
			->setBreadcrumb(($bIsEdit ? Phpfox::getPhrase('ad.updating_an_ad') : Phpfox::getPhrase('ad.creating_an_ad')), $this->url()->makeUrl('ad.add'), true)
			->setPhrase(array(
					'ad.select_an_ad_placement'
				)
			)
			->assign(array(
					'aAge' => $aAge,
					'bIsEdit' => $bIsEdit,
					'sCreateJs' => $oValidator->createJS(),
					'sGetJsForm' => $oValidator->getJsForm(),
					'bCompleted' => $bCompleted,
					'bIsEdit' => $bIsEdit,
					'iPlacementCount' => $iPlacementCount
				)
			)
			->setPhrase(array(
					'core.you_have_limit_character_s_left'
				)
			)
			->setHeader('cache', array(					
					'jquery/plugin/jquery.limitTextarea.js' => 'static_script',
					'colorpicker.js' => 'static_script'
				)
			)
			->setFullSite();		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ad.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}

?>