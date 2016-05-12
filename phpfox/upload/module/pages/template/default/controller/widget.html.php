<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
		<div id="js_pages_widget_error"></div>
		<form method="post" action="#" onsubmit="$('#js_pages_widget_error').hide(); $Core.processForm('#js_pages_widget_submit_button'); {plugin call='pages.template_controller_widget_ajax_onsubmit'} $(this).ajaxCall('pages.addWidget'); return false;">
			<div><input type="hidden" name="val[page_id]" value="{$iPageId}" /></div>
			{if $bIsEdit}
			<div><input type="hidden" name="widget_id" value="{$aForms.widget_id}" /></div>
			{/if}
			<div class="table">
				<div class="table_left">
					{phrase var='pages.title'}:
				</div>
				<div class="table_right">
					<input type="text" name="val[title]" value="{value type='input' id='title'}" size="30" />				
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="table">
				<div class="table_left">
					{phrase var='pages.is_a_block'}
				</div>
				<div class="table_right">
					<select name="val[is_block]" onchange="if (this.value == '1') {l} $('#js_pages_widget_block').slideUp(); {r} else {l} $('#js_pages_widget_block').slideDown(); {r}">
						<option value="1"{value type='select' id='is_block' default='1'}> {phrase var='pages.yes'}</option>
						<option value="0"{value type='select' id='is_block' default='0' selected='true'}> {phrase var='pages.no'}</option>						
					</select>
				</div>
				<div class="clear"></div>
			</div>				
			
			<div id="js_pages_widget_block"{if $bIsEdit && $aForms.is_block == '1'} style="display:none;"{/if}>
				<div class="table">
					<div class="table_left">
						{phrase var='pages.menu_title'}:
					</div>
					<div class="table_right">
						<input type="text" name="val[menu_title]" value="{value type='input' id='menu_title'}" size="30" />				
					</div>
					<div class="clear"></div>
				</div>			

				<div class="table">
					<div class="table_left">
						{phrase var='pages.url'}:
					</div>
					<div class="table_right">
						<input type="text" name="val[url_title]" value="{value type='input' id='url_title'}" size="30" />				
					</div>
					<div class="clear"></div>
				</div>	
			</div>
			
			<div class="table">
				<div class="table_left">
					{phrase var='pages.content'}:
				</div>
				<div class="table_right">
					{editor id='text'}			
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="table_clear" id="js_pages_widget_submit_button">
				<ul class="table_clear_button">
					<li><input type="submit" value="{phrase var='pages.submit'}" class="button" /></li>
					<li class="table_clear_ajax"></li>
				</ul>		
				<div class="clear"></div>
			</div>			
		</form>