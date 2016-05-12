<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: pager.html.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="js_pager_view_more_link">
	{if isset($iPagerNextPageCnt)}
	<a href="{$aPager.nextAjaxUrlPager}" class="global_view_more{if isset($aPager.lastAjaxUrl)} no_ajax_link{/if}" {if isset($aPager.lastAjaxUrl)}onclick="$Core.addUrlPager(this); $.ajaxCall('blog.viewMore', 'page={$iPagerNextPageCnt}&amp;do=' + $Core.getRequests(this, true), 'GET'); return false;"{/if}>{phrase var='core.view_more'}</a>
	{elseif isset($sAjax)}
	<div class="pager_view_more_holder">
		<div class="pager_view_more_link">
			{if !empty($aPager.nextAjaxUrl)}		
			<a href="{$aPager.nextUrl}" class="pager_view_more no_ajax_link" onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParams}', 'GET'); return false;">
				{if !empty($aPager.icon)}		
				{img theme=$aPager.icon class='v_middle'}
				{/if}				
				{if !empty($aPager.phrase)}{$aPager.phrase}{else}{phrase var='core.view_more'}{/if}
			<span>{phrase var='core.displaying_of_total' displaying=$aPager.displaying total=$aPager.totalRows}</span>
			</a>
			{/if}
		</div>			
	</div>
	{else}
	{if isset($aPager) && $aPager.totalPages > 1}
	<div class="pager_outer">
			<ul class="pager">
	{if !isset($bIsMiniPager)}
				<li class="pager_total">{phrase var='core.page_x_of_x' current=$aPager.current total=$aPager.totalPages}</li>
	{/if}
				{if isset($aPager.firstUrl)}<li class="first"><a {if $sAjax}href="{$aPager.firstUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('Loading')); $.ajaxCall('{$sAjax}', 'page={$aPager.firstAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.firstUrl}"{/if}>{phrase var='core.first'}</a></li>{/if}
				{if isset($aPager.prevUrl)}<li><a {if $sAjax}href="{$aPager.prevUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('Loading')); $.ajaxCall('{$sAjax}', 'page={$aPager.prevAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.prevUrl}"{/if}>{phrase var='core.previous'}</a></li>{/if}
			{foreach from=$aPager.urls key=sLink name=pager item=sPage}
				<li {if !isset($aPager.firstUrl) && $phpfox.iteration.pager == 1} class="first"{/if}><a {if $sAjax}href="{$sLink}" onclick="{if $sLink}$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('Loading')); $.ajaxCall('{$sAjax}', 'page={$sPage}{$aPager.sParams}'); $Core.addUrlPager(this);{/if} return false;{else}href="{if $sLink}{$sLink}{else}javascript:void(0);{/if}{/if}"{if $aPager.current == $sPage} class="active"{/if}>{$sPage}</a></li>
			{/foreach}
				{if isset($aPager.nextUrl)}<li><a {if $sAjax}href="{$aPager.nextUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('Loading')); $.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.nextUrl}"{/if}>{phrase var='core.next'}</a></li>{/if}
				{if isset($aPager.lastUrl)}<li><a {if $sAjax}href="{$aPager.lastUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('Loading')); $.ajaxCall('{$sAjax}', 'page={$aPager.lastAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.lastUrl}"{/if}>{phrase var='core.last'}</a></li>{/if}
			</ul>	
			<div class="clear"></div>		
	</div>
	{/if}
	{/if}
</div>