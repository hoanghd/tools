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
 * @version 		$Id: import.class.php 2655 2011-06-03 11:40:56Z Miguel_Espinoza $
 */
class Language_Component_Controller_Admincp_Import extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (Phpfox::getParam('core.phpfox_is_hosted'))
		{
			$this->url()->send('admincp');
		}		
		
		$iPage = $this->request()->getInt('page', 0);
		$bImportPhrases = false;
		
		if (($sModulePackage = $this->request()->get('module')))
		{
			$bImportPhrases = true;
			$mReturn = Phpfox::getService('language.phrase.process')->installFromFolder($sModulePackage, $iPage);
			if ($mReturn === false)
			{
				$sPhrase = Phpfox::getPhrase('language.successfully_installed_the_language_package');
				
				Phpfox::getLib('cache')->remove('locale', 'substr');
				
				$this->url()->send('admincp.language', null, $sPhrase);
			}
			else
			{
				$this->template()->setHeader('<meta http-equiv="refresh" content="2;url=' . $this->url()->makeUrl('admincp.language.import', array('module' => $sModulePackage, 'page' => ($iPage + 1))) . '">');
			}
		}
		else
		{
			if (($sPackToInstall = $this->request()->get('install')) && Phpfox::getService('language.process')->installPackFromFolder($sPackToInstall))
			{
				$this->url()->send('admincp.language.import', array('module' => $sPackToInstall));
			}		
		}
		
		$this->template()->setTitle(Phpfox::getPhrase('language.manage_language_packages'))
			->setBreadCrumb(Phpfox::getPhrase('language.manage_language_packages'))
			->assign(array(
					'aNewLanguages' => Phpfox::getService('language')->getForInstall(),
					'bImportPhrases' => $bImportPhrases
				)
			);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('language.component_controller_admincp_import_clean')) ? eval($sPlugin) : false);
	}
}

?>
