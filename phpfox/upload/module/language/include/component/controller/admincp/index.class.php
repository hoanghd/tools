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
 * @package  		Module_Language
 * @version 		$Id: index.class.php 3228 2011-10-06 14:00:31Z Raymond_Benc $
 */
class Language_Component_Controller_Admincp_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		Phpfox::getUserParam('language.can_manage_lang_packs', true);
		
		if (($sExportId = $this->request()->get('export')))
		{
			$oArchiveExport = Phpfox::getLib('archive.export')->set(array('zip'));
			
			if (($aData = Phpfox::getService('language')->exportForDownload($sExportId, ($this->request()->get('custom') ? true : false))))
			{
				$oArchiveExport->download('phpfox-language-' . $aData['name'] . '', 'zip', $aData['folder']);
			}
		}
		
		$aLanguages = Phpfox::getService('language')->getForAdminCp();
		
		if ($iDefault = $this->request()->get('default'))
		{
			if (Phpfox::getService('language.process')->setDefault($iDefault))
			{
				$this->url()->send('admincp', 'language', Phpfox::getPhrase('language.default_language_package_reset'));
			}
		}
		
		$this->template()->assign(array(
			'aLanguages' => $aLanguages
		))->setTitle(Phpfox::getPhrase('language.manage_language_packages'))
			->setBreadCrumb(Phpfox::getPhrase('language.manage_language_packages'));
	}
}

?>