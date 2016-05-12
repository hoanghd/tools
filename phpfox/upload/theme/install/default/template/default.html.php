<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: default.html.php 906 2009-08-29 13:49:12Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sMessage}
{if isset($sNext)}
 Please hold...
<meta http-equiv="refresh" content="2;url={$sNext}" />
{/if}