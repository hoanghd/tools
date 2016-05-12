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
 * @package  		Module_Captcha
 * @version 		$Id: ajax.class.php 852 2009-08-10 18:05:32Z Raymond_Benc $
 */
class Captcha_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function reload()
	{
		$sUrl = Phpfox::getLib('url')->makeUrl('captcha.image', array('id' => md5(rand(100, 1000))));
		$this->call('$("#' . $this->get('sId') . '").attr("src", "' . $sUrl . '"); $("#' . $this->get('sInput') . '").val(""); $("#' . $this->get('sInput') . '").focus(); $("#js_captcha_process").html("");');
	}
}

?>