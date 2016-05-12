<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Share
 * @version 		$Id: frame.html.php 3357 2011-10-25 14:38:44Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="label_flow_menu" style="margin-top:10px;">	
	<ul>
{if Phpfox::getParam('share.enable_social_bookmarking')}	
		<li class="label_flow_menu_active"><a href="#share.bookmark?type={$sBookmarkType}&amp;url={$sBookmarkUrl|urlencode}&amp;title={$sBookmarkTitle}">{phrase var='share.social_bookmarks'}</a></li>
{/if}
		{if Phpfox::isUser()}
		{if Phpfox::isModule('friend')}
			<li{if !Phpfox::getParam('share.enable_social_bookmarking')} class="label_flow_menu_active"{/if}><a href="#share.friend?type={$sBookmarkType}&amp;url={$sBookmarkUrl|urlencode}&amp;title={$sBookmarkTitle}">{phrase var='share.friends'}</a></li>
		{/if}
		{if Phpfox::getUserParam('share.can_send_emails')}
		<li{if !Phpfox::getParam('share.enable_social_bookmarking')} class="last"{/if}><a href="#share.email?type={$sBookmarkType}&amp;url={$sBookmarkUrl|urlencode}&amp;title={$sBookmarkTitle}">{phrase var='share.e_mail'}</a></li>
		{/if}
		{/if}
{if Phpfox::getParam('share.enable_social_bookmarking')}			
		<li class="last"><a href="#share.post?type={$sBookmarkType}&amp;url={$sBookmarkUrl|urlencode}&amp;title={$sBookmarkTitle}">{phrase var='share.post'}</a></li>	
{/if}		
	</ul>
	<br class="clear" />
</div>	
<div class="labelFlowContent" id="js_share_content">
{if Phpfox::getParam('share.enable_social_bookmarking')}
	{module name='share.bookmark' type=$sBookmarkType url=$sBookmarkUrl title=$sBookmarkTitle}
	{else}	
	{module name='share.friend' type=$sBookmarkType url=$sBookmarkUrl title=$sBookmarkTitle}
{/if}
</div>
<script type="text/javascript">$Core.loadStaticFile('{jscript file='switch_legend.js'}');</script>
<script type="text/javascript">$Core.loadStaticFile('{jscript file='switch_menu.js'}');</script>
<script type="text/javascript">$Core.loadInit();</script>