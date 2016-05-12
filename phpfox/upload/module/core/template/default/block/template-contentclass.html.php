<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: template-contentclass.html.php 3067 2011-09-12 08:27:57Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !$bUseFullSite}class="{if count($aBlocks3) || count($aBlocks1) || count($aAdBlocks3) || count($aAdBlocks1)} content_float{/if} {if (count($aBlocks1) || count($aAdBlocks1)) && (count($aBlocks3) || count($aAdBlocks3))} content3{/if} {if count($aBlocks1) || count($aBlocks3) || count($aAdBlocks3)} {if isset($aFilterMenus) && (count($aBlocks3) || count($aAdBlocks3)) && !count($aBlocks1)}content3{else}content2{/if}{/if}"{/if}