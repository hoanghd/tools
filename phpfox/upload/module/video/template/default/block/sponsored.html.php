<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: sponsored.html.php 3214 2011-09-30 12:05:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="t_center">
	{if $aSponsorVideo.is_stream}
	    {$aSponsorVideo.embed_code}
	{else}
	<script type="text/javascript" src="{param var='core.url_static_script'}player/{param var='core.default_music_player'}/core.js"></script>
	<script type="text/javascript">
		$(function() {left_curly} $Core.player.load({left_curly}id: 'js_video_sponsor_player', auto: true, type: 'video', play: '{$sPath}'{right_curly}); {right_curly});
	</script>
	<div id="js_video_sponsor_player" style="width:298px; height:223px; margin:auto;"></div>
	{/if}
</div>
<div class="p_4">
	<a href="{url link='ad.sponsor' view=$aSponsorVideo.sponsor_id}" class="row_sub_link">{$aSponsorVideo.title|clean|shorten:30|split:20}</a>
	<div class="extra_info_link">
		by {$aSponsorVideo|user}		
	</div>	
</div>