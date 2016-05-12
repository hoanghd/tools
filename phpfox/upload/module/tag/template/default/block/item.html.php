{if isset($sTags) && !empty($sTags)}
{if $bIsInline}
 <span id="js_quick_edit_tag{$iItemId}">{if $bDontCleanTags}{$sTags}{else}{$sTags|clean}{/if}</span>
{else}
<div class="item_tag_holder">
	<span class="item_tag">
		{phrase var='tag.topics'}:
	</span>
	<span id="js_quick_edit_tag{$iItemId}">{foreach from=$aTags item=aTag name=tag}{if $phpfox.iteration.tag != 1}, {/if}<a href="{$aTag.tag_url}">{$aTag.tag_text|clean}</a>{/foreach}</span>
	{if (Phpfox::getUserId() == $iUserId && Phpfox::getUserParam('tag.edit_own_tags')) || Phpfox::getUserParam('tag.edit_user_tags')}
	<div id="js_quick_edit_tag_content{$iItemId}" style="display:none;">
		{foreach from=$aTags item=aTag name=tag}{if $phpfox.iteration.tag != 1}, {/if}{$aTag.tag_text|clean}{/foreach}
	</div>
	{/if}
</div>
{/if}
{/if}
{unset var=$sTags}