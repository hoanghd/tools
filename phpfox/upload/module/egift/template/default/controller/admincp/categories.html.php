<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="table_header">
	{phrase var='egift.add_category'}
</div>
<form action="{url link='admincp.egift.categories'}" method="post">
	{foreach from=$aLanguages item=aLang key=iKey}
	<div class="table">
		<div class="table_left">
				{$aLang.title}:
		</div>
		<div class="table_right">
			<input type="text" name="cat_name[{$aLang.language_id}]">
		</div>
	</div>
	{/foreach}
	
	<div class="table_clear">
		<input type="submit" value="{phrase var='egift.add_category'}" class="button">
	</div>
</form>

<br />

<div class="table_header">
	{phrase var='egift.maange_categories'}
</div>
<form action="{url link='admincp.egift.categories'}" method="post">
	<table id="js_drag_drop">
		<tr>
			<th style="width:20px"></th>
			<th style="width:20px"></th>
			<th style="width:20px"></th>
			{foreach from=$aLanguages key=iLangCount item=aLang}
			<th>{$aLang.title}</th>
			{/foreach}
		</tr>
		{foreach from=$aCategories key=iKey item=aCategory}
		<tr {if is_int($iKey/2)} class="tr"{else}{/if} id="tr_{$iKey}">
			<td class="drag_handle">
				<input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" />
			</td>
			<td>
				<input type="hidden" id="language_var_{$iKey}" value="{$aCategory.phrase}">
				<a href="#" onclick="showEdit({$iKey}); return false;">
					{img theme='misc/page_white_edit.png' style='vertical-align:middle;'}
				</a>
			</td>
			<td>
				<a href="{url link='admincp.egift.categories' delete=$aCategory.category_id}" onclick="return confirm('{phrase var='core.are_you_sure'}');">
					{img theme='misc/delete.png' style='vertical-align:middle;'}
				</a>
			</td>
			{foreach from=$aLanguages item=aLang}				
				<td id="phraseid_{$aLang.language_id}_{$aCategory.category_id}" class="phraseid_{$aLang.language_id}_{$aCategory.category_id} tr_td_{$iKey}">
					{phrase var=$aCategory.phrase language=$aLang.language_id}
				</td>
			{/foreach}
		</tr>
		{foreachelse}
		<tr>
			<td colspan="{$iTotalColumns}" class="t_center"> {phrase var='egift.no_categories_found'} </td>
		</tr>
		{/foreach}
	</table>
	<div class="table_clear">
		<span id="edit_button" style="display:none;">
			<input type="submit" value="{phrase var='egift.edit_categories'}" class="button">
		</span>
	</div>
</form>