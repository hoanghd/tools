<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: completed.html.php 2825 2011-08-09 20:14:13Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsUpgrade}
Successfully upgraded to phpFox version {$sUpgradeVersion}.
{else}
Successfully installed phpFox {$sUpgradeVersion}.
{/if}
<ul class="action">
	<li><a href="../index.php">View Your Site</a></li>
</ul>