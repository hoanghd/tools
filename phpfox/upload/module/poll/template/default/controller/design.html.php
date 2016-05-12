<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Poll
 * @version 		$Id: design.html.php 2501 2011-04-04 20:13:13Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{template file='poll.block.entry'}

	{if isset($bDesign) && $bDesign}
	<h3>{phrase var='poll.poll_designer'}</h3>
		<form action="{url link='poll.design' id=$aPoll.poll_id}" method="post">		
		<div><input type="hidden" name="val[js_poll_background]" id="js_colorpicker_drop_1" value="{$aPoll.background}" /></div>
		<div><input type="hidden" name="val[js_poll_percentage]" id="js_colorpicker_drop_2" value="{$aPoll.percentage}" /></div>
		<div><input type="hidden" name="val[js_poll_border]" id="js_colorpicker_drop_3" value="{$aPoll.border}" /></div>
		<div><input type="hidden" name="val[poll_id]" id="iPoll" value="{$aPoll.poll_id}" /></div>
		<div>
				<div class="table">
					<div class="table_left">
						{phrase var='poll.background'}:
					</div>
					<div class="table_right label_hover">
						<a href="#?var=backgroundColor&amp;class=js_sample_outer&amp;id=js_colorpicker_drop_1" {if isset($aPoll.background)} style="background-color:#{$aPoll.background};"{/if} class="colorpicker_select">{phrase var='poll.select_color'}</a>
					</div>
					<div class="clear"></div>
				</div>	
				
				<div class="table">
					<div class="table_left">
						{phrase var='poll.percent'}:
						</div>
					<div class="table_right label_hover">
						<a href="#?var=backgroundColor&amp;class=js_sample_percent&amp;id=js_colorpicker_drop_2" {if isset($aPoll.percentage)} style="background-color:#{$aPoll.percentage};"{/if} class="colorpicker_select" {if isset($aPoll.percentage)}style="#{$aPoll.percentage}"{/if}>{phrase var='poll.select_color'}</a>
					</div>
					<div class="clear"></div>
				</div>		
				
				<div class="table">
					<div class="table_left">
						{phrase var='poll.border'}:
					</div>
					<div class="table_right label_hover">
						<a href="#?var=borderColor&amp;class=js_sample_outer&amp;id=js_colorpicker_drop_3" {if isset($aPoll.border)} style="background-color:#{$aPoll.border};"{/if} class="colorpicker_select" {if isset($aPoll.border)}style="#{$aPoll.border}"{/if}>{phrase var='poll.select_color'}</a>
					</div>
					<div class="clear"></div>
				</div>		
				
				<div class="table_clear">
					<ul class="table_clear_button">
						<li><input type="submit" value="{phrase var='poll.save'}" class="button" /></li>
						<li><input type="button" class="button button_off" onclick="window.location.href='{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}';" value="Cancel" /></li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</form>
	{/if}	