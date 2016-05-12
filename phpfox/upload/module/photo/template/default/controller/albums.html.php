<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: albums.html.php 2635 2011-06-01 18:58:25Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aAlbums)}
{foreach from=$aAlbums item=aAlbum name=albums}
	{template file='photo.block.album-entry'}
{/foreach}
<div class="clear"></div>
{pager}
{else}
<div class="extra_info">
	No albums found here.
</div>
{/if}