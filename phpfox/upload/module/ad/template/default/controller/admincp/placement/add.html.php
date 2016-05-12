<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 3003 2011-09-04 19:53:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.ad.placement.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.plan_id}" /></div>
{/if}
	<div class="table_header">
		{phrase var='ad.ad_placement_details'}
	</div>
	<div class="table">
		<div class="table_left">
			{phrase var='ad.title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" id="title" value="{value id='title' type='input'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table">
		<div class="table_left">
			{phrase var='ad.placement'}:
		</div>
		<div class="table_right">
			<select name="val[block_id]" id="location">	
				<option value="">Select:</option>
				{foreach from=$aPlanBlocks item=i}
					<option value="{$i}"{value type='select' id='block_id' default=$i}>{phrase var='admincp.block' x=$i}</option>
				{/foreach}
			</select>
			<a href="#?call=ad.sample&amp;width=scan&amp;click=1" class="inlinePopup" title="{phrase var='admincp.sample_layout'}">{phrase var='ad.view_site_layout'}</a>
			<div class="extra_info">
				{phrase var='ad.notice_the_ad_sizes_provided_is_a_recommendation'}
			</div>
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table">
		<div class="table_left">
			Dimensions:
		</div>
		<div class="table_right">
			Width: <input type="text" name="val[d_width]" value="{value id='d_width' type='input'}" size="5" /> Height: <input type="text" name="val[d_height]" value="{value id='d_height' type='input'}" size="5" />
			<div class="extra_info">
				Ad dimensions are in pixels.
			</div>
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table">
		<div class="table_left">
			{phrase var='ad.price'}:
		</div>
		<div class="table_right">
			{module name='core.currency' currency_field_name='val[cost]'}
		</div>
		<div class="clear"></div>
	</div>			
	<div class="table">
		<div class="table_left">
			{phrase var='ad.is_active'}:
		</div>
		<div class="table_right">	
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {phrase var='ad.yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {phrase var='ad.no'}</span>
			</div>
		</div>
		<div class="clear"></div>		
	</div>		
	<div class="table_clear">
		<input type="submit" value="{phrase var='ad.submit'}" class="button" />
	</div>	
</form>