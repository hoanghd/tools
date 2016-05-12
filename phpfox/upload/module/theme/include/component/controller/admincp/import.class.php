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
 * @version 		$Id: import.class.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
class Theme_Component_Controller_Admincp_Import extends Phpfox_Component
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
		
		$oArchiveImport = Phpfox::getLib('archive.import')->set(array('zip'));
		$bOverwrite = ($this->request()->getInt('overwrite') ? true : false);
		
		if (($sThemeToInstall = $this->request()->get('install')) && Phpfox::getService('theme.process')->installThemeFromFolder($sThemeToInstall))
		{
			$this->url()->send('admincp.theme.import', null, Phpfox::getPhrase('theme.theme_successfully_imported'));
		}
		
		if (isset($_FILES['import']) && ($aFile = $_FILES['import']) && !empty($aFile['name']))
		{
            if (preg_match('/^phpfox-theme-(.*?)\.zip$/i', $aFile['name'], $aMatches))
            {
				if ($sDirectory = $oArchiveImport->process($aFile))
				{
					$sFolderName = $aMatches[1];
					if (preg_match('/^(.*)-(.*?)$/i', $aMatches[1]))
					{
						$aParts = explode('-', $aMatches[1]);
						$sFolderName = $aParts[0];
					}
					
					$this->url()->send('admincp.theme.import', array('install' => $sFolderName));
				}
            }
            else 
            {
            	Phpfox_Error::set(Phpfox::getPhrase('theme.not_a_valid_theme_to_import'));
            }
		}
		
		$this->template()->setTitle(Phpfox::getPhrase('theme.import_themes'))
		->setBreadcrumb(Phpfox::getPhrase('theme.themes'), $this->url()->makeUrl('admincp.theme'))
			->setBreadcrumb(Phpfox::getPhrase('theme.import_themes'), null, true)
			->assign(array(
					'aNewThemes' => Phpfox::getService('theme')->getNewThemes()
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('theme.component_controller_admincp_import_clean')) ? eval($sPlugin) : false);
	}
}

?>