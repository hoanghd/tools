<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: form.html.php 3314 2011-10-18 11:46:11Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
			{if $aSetting.var_type == 'textarea'}
				<textarea cols="60" style="width:90%;" rows="8" name="custom[{$aSetting.field_id}]">{if isset($aSetting.value)}{$aSetting.value|clean}{/if}</textarea>
			{elseif $aSetting.var_type == 'text'}
				<input type="text" name="custom[{$aSetting.field_id}]" value="{if isset($aSetting.value)}{$aSetting.value|clean}{/if}" size="30" maxlength="255"{if PHPFOX_IS_AJAX} style="width:90%;"{/if} />
			{elseif $aSetting.var_type == 'select'}
				<select name="custom[{$aSetting.field_id}]" id="custom_field_{$aSetting.field_id}">
					{if $aSetting.is_required && isset($aSetting.value)}
						{if !$aSetting.value}
							<option value="">{phrase var='custom.select'}:</option>
						{/if}
					{/if}
					{if !$aSetting.is_required}
						<option value="">{phrase var='custom.no_answer'}</option>
					{/if}
					{foreach from=$aSetting.options key=iKey item=sOption}
						<option value="{$iKey}"{if isset($sOption.selected) && $sOption.selected == true} selected="selected"{/if}>{$sOption.value}</option>
					{/foreach}
				</select>
			{elseif $aSetting.var_type == 'multiselect'}
				<select name="custom[{$aSetting.field_id}][]" multiple="multiple" id="custom_field_{$aSetting.field_id}">
					{if $aSetting.is_required}
						{if !$aSetting.value}
							<option value="">{phrase var='custom.select'}:</option>
						{/if}
					{/if}					
					{foreach from=$aSetting.options key=iKey item=aOption}
						<option value="{$iKey}"{if isset($aOption.value) && isset($aOption.selected) && $aOption.selected == true} selected="selected"{/if}>{$aOption.value}</option>
					{/foreach}
				</select>
			{elseif $aSetting.var_type == 'radio'}
				{foreach from=$aSetting.options key=iKey item=aOption}
					<input type="radio" name="custom[{$aSetting.field_id}]" value="{$iKey}" {if isset($aOption.selected) && $aOption.selected == true}checked="checked"{/if}>{$aOption.value} <br />
				{/foreach}
			{elseif $aSetting.var_type == 'checkbox'}
				{foreach from=$aSetting.options key=iKey item=aOption}
					<input type="checkbox" name="custom[{$aSetting.field_id}][]" value="{$iKey}" {if isset($aOption.selected) && $aOption.selected == true}checked="checked"{/if}>{$aOption.value} <br />
				{/foreach}
			{/if}
