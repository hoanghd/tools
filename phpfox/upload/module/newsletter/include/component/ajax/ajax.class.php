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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 1168 2009-10-09 14:20:37Z Raymond_Benc $
 */
class Newsletter_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function showPlain()
	{
		$sText = $this->get('sText');
		$aToStrip = array('[b]', '[i]', '[/b]', '[/i]', '[u]', '[/u]');
		$this->call('$("#txtPlain").val("'.str_replace($aToStrip, '', strip_tags($sText)).'");');
	}

	public function deleteNewsletter()
	{
		$iId = $this->get('iId');
		if (!Phpfox::getUserParam('newsletter.can_create_newsletter'))
		{
			$this->alert(Phpfox::getPhrase('newsletter.you_are_not_allowed_to_delete_newsletters'));
			return false;
		}
		if (Phpfox::getService('newsletter.process')->delete($iId))
		{
			$this->call('$("#js_newsletter_'.$iId.'").remove();');
		}
		
	}
}

?>