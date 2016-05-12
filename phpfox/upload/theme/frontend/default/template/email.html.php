<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		PhpFox
 * @version 		$Id: email.html.php 1284 2009-11-27 23:44:31Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bHtml}	
{if $bMessageHeader}
	{if isset($sName)}
	{phrase var='core.hello_name' name=$sName}
	{else}
	{phrase var='core.hello'}
	{/if},
	<br />
	<br />
{/if}
	{$sMessage}
	<br />
	<br />
	{$sEmailSig}	
{else}	
{if $bMessageHeader}
	{if isset($sName)}
	{phrase var='core.hello_name' name=$sName}
	{else}
	{phrase var='core.hello'}
	{/if},
{/if}	
	{$sMessage}

	{$sEmailSig}	
{/if}