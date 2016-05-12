<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: template-logo.html.php 2818 2011-08-09 12:01:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
						{if !empty($sStyleLogo)}
						<a href="{url link=''}" id="logo"><img src="{$sStyleLogo}" class="v_middle" /></a>
						{else}
						<a href="{url link=''}" id="logo">{param var='core.site_title'}</a>
						{/if}