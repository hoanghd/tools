<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Handle AJAX calls for the language module
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: ajax.class.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
class Language_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	/**
	 * Add a phrase using an inline method
	 *
	 */
	public function add()
	{
		Phpfox::getComponent('language.admincp.phrase.add', array('sReturnUrl' => $this->get('return'), 'sVar' => $this->get('phrase'), 'bNoJsValidation' => true), 'controller');
	}
	
	public function select()
	{
		Phpfox::getBlock('language.select');
	}
	
	public function process()
	{
		if (Phpfox::getService('language.process')->useLanguage($this->get('id')))
		{
			Phpfox::addMessage(Phpfox::getPhrase('language.successfully_updated_your_language_preferences'));
			
			$this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('') . '\';');
		}
	}
	
	public function sample()
	{
		Phpfox::getBlock('language.sample');
	}

	public function loadMailPhrases()
	{
		$sLanguage = $this->get('sLanguage');
		Phpfox::getBlock('language.admincp.email', array('sLanguage' => $sLanguage));
		$this->html('#phrasesContainer', $this->getContent(false));
	}
}

?>