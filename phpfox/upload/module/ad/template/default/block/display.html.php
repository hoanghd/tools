<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: display.html.php 3118 2011-09-16 10:51:04Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{plugin call='ad.template_block_display__start'}
{if !PHPFOX_IS_AJAX && !defined('PHPFOX_IS_AD_IFRAME')}
<div class="js_ad_space_parent">
	<div id="js_ad_space_{$aAd.location}" class="t_center ad_space" style="padding:4px 0px 4px 0px;">
{/if}
	{if $aAd.type_id == 1}
		<a href="{url link='ad' id=$aAd.ad_id}" target="_blank">{img file=$aAd.image_path path='ad.url_image' server_id=$aAd.server_id}</a>
	{else}
		{if (defined('PHPFOX_IS_AJAX_PAGE') && PHPFOX_IS_AJAX_PAGE)}		
		<iframe src="{url link='ad.iframe' id={$aAd.location resize=true}" allowtransparency="true" id="js_ad_space_{$aAd.location}_frame" frameborder="0" style="width:100%; height:0px;"></iframe>
		{else}
		{$aAd.html_code}		
		{/if}
	{/if}
{if !PHPFOX_IS_AJAX && !defined('PHPFOX_IS_AD_IFRAME')}
	</div>
</div>
{/if}
{if Phpfox::getParam('ad.ad_ajax_refresh')}
<script type="text/javascript">	
{if $aAd.type_id == 2}
	{literal}
	window.parent.$(function()
	{
		{/literal}			
		if (window.parent.$('#js_ad_space_{$aAd.location}_frame').length > 0)
		{left_curly}
		setTimeout("window.parent.$('#js_ad_space_{$aAd.location}_frame').attr('src', '{url link='ad.iframe' id={$aAd.location}');", ({param var='ad.ad_ajax_refresh_time'} * 60000));
		{right_curly}
		else
		{left_curly}
		setTimeout("window.parent.$('#js_ad_space_{$aAd.location}').html('<iframe allowtransparency=\"true\" id=\"js_ad_space_{$aAd.location}_frame\" frameborder=\"0\" style=\"width:' + window.parent.$('#js_ad_space_{$aAd.location}').width() + 'px; height:' + window.parent.$('#js_ad_space_{$aAd.location}').height() + 'px;\"></iframe>'); window.parent.$('#js_ad_space_{$aAd.location}_frame').attr('src', '{url link='ad.iframe' id={$aAd.location}');", ({param var='ad.ad_ajax_refresh_time'} * 60000));
		{right_curly}		
		{literal}
	});
	{/literal}
{else}
	setTimeout('$.ajaxCall(\'ad.update\', \'block_id={$aAd.location}\', \'GET\');', ({param var='ad.ad_ajax_refresh_time'} * 60000));
{/if}
</script>
{/if}
{plugin call='ad.template_block_display__end'}