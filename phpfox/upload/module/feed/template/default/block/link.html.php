			{if isset($sFeedType) &&  $sFeedType == 'view'}
			<div class="feed_share_custom">	
				{if Phpfox::isModule('share') && Phpfox::getParam('share.share_facebook_like')}
				<div class="feed_share_custom_block">
					<iframe src="http://www.facebook.com/plugins/like.php?app_id=156226084453194&amp;href={$aFeed.feed_link}&amp;send=false&amp;layout=button_count&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;width=90&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>					
				</div>
				{/if}
				{if Phpfox::isModule('share') && Phpfox::getParam('share.share_twitter_link')}
				<div class="feed_share_custom_block"><a href="http://twitter.com/share" class="twitter-share-button" data-url="{$aFeed.feed_link}" data-count="horizontal" data-via="phpFox">{phrase var='feed.tweet'}</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
				{/if}
				{if Phpfox::isModule('share') && Phpfox::getParam('share.share_google_plus_one')}
				<div class="feed_share_custom_block">
					<g:plusone href="{$aFeed.feed_link}" size="medium"></g:plusone>
					{literal}
					<script type="text/javascript">
					  (function() {
						var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
						po.src = 'https://apis.google.com/js/plusone.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
					{/literal}
				</div>
				{/if}
				<div class="clear"></div>
			</div>
			{/if}
			
			<ul>
				{if !isset($aFeed.feed_mini)}
				{if !empty($aFeed.feed_icon)}
				<li><img src="{$aFeed.feed_icon}" alt="" /></li>
				{/if}
				{if isset($aFeed.time_stamp)}
				<li class="feed_entry_time_stamp">				
					<a href="{$aFeed.feed_link}" class="feed_permalink">{$aFeed.time_stamp|convert_time:'feed.feed_display_time_stamp'}</a>{if !empty($aFeed.app_link)} via {$aFeed.app_link}{/if}
				</li>
				<li><span>&middot;</span></li>
				{/if}
				
				{if $aFeed.privacy > 0 && $aFeed.user_id == Phpfox::getUserId()}
				<li><div class="js_hover_title">{img theme='layout/privacy_icon.png' alt=$aFeed.privacy}<span class="js_hover_info">{$aFeed.privacy|privacy_phrase}</span></div></li>	
				<li><span>&middot;</span></li>
				{/if}
				{/if}
					
				{if Phpfox::isModule('like') && isset($aFeed.like_type_id)}
				{if isset($aFeed.like_item_id)}
				{module name='like.link' like_type_id=$aFeed.like_type_id like_item_id=$aFeed.like_item_id like_is_liked=$aFeed.feed_is_liked}
				{else}
				{module name='like.link' like_type_id=$aFeed.like_type_id like_item_id=$aFeed.item_id like_is_liked=$aFeed.feed_is_liked}
				{/if}				
				<li><span>&middot;</span></li>
				{/if}
				
				{if Phpfox::isModule('comment') && (isset($aFeed.comment_type_id) && $aFeed.can_post_comment) || (!isset($aFeed.comment_type_id) && isset($aFeed.total_comment))}				
				<li>
					<a href="{$aFeed.feed_link}add-comment/" class="{if (isset($sFeedType) && $sFeedType == 'mini') || (!isset($aFeed.comment_type_id) && isset($aFeed.total_comment))}{else}js_feed_entry_add_comment no_ajax_link{/if}">{phrase var='feed.comment'}</a>
				</li>				
				{if (Phpfox::isModule('share') && !isset($aFeed.no_share)) || (isset($aFeed.report_module) && isset($aFeed.force_report))}
				<li><span>&middot;</span></li>
				{/if}
				{/if}				
				{if Phpfox::isModule('share') && !isset($aFeed.no_share)}					
				{module name='share.link' type='feed' display='menu' url=$aFeed.feed_link title=$aFeed.feed_title}				
				{/if}
				{if isset($aFeed.report_module) && isset($aFeed.force_report)}				
				<li><span>&middot;</span></li>
				<li><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type={$aFeed.report_module}&amp;id={$aFeed.item_id}" class="inlinePopup activity_feed_report" title="{$aFeed.report_phrase}">{phrase var='feed.report'}</a></li>				
				{/if}				
				{plugin call='feed.template_block_entry_2'}				
			</ul>
			<div class="clear"></div>		