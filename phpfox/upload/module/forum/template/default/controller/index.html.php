<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 * @version 		$Id: index.html.php 2766 2011-07-29 11:58:31Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aForums)}
<div class="extra_info">
	{phrase var='forum.no_forums_have_been_created'}
	{if Phpfox::getUserParam('forum.can_add_new_forum')}
	<ul class="action">
		<li><a href="{url link='admincp.forum.add'}">{phrase var='forum.create_a_new_forum'}</a></li>
	</ul>
	{/if}
</div>
{else}
{template file='forum.block.entry'}
{/if}