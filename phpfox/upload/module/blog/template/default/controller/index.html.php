<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: index.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aItems)}
<div class="extra_info">
	{phrase var='blog.no_blogs_found'}
</div>
{else}
{foreach from=$aItems name=blog item=aItem}
	{template file='blog.block.entry'}
{/foreach}
{if Phpfox::getUserParam('blog.can_approve_blogs') || Phpfox::getUserParam('blog.delete_user_blog')}
{moderation}
{/if}
{unset var=$aItems}
{pager}
{/if}