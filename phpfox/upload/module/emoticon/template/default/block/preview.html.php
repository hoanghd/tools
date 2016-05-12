<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Emoticon
 * @version 		$Id: preview.html.php 2860 2011-08-20 19:17:52Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aRows key=iKey item=aRow name=emoticons}
<div class="emoticon_preview" onclick="Editor.insert({literal}{{/literal}type: 'emoticon', path: '{$sUrlEmoticon}{$aRow.package_path}/{$aRow.image}', text: '{$aRow.text}', editor_id: '{$sEditorId}'{literal}}{/literal}); return false;">
	<img src="{$sUrlEmoticon}{$aRow.package_path}/{$aRow.image}" alt="{$aRow.title}" style="vertical-align:middle;" /> {$aRow.text}
</div>
{/foreach}
<div class="clear"></div>