<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.report.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.report_id}" /></div>
{/if}
	<div class="table_header">
		{phrase var='report.category_details'}
	</div>
	{module name='admincp.product.form'}
	{module name='admincp.module.form'}
	<div class="table">
		<div class="table_left">
		{required}{phrase var='report.category_name'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[message]" value="{value type='input' id='message'}" size="40" maxlength="100" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{if $bIsEdit}{phrase var='report.update'}{else}{phrase var='report.add'}{/if}" class="button" />
	</div>
</form>